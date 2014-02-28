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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class ADuCInputTable extends \HUGnet\devices\inputTable\Driver
    implements \HUGnet\devices\inputTable\DriverInterface
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
    * This is the class to use for our entry object.
    */
    protected $entryClass = "ADuCInputTable";
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
            "Reserved",
            "Channel 0 Reading @ 0",
            "Channel 1 Reading @ 0",
        ),
        "extraDesc" => array(
            "Reserved",
            "The offset for ADC0.  This is in the units of the driver used",
            "The offset for ADC1.  This is in the units of the driver used",
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            -1, 10, 10
        ),
        "extraDefault" => array("", 0, 0),
        "maxDecimals" => 6,
    );
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$sensor The sensor in question
    *
    * @return null
    */
    protected function __construct(&$sensor)
    {
        parent::__construct($sensor);
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        foreach (array_keys((array)$this->_driver) as $key) {
            unset($this->_driver[$key]);
        }
        unset($this->_table);
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
                $driver = $this->entry()->driver0();
            } else if ($num == 1) {
                $driver = $this->entry()->driver1();
                $offset += count($this->_driver(0)->get("extraDefault"));
            } else {
                return null;
            }
            $driver = explode(":", (string)$driver);
            $sensor = $this->input();
            $entry  = $this->entry();
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
    * Returns the converted table entry
    *
    * @return bool The table to use
    */
    protected function convertOldEntry()
    {
        $extra = $this->input()->table()->get("extra");
        $this->_table()->getRow((int)$extra[0]);
        $table = $this->_table()->toArray();
        return $table;
    }
    /**
    * Returns the driver object
    *
    * @return object The driver requested
    */
    private function _getTableEntries()
    {
        $values = $this->_table()->select("arch = ?", array("0039-37"));
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
            //$param = (array)$param;
            //$param[0] = $this->_getTableEntries();
        case "extraText":
        case "extraDesc":
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

        $chan0 = count($this->_driver(0)->channels());
        if ($channel >=  $chan0) {
            $ret = $this->_driver(1)->decodeDataPoint($string, ($channel - $chan0));
        } else {
            $ret = $this->_driver(0)->decodeDataPoint($string, $channel);
        }
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
        $chan0 = count($this->_driver(0)->channels());
        if ($channel >=  $chan0) {
            $ret = $this->_driver(1)->getRawData($string, ($channel - $chan0));
        } else {
            $ret = $this->_driver(0)->getRawData($string, $channel);
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
        $chan0 = count($this->_driver(0)->channels());
        if ($channel >=  $chan0) {
            return $this->_driver(1)->encodeDataPoint(
                $value, ($channel - $chan0), $deltaT, $prev, $data
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
        $ret = array();
        $suffixes = explode(",", (string)$this->getExtra(0));
        $chan = array(
            0 => (array)$this->_driver(0)->channels(),
            1 => (array)$this->_driver(1)->channels(),
        );
        $index = 0;
        foreach ($chan as $k => $c) {
            foreach ($c as $d) {
                $d["index"] = $index++;
                $ret[] = $d;
            }
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
        $this->entry()->decode($string);
        $this->input()->table()->set("tableEntry", $this->entry()->toArray());
        $extra = $this->input()->get("extra");
        $start = 22;
        $data = substr($string, $start);
        $val = $this->decodeDataPoint($data, 0);
        if (!is_null($val)) {
            $extra[1] = $val;
        }
        $val = $this->decodeDataPoint($data, 1);
        if (!is_null($val)) {
            $extra[2] = $val;
        }
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
        $entry = \HUGnet\devices\inputTable\tables\ADuCInputTable::factory(
            $this, array()
        );
        $mine = json_encode($this->entry()->toArray());
        $ret = $this->_table()->selectInto("1");
        while ($ret) {
            $entry = \HUGnet\devices\inputTable\tables\ADuCInputTable::factory(
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
        // This calculates the offset from 0, then encodes it
        $zero = $this->_driver(0)->getRaw(0);
        $val  = $this->_driver(0)->getRaw($this->getExtra(1)) - $zero;
        $string .= $this->encodeInt($val, 4);
        // This calculates the offset from 0, then encodes it
        $zero = $this->_driver(1)->getRaw(0);
        $val  = $this->_driver(1)->getRaw($this->getExtra(2)) - $zero;
        $string .= $this->encodeInt($val, 4);
        $string .= $this->_driver(0)->encode();
        $string .= $this->_driver(1)->encode();
        return $string;
    }

}


?>
