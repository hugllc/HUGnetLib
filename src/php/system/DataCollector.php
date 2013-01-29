<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DataCollector extends \HUGnet\base\SystemTableBase
{
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
    * Gets the config and saves it
    *
    * @return string The left over string
    */
    public function checkin()
    {
        $master = $this->system()->get("master");
        return \HUGnet\Util::postData(
            $master["url"],
            array(
                "uuid"   => urlencode($this->system()->get("uuid")),
                "id"     => urlencode($this->system()->get("uuid")),
                "action" => "put",
                "task"   => "datacollector",
                "data"   => $this->toArray(true),
            )
        );
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
}


?>
