<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Test extends SystemTableBase
{
    /**
    * This function creates the system.
    *
    * @param mixed  $system (object)The system object to use
    * @param mixed  $data   (int)The id of the item, (array) data info array
    * @param string $table  The table to use
    *
    * @return null
    */
    public static function &factory($system, $data=null, $table="TestTable")
    {
        $object = &parent::factory($system, $data, $table);
        return $object;
    }
    /**
    * Gets all of the device ids that are needed
    *
    * @return array of device keys
    */
    private function _getIDs()
    {
        $fields = json_decode($this->get("fields"), true);
        $ret = array();
        foreach ((array)$fields as $key => $field) {
            $dev = hexdec($field["device"]);
            if ($dev !== 0) {
                $ret[$dev] = sprintf("%06X", $dev);
            }
        }
        return $ret;
    }
    /**
    * This function creates the system.
    *
    * @return array The
    */
    public function poll()
    {
        $devs = $this->_getIDs();

        $time = time();
        $id = $this->get("id");
        $hist = array(
            0 => $this->getField(),
            "Date" => $time,
            "TestID" => $id,
            "id" => $id
        );
        foreach ($devs as $dev => $devID) {
            $hist[$devID] = &$this->system()->device($dev)->action()->poll(
                $id, $time
            );
        }
        $history = &$this->historyFactory($hist);
        $history->insertRow();
        return $history;
    }
    /**
    * returns a history object for this device
    *
    * @param array $data    The data to build the history record with.
    * @param bool  $history History if true, average if false
    *
    * @return string
    */
    public function &historyFactory($data, $history = true)
    {
        $class = \HUGnet\Util::findClass(
            "ETESTHistoryTable", "plugins/historyTable", true
        );
        $obj = new $class($data);
        $obj->device = &$this;
        return $obj;
    }
    /**
    * Gets one of the fields
    *
    * @param string $field The field to get
    *
    * @return The value of the field
    */
    public function &getField($field = null)
    {
        $array = json_decode($this->table()->get("fields"), true);
        if (is_null($field)) {
            return $array;
        }
        return $array[$field];
    }
    /**
    * Sets one of the fields
    *
    * This will set all of the fields if the first argument is an array
    *
    * @param string $field The field to set
    * @param mixed  $value The value to set the field to
    *
    * @return null
    */
    public function &setField($field, $value = null)
    {
        if (is_array($field)) {
            $params = $field;
        } else {
            $params = json_decode($this->table()->get("fields"), true);
            $params[$field] = $value;
        }
        return $this->table()->set("fields", json_encode($params));
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
        $ret = $this->table()->toArray($default);
        $ret["fields"] = json_decode($ret["fields"], true);
        if ($ret["fieldcount"] < 1) {
            $ret["fieldcount"] = 1;
        }
        $missing = $ret["fieldcount"] - count($ret["fields"]);
        for ($i = 0; $i < $missing; $i++) {
            $ret["fields"][] = array(
                "name" => "No Name", "device" => 0, "field" => 0
            );
        }
        return $ret;
    }
    /**
    * Gives us the ID of the last record inserted
    *
    * @return bool
    */
    public function create()
    {
        $this->table()->clearData();
        return $this->table()->newRow();
    }
}


?>
