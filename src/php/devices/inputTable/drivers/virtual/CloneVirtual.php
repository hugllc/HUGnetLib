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
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\virtual;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../DriverVirtual.php";

/**
 * Driver for reading voltage based pressure sensors
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class CloneVirtual extends \HUGnet\devices\inputTable\DriverVirtual
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
     * This is the sensor we are cloning
     */
    private $_clone = null;
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "shortName" => "CloneVirtual",
        "virtual" => true,              // This says if we are a virtual sensor
        "extraText" => array(
            "Device ID", "Input"
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(8, 3),
        "extraDefault" => array("", ""),
        "extraDesc" => array(
            "The DeviceID of the board (in hexidecimal)",
            "The INPUT to clone.  Zero based."
        ),
        "extraNames" => array(
            "deviceid" => 0,
            "datachan" => 1,
        ),
        "requires" => array(), // We don't require anything.
    );
    /**
     * This is the routine that gets the sensor that we are cloning
     *
     * @return object The object for the sensor we are cloning
     */
    private function _clone()
    {
        if (!is_object($this->_clone)) {
            $did = hexdec($this->getExtra(0));
            $sen = $this->getExtra(1);
            if ($did == 0) {
                $sensor = $this->input();
                $this->_clone = parent::factory("SDEFAULT", $sensor);
            } else {
                $sensor = $this->input()->system()->device($did)->input($sen);
                // Set our location the same as the other sensors and lock it there
                if ($sensor->get("driver") == "EmptySensor") {
                    $loc = sprintf("(Input %06X.%d does not exist)", $did, $sen);
                } else {
                    $loc = $sensor->get("location");
                    if (empty($loc)) {
                        $loc = "(No Input Label)";
                    }
                }
                $this->params["location"] = $loc;
                $this->input()->set("location", $loc);
                // Create our clone.
                $this->_clone = parent::factory(
                    $sensor->get("driver"),
                    $sensor
                );
            }
        }
        return $this->_clone;
    }
    /**
    * Returns all of the parameters and defaults in an array
    *
    * @return array of data from the sensor
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function toArray()
    {
        return array_merge($this->_clone()->toArray(), $this->params);
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
        $value = parent::get($name);
        if (isset($this->params[$name]) || is_null($value)) {
            return $value;
        } else {
            return $this->_clone()->get($name);
        }
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channels()
    {
        $ret = $this->_clone()->channels();
        if (empty($ret)) {
            $input = $this->getExtra(0).".".$this->getExtra(1);
            $ret = array(
                array(
                    "units" => "Unknown",
                    "unitType" => "Unknown",
                    "dataType" => \HUGnet\devices\datachan\Driver::TYPE_IGNORE,
                    "index" => 0,
                    "error" => "Input Not Found",
                )
            );
        } else {
            foreach (array_keys((array)$ret) as $key) {
                $ret[$key]["epChannel"] = false;
            }
        }
        return $ret;
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$hist  The history object or array
    * @param int    $chan    The channel this input starts at
    * @param float  $deltaT The time delta in seconds between this record
    * @param array  &$prev  The previous reading
    * @param array  &$data  The data from the other sensors that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function decodeData(
        &$hist, $chan, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $ret = $this->channels();
        $oid = $this->_clone()->input()->channelStart();
        foreach (array_keys((array)$ret) as $key) {
            $sen = $oid + $key;
            if (is_object($hist)) {
                $ret[$key]["value"] = $hist->get("Data".$sen);
            } else if (is_array($hist)) {
                $ret[$key]["value"] = $hist["Data".$sen];
            } else {
                $ret[$key]["value"] = null;
            }
            $ret[$key]["raw"] = null;
        }
        return $ret;
    }
}


?>
