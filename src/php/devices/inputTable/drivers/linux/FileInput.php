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
namespace HUGnet\devices\inputTable\drivers\linux;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * Default sensor driver
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
 */
class FileInput extends \HUGnet\devices\inputTable\Driver
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /** @var This is our driver name */
    private $_driverName = "";
    /** @var This is our driver */
    private $_driver = null;
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "File Input",
        "shortName" => "FileInput",
        "unitType" => "Unknown",
        "storageUnit" => 'Unknown',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Priority",
            "Driver",
        ),
        "extraNames" => array(
            "priority" => 0,
            "driver"   => 1,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            5, 
            array(
            )
        ),
        "extraDefault" => array(1, 0),
        "extraDesc" => array(
            "The number of times to run per second.  0.5 to 129",
            "The driver to emulate"
        ),
        "maxDecimals" => 0,
        "inputSize" => 4,
    );
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        return $this->_driver()->getReading($A, $deltaT, $data, $prev);
    }
    /**
    * Returns the reversed reading
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
    protected function getRaw(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        return $this->_driver()->getRaw($value, $channel, $deltaT, $prev, $data);
    }
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    private function _driver()
    {
        // This can't use getExtra because of recursion.  It doesn't have a default
        // anyway.
        $extra      = $this->input()->get("extra");
        $driver     = explode(":", $extra[1]);
        $sid        = hexdec($driver[0]);
        $type       = $driver[1];
        $driverName = $this->getDriver($sid, $type);
        if ($driverName !== $this->_driverName) {
            $input  = $this->input();
            $offset = 2;
            $this->_driverName = $driverName;
            $this->_driver = \HUGnet\devices\inputTable\Driver::factory(
                $this->getDriver($sid, $type), $input, $offset
            );
        }
        return $this->_driver;
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
        $ret = parent::get($name);
        switch ($name) {
        case "extraValues":
            $ret[1] = $this->getDrivers(true);
            unset($ret[1]["66:DEFAULT"]); // Remove this driver from the list
            $ret = array_merge($ret, (array)$this->_driver()->get("extraValues"));
            break;
        case "extraDefault":
        case "extraText":
        case "extraDesc":
            $ret = array_merge($ret, (array)$this->_driver()->get($name));
            break;
        }
        return $ret;
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string The string to decode
    *
    * @return array
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function decode($string)
    {
        $extra = $this->input()->get("extra");
        $extra[0] = $this->decodePriority(substr($string, 0, 2));
        $length   = $this->decodeInt(substr($string, 2, 2), 1);
        $extra[1] = hex2bin(substr($string, 4, $length));
        $this->input()->set("extra", $extra);
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function encode()
    {
        $string  = $this->encodePriority($this->getExtra(0));
        $driver  = bin2hex($this->getExtra(1));
        $string .= $this->encodeInt(strlen($driver), 1);
        $string .= $driver;
        return $string;
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channels()
    {
        return $this->_driver()->channels();
    }
}


?>
