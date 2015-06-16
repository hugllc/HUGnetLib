<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/SystemTableBase.php";
/** This is our webapi interface */
require_once dirname(__FILE__)."/../interfaces/WebAPI.php";
/** This is our webapi interface */
require_once dirname(__FILE__)."/../interfaces/WebAPI2.php";
/** This is our system interface */
require_once dirname(__FILE__)."/../interfaces/SystemInterface.php";

/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DataCollector extends \HUGnet\base\SystemTableBase
    implements \HUGnet\interfaces\WebAPI, \HUGnet\interfaces\SystemInterface,
               \HUGnet\interfaces\WebAPI2
{
    /** @var int The database table class to use */
    protected $tableClass = "Datacollectors";
    /** This is our url */
    protected $url = "/datacollector";
    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $table   The table to use
    *
    * @return null
    */
    public static function &factory(
        &$system, $data=null, $table="Datacollectors"
    ) {
        $object = parent::factory($system, $data, $table);
        return $object;
    }
    /**
    * Returns the table as an array
    *
    * @param bool $default Whether or not to include the default values
    *
    * @return array
    */
    public function toArray($default = false)
    {
        $return = (array)parent::toArray($default);
        if (is_string($return["Runtime"])) {
            $return["Runtime"] = json_decode($return["Runtime"]);
        }
        if ($default) {
            // This is checks for it being 1 hour late
            $late = $this->system()->now() - 3600;
            if ($late > $return["LastContact"]) {
                $return["LateCheckin"] = true;
            } else {
                $return["LateCheckin"] = false;
            }
        }
        return $return;
    }
    /**
    * Gets the config and saves it
    *
    * @return string The left over string
    */
    public function checkin($url = null)
    {
        if (empty($url)) {
            $master = $this->system()->get("master");
            $url = $master["url"];
        }
        $url .= $this->url()."/checkin";
        return $this->httpMethod("PUT", "", $url);
    }
    /**
    * returns a history object for this device
    *
    * @param object $args  The argument object
    * @param array  $extra Extra data from the
    *
    * @return string
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function webAPI($args, $extra)
    {
        $action = trim(strtolower($args->get("action")));
        $ret = null;
        if ($action === "run") {
            $ret = $this->_run();
        } else if ($action === "status") {
            $ret = $this->_status();
        } else if ($action === "checkin") {
            $ret = $this->_checkin($args);
        }
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @param object $api   The api object
    * @param array  $extra Extra data from the
    *
    * @return string
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function webAPI2($api, $extra)
    {
        $method = trim(strtoupper($api->args()->get("method")));
        $object = trim(strtolower($api->args()->get("subobject")));
        $ret = null;
        if ($object === "run") {
            if ($method == "GET") {
                $ret = $this->_status();
            } else if ($method == "POST") {
                $ret = $this->_run();
            }
        } else if ($object === "checkin") {
            if (($method == "POST") || ($method == "PUT")) {
                $ret = $this->_checkin($api->args());
                if ($ret) {
                    $api->response(202);
                } else {
                    $api->response(400);
                    $api->pdoerror($this->lastError, \HUGnet\ui\WebAPI2::SAVE_FAILED);
                }
            }
        } else {
            $api->response(401);
            $api->error(\HUGnet\ui\WebAPI2::NOT_IMPLEMENTED);
        }
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @return string
    */
    private function _run()
    {
        $this->load($this->system()->get("uuid"));
        $config = json_decode($this->get("Runtime"), true);
        if ($config["gather"]) {
            $config["gather"] = false;
            $config["gatherpoll"] = false;
            $config["gatherconfig"] = false;
        } else {
            $config["gather"] = true;
            $config["gatherpoll"] = true;
            $config["gatherconfig"] = true;
        }
        $this->set("Runtime", json_encode($config));
        $this->store();
        $this->system()->network()->send(
            array("To" => '000000', "Command" => "5B"),
            null,
            array(
                "tries" => 1,
                "find" => false,
                "block" => false,
            )
        );
        return $this->_status();
    }
    /**
    * returns a history object for this device
    *
    * @return string
    */
    private function _status()
    {
        $this->load($this->system()->get("uuid"));
        $config = json_decode($this->get("Runtime"), true);
        if ($config["gather"]) {
            $ret = 1;
        } else {
            $ret = 0;
        }
        return $ret;

    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _checkin($args)
    {
        $this->load($args->get("id"));
        $ret = $this->system()->table("DatacollectorCheckin")->checkin(
            $this->id()
        );
        if ($ret) {
            $this->set("LastContact", $this->system()->now());
            $this->store();
        }
        return (int)$ret;
    }
}


?>
