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
/** This is our system interface */
require_once dirname(__FILE__)."/../interfaces/SystemInterface.php";
/** This is our base class */
require_once dirname(__FILE__)."/../interfaces/WebAPI.php";

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
class Annotation extends \HUGnet\base\SystemTableBase
    implements \HUGnet\interfaces\WebAPI, \HUGnet\interfaces\SystemInterface
{
    /** @var int The database table class to use */
    protected $tableClass = "Annotations";
    /** This is the test we are attached to */
    private $_test = null;
    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $table   The table to use
    *
    * @return null
    */
    public static function &factory(&$system, $data=null, $table="Annotations")
    {
        $object = parent::factory($system, $data, $table);
        return $object;
    }
    /**
    * This function creates the device object
    *
    * @return null
    */
    public function &test()
    {
        if (!is_object($this->_test)) {
            $test = $this->get("test");
            $this->_test = $this->system()->test($test);
        }
        return $this->_test;
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
        if ($action === "list") {
            $ret = $this->_getAnnotation($args);
        } else if ($action === "repl") {
            $ret = $this->_repl($args);
        } else if ($action === "delete") {
            $ret = $this->_deleteAnnotation($args);
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
    private function _repl($args)
    {
        $id = (int)$args->get("id");
        $data = (array)$args->get("data");
        $extraData = array();
        $start = (isset($data["start"])) ? (int)$data["start"] : 0;
        $res = $this->table()->selectInto(
            array(
                $this->table()->sqlId => array(
                    '$gte' => $start,
                )
            )
        );
        $ret = array();
        while ($res) {
            $ret[] = $this->table()->toArray(true);
            $res = $this->table()->nextInto();
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
    private function _getAnnotation($args)
    {
        $id = (int)$args->get("id");
        if ($id != 0) {
            $this->load($id);
            $ret = "regen";
        } else {
            $data = (array)$args->get("data");
            $extraData = array();
            $data["since"] = (isset($data["since"])) ? (int)$data["since"] : 0;
            $data["until"] = (isset($data["until"])) ? (int)$data["until"] : 0;
            $res = $this->table()->getPeriod(
                $data["since"],
                $data["until"],
                $data["test"],
                $data["type"]
            );
            $ret = array();
            while ($res) {
                $ret[] = $this->table()->toArray(true);
                $res = $this->table()->nextInto();
            }
        }
        return $ret;
    }
    /**
    * Deletes an annotation
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _deleteAnnotation($args)
    {
        $id = (int)$args->get("id");
        return $this->table()->deleteRow();
    }
    
}


?>
