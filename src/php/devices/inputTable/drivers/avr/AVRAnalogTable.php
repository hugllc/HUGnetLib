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
require_once dirname(__FILE__)."/../../DriverAVR.php";


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
class AVRAnalogTable extends \HUGnet\devices\inputTable\DriverAVR
    implements \HUGnet\devices\inputTable\DriverInterface
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
    * This is where we store the table entry
    */
    private $_tableEntry = null;
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
        "extraDefault" => array(0),
        "maxDecimals" => 6,
    );
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$sensor The sensor in question
    * @param array  $table   The table to use.  This forces the table, instead of
    *                        using the database to find it
    *
    * @return null
    */
    protected function __construct(&$sensor, $table = null)
    {
        parent::__construct($sensor);
        if (is_array($table)) {
            $this->_tableEntry = $table;
        }
    }
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
    * @param array  $table   The table to use.  This forces the table, instead of
    *                        using the database to find it
    *
    * @return null
    */
    public static function &testFactory(
        &$sensor, $class = "InputTable", $table = null
    ) {
        $obj = \HUGnet\devices\inputTable\Driver::factory(
            "AVRAnalogTable", $sensor, $table
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
            $entry  = $this->entry();
            $driver = explode(":", (string)$entry->get("driver"));
            $input  = $this->input();
            $this->_driver = \HUGnet\devices\inputTable\DriverAVR::factory(
                \HUGnet\devices\inputTable\Driver::getDriver(
                    hexdec($driver[0]), $driver[1]
                ),
                $input,
                $entry
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
    * @param array $table The table to use.  This only works on the first call
    *
    * @return object The driver requested
    */
    public function &entry($table = null)
    {
        if (!is_object($this->_entry)) {
            if (is_array($this->_tableEntry)) {
                $table = $this->_tableEntry;
            } else {
                $extra = $this->input()->table()->get("extra");
                $this->_table()->getRow((int)$extra[0]);
                $table = $this->_table()->toArray();
            }
            $driver = $this->_entryDriver();
            $entry = $driver::factory(
                $this, $table, count($this->params["extraDefault"])
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
    private function _entryDriver()
    {
        $dir = dirname(__FILE__)."/../../tables/";
        $namespace = "\\HUGnet\\devices\\inputTable\\tables\\";
        $arch = $this->input()->device()->get("arch");
        switch ($arch) {
        case "0039-12":
            include_once $dir."E003912AnalogTable.php";
            $class = $namespace."E003912AnalogTable";
            break;
        case "0039-21-01":
            include_once $dir."E00392101AnalogTable.php";
            $class = $namespace."E00392101AnalogTable";
            break;
        case "0039-21-02":
            include_once $dir."E00392102AnalogTable.php";
            $class = $namespace."E00392102AnalogTable";
            break;
        case "0039-28":
            include_once $dir."E003928AnalogTable.php";
            $class = $namespace."E003928AnalogTable";
            break;
        default:
            include_once $dir."AVRAnalogTable.php";
            $class = $namespace."AVRAnalogTable";
            break;
        }
        return $class;
    }

    /**
    * Returns the driver object
    *
    * @return object The driver requested
    */
    private function _getTableEntries()
    {
        $return = array();
        if (is_array($this->_tableEntry)) {
            $return[$this->_tableEntry["id"]] = $this->_tableEntry["name"];
        } else {
            $values = $this->_table()->select(
                "arch = ?", array($this->input()->device()->get("arch"))
            );
            foreach ((array)$values as $val) {
                $return[$val->get("id")] = $val->get("name");
            }
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
        $param = $this->_driver()->get($name);
        switch ($name) {
        case "extraValues":
            $param = array_merge(
                (array)$this->params[$name],
                (array)$param
            );
            $param[0] = $this->_getTableEntries();
            break;
        case "extraText":
        case "extraDefault":
            $param = array_merge(
                (array)$this->params[$name],
                (array)$param
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
        return $ret;
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
        $ret = $this->_driver()->decodeDataPoint(
            $string, $channel, $deltaT, $prev, $data
        );
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
        $ret = $this->_driver()->channels();
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
        $this->entry()->decode($string);
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
        $mine = json_encode($this->entry()->toArray());
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
        $string  = $this->entry()->encode();
        return $string;
    }

}


?>
