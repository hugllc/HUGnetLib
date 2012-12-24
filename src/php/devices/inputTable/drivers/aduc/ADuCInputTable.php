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
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\aduc;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../DriverADuC.php";


/**
 * Driver for reading voltage based pressure sensors
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class ADuCInputTable extends \HUGnet\devices\inputTable\Driver
{
    /**
    * This is where we store the drivers
    */
    private $_driver = array(
        0 => null,
        1 => null
    );
    /**
    * This is where we store the InputTable
    */
    private $_table;
    /**
    * This is where we store the InputTable
    */
    private $_tableClass = "InputTable";
    /**
    * This is where we store our entry in the input table
    */
    private $_entry;
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "ADuC Input Table Entry",
        "shortName" => "ADuCInputTable",
        "unitType" => "Unknown",
        "storageUnit" => "Unknown",
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Table Entry",
            "Channel 0 Reading @ 0",
            "Channel 1 Reading @ 0",
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            array(), 10, 10
        ),
        "extraDefault" => array(0, 0, 0),
        "maxDecimals" => 6,
    );
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        foreach (array_keys((array)$this->_driver) as $key) {
            unset($this->_driver[$key]);
        }
        unset($this->_table);
        unset($this->_entry);
        parent::__destruct();
    }
    /**
    * This function creates the system.
    *
    * @param object &$sensor The sensor object
    * @param string $class   The class to use
    *
    * @return null
    */
    public static function &testFactory(&$sensor, $class = "InputTable")
    {
        $obj = \HUGnet\devices\inputTable\Driver::factory(
            "ADuCInputTable", $sensor
        );
        if (is_object($class)) {
            $obj->_table = $class;
        } else if (is_string($class)) {
            $obj->_tableClass = $class;
        }
        return $obj;
    }
    /**
    * Returns the driver object
    *
    * @param int $num The driver number
    *
    * @return object The driver requested
    */
    private function &_driver($num)
    {
        if (!is_object($this->_driver[$num])) {
            $offset = count($this->params["extraDefault"]);
            if ($num == 0) {
                $driver = $this->_entry()->driver0();
            } else if ($num == 1) {
                $driver = $this->_entry()->driver1();
                $offset += count($this->_driver(0)->get("extraDefault"));
            } else {
                return null;
            }
            $driver = explode(":", (string)$driver);
            $sensor = $this->input();
            $entry  = $this->_entry();
            $this->_driver[$num] = \HUGnet\devices\inputTable\DriverADuC::factory(
                \HUGnet\devices\inputTable\Driver::getDriver(
                    hexdec($driver[0]), $driver[1]
                ),
                $sensor,
                $offset,
                $entry,
                $num
            );
        }
        return $this->_driver[$num];
    }
    /**
    * Returns the driver object
    *
    * @param string $class The class to use
    *
    * @return object The driver requested
    */
    private function &_table($class = "InputTable")
    {
        if (!is_object($this->_table)) {
            $this->_table = $this->input()->system()->table($class);
        }
        return $this->_table;
    }
    /**
    * Returns the driver object
    *
    * @return object The driver requested
    */
    private function &_entry()
    {
        if (!is_object($this->_entry)) {
            include_once dirname(__FILE__)."/../../ADuCInputTable.php";
            $extra = $this->input()->get("extra");
            $this->_table()->getRow((int)$extra[0]);
            $this->_entry = \HUGnet\devices\inputTable\ADuCInputTable::factory(
                $this, $this->_table()->toArray()
            );
        }
        return $this->_entry;
    }
    /**
    * Returns the driver object
    *
    * @return object The driver requested
    */
    private function _getTableEntries()
    {
        $values = $this->_table()->select("arch = ?", array("ADuC"));
        $return = array();
        foreach ((array)$values as $val) {
            $return[$val->get("id")] = $val->get("name");
        }
        return $return;
    }
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    public function get($name)
    {
        $param = parent::get($name);
        switch ($name) {
        case "extraValues":
            $param = (array)$param;
            $param[0] = $this->_getTableEntries();
        case "extraText":
        case "extraDefault":
            $param = array_merge(
                (array)$param,
                (array)$this->_driver(0)->get($name),
                (array)$this->_driver(1)->get($name)
            );
            break;
        }
        return $param;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param float  $deltaT  The time delta in seconds between this record
    * @param array  &$prev   The previous reading
    * @param array  &$data   The data from the other sensors that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function decodeData(
        &$string, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $ret = array_merge(
            (array)$this->_driver(0)->decodeData($string, $deltaT, $prev, $data),
            (array)$this->_driver(1)->decodeData($string, $deltaT, $prev, $data)
        );
        return $ret;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string $string  The data string
    * @param int    $channel The channel to use
    * @param float  $deltaT  The time delta in seconds between this record
    * @param array  &$prev   The previous reading
    * @param array  &$data   The data from the other sensors that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function decodeDataPoint(
        $string, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {

        if ($channel > count($this->_driver(0)->channels())) {
            $ret = $this->_driver(1)->decData($string, $channel);
        } else {
            $ret = $this->_driver(0)->decData($string, $channel);
        }
        return $ret;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param array $value   The data to use
    * @param int   $channel The channel to get
    * @param float $deltaT  The time delta in seconds between this record
    * @param array &$prev   The previous reading
    * @param array &$data   The data from the other sensors that were crunched
    *
    * @return string The reading as it would have come out of the endpoint
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function encodeDataPoint(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        if ($channel > count($this->_driver(0)->channels())) {
            return $this->_driver(1)->encodeDataPoint(
                $value, $channel, $deltaT, $prev, $data
            );
        }
        return $this->_driver(0)->encodeDataPoint(
            $value, $channel, $deltaT, $prev, $data
        );
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channels()
    {
        $ret = array_merge(
            (array)$this->_driver(0)->channels(),
            (array)$this->_driver(1)->channels()
        );
        if (is_array($ret[1])) {
            $ret[1]["index"] = 1;
        }
        return $ret;
    }
    /**
    * Takes a little endian 32 bit ascii hex number and turns it into an int
    *
    * @param int $value The value to encode
    *
    * @return string
    */
    private function _decode32($value)
    {
        $ret = hexdec(
            substr($value, 6, 2).substr($value, 4, 2)
            .substr($value, 2, 2).substr($value, 0, 2)
        );
        if (($ret & 0x80000000) === 0x80000000) {
            $ret = -(0x100000000 - $ret);
        }
        return $ret;
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string The string to decode
    *
    * @return null
    */
    public function decode($string)
    {
        $this->_entry()->decode($string);
        $extra = $this->input()->get("extra");
        $extra[1] = $this->_decode32(substr($string, 22, 8));
        $extra[2] = $this->_decode32(substr($string, 30, 8));
        if (!isset($extra[0])) {
            $iid = $this->_find();
            if (!is_null($iid)) {
                $extra[0] = $iid;
            }
        }
        $this->input()->set("extra", $extra);
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return mixed int if found, null if not
    */
    private function _find()
    {
        $entry = \HUGnet\devices\inputTable\ADuCInputTable::factory(
            $this, array()
        );
        $mine = json_encode($this->_entry()->toArray());
        $ret = $this->_table()->selectInto("1");
        while ($ret) {
            $entry = \HUGnet\devices\inputTable\ADuCInputTable::factory(
                $this, $this->_table()->toArray()
            );
            $row = json_encode($entry->toArray());
            if ($row === $mine) {
                return $this->_table()->get("id");
            }
            $ret = $this->_table()->nextInto();
        }
        return null;
    }
    /**
    * Encodes this driver as a setup string
    *
    * @param int $value The value to encode
    *
    * @return string
    */
    private function _encode32($value)
    {
        return sprintf(
            "%02X%02X%02X%02X",
            ($value >> 0) & 0xFF,
            ($value >> 8) & 0xFF,
            ($value >> 16) & 0xFF,
            ($value >> 24) & 0xFF
        );
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return string
    */
    public function encode()
    {
        $string  = $this->_entry()->encode();
        $string .= $this->_encode32($this->getExtra(1));
        $string .= $this->_encode32($this->getExtra(2));
        return $string;
    }

}


?>
