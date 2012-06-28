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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\sensors\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../DriverADuC.php";


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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCInputTable extends \HUGnet\sensors\Driver
{
    /**
    * This is where we store the drivers
    */
    private $_drivers = array(
        0 => null,
        1 => null
    );
    /**
    * This is where we store the InputTable
    */
    private $_table;
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
        "storageType" => \HUGnet\units\Driver::TYPE_RAW, // Storage dataType
        "extraText" => array(
            "Table Entry"
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
    * This is the destructor
    */
    public function __destruct()
    {
        foreach (array_keys((array)$this->_driver) as $key) {
            unset($this->_driver[$key]);
        }
    }
    /**
    * This function creates the system.
    *
    * @param object &$sensor The sensor object
    *
    * @return null
    */
    public static function &factory(&$sensor)
    {
        return parent::intFactory($sensor);
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
            if ($num == 0) {
                $driver = $this->_entry()->driver0();
            } else {
                $driver = $this->_entry()->driver1();
            }
            $driver = explode(":", (string)$driver);
            $this->_driver[$num] = &\HUGnet\sensors\Driver::factory(
                \HUGnet\sensors\Driver::getDriver(hexdec($driver[0]), $driver[1]),
                $this->sensor()
            );
        }
        return $this->_driver[$num];
    }
    /**
    * Returns the driver object
    *
    * @return object The driver requested
    */
    private function &_table()
    {
        if (!is_object($this->_table)) {
            include_once dirname(__FILE__)."/../../tables/InputTableTable.php";
            $this->_table = new \InputTableTable();
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
            include_once dirname(__FILE__)."/../ADuCInputTable.php";
            $this->_table()->getRow($this->getExtra(0));
            $this->_entry = \HUGnet\sensors\ADuCInputTable::factory(
                $this, $this->_table()->toArray()
            );
        }
        return $this->_entry;
    }
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    * @param int    $sid  The sensor ID to use
    *
    * @return null
    */
    public function get($name, $sid = null)
    {
        if (!is_int($sid)) {
            $sid = $this->sensor()->id();
        }
        $sid = (int)$sid;
        $param = parent::get($name);
        if (is_object($this->_entry)) {
            switch ($name) {
            case "extraDefault":
            case "extraText":
            case "extraValues":
                $param = array_merge(
                    (array)$param,
                    (array)$this->_driver(0)->get($name),
                    (array)$this->_driver(1)->get($name)
                );
                break;
            }
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
        return $ret;
    }

}


?>