<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\avr;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../Driver.php";


/**
 * Driver for reading voltage based pressure sensors
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class AVRAnalogTable extends \HUGnet\devices\inputTable\Driver
{
    /**
    * This is where we store the drivers
    */
    private $_driver = null;
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
        "longName" => "AVR Analog Table Entry",
        "shortName" => "AVRAnalogTable",
        "unitType" => "Unknown",
        "storageUnit" => "Unknown",
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Table Entry",
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            array()
        ),
        "extraDefault" => array(0, 0, 0),
        "maxDecimals" => 6,
    );
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_driver);
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
            "AVRAnalogTable", $sensor
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
    * @return object The driver requested
    */
    private function &_driver()
    {
        if (!is_object($this->_driver)) {
            $driver = explode(":", (string)$driver);
            $sensor = $this->input();
            $entry  = $this->_entry();
            $this->_driver[$num] = \HUGnet\devices\inputTable\DriverAVR::factory(
                \HUGnet\devices\inputTable\Driver::getDriver(
                    hexdec($driver[0]), $driver[1]
                ),
                $sensor,
                $entry,
                $num
            );
        }
        return $this->_driver;
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
            include_once dirname(__FILE__)."/../../tables/E00392101AnalogTable.php";
            $extra = $this->input()->get("extra");
            $this->_table()->getRow((int)$extra[0]);
            $entry = \HUGnet\devices\inputTable\tables\E00392101AnalogTable::factory(
                $this, $this->_table()->toArray()
            );
            $this->_entry = &$entry;
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
        $values = $this->_table()->select("arch = ?", array("AVR"));
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
                (array)$this->_driver()->get($name)
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
        $ret = $this->_driver()->decodeData($string, $deltaT, $prev, $data);
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
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
        &$string, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {

        $ret = $this->_driver()->decodeDataPoint($string, $channel);
        return $ret;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param int    $channel The channel to decode
    *
    * @return float The raw value
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function getRawData(&$string, $channel = 0)
    {
        $ret = $this->_driver()->getRawData($string, $channel);
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
        return $this->_driver()->encodeDataPoint(
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
        $ret = $this->_driver(0)->channels();
        if (is_array($ret[1])) {
            $ret[1]["index"] = 1;
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
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return mixed int if found, null if not
    */
    private function _find()
    {
        $entry = \HUGnet\devices\inputTable\tables\AVRAnalogTable::factory(
            $this, array()
        );
        $mine = json_encode($this->_entry()->toArray());
        $ret = $this->_table()->selectInto("1");
        while ($ret) {
            $entry = \HUGnet\devices\inputTable\tables\AVRAnalogTable::factory(
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
    * @return string
    */
    public function encode()
    {
        $string  = $this->_entry()->encode();
        return $string;
    }

}


?>