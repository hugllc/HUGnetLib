<?php
/**
 * Sensor driver for wind direction sensors
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
 * @subpackage PluginsSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../DriverPulse.php";
/**
 * This class deals with wind direction sensors.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class LiquidVolume extends \HUGnet\devices\inputTable\DriverPulse
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /** This is where our port is stored */
    protected $portExtra = 2;
    /**
    * This is the array of sensor information.
    */
    protected $params = array(
        "longName" => "Liquid Volume Meter",
        "shortName" => "LiquidVolume",
        "unitType" => "Volume",
        "storageUnit" => 'gal',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Gallons / Pulse",
            "Clock Base",
            "Port",
            "Debounce",
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            10,
            array(0 => "Counter"),
            array(),
            3,
        ),
        "extraDefault" => array(1000, 0, 0, 3),
        "extraDesc" => array(
            "How many gallons each count of the counter represent",
            "The clock base to use to do the pulse counting",
            "The port to count pulses on",
            "The number of matching samples to count as a pulse.",
        ),
        "extraNames" => array(
            "galperpulse" => 0,
            "clockbase"   => 1,
            "port0"       => 2,
            "debounce"    => 3,
        ),
        "maxDecimals" => 2,
        "total" => true,
        "requires" => array("DI"),
        "provides" => array("DC"),
    );
    /**
    * This function returns the output in RPM
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
    protected function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        $extra = $this->getExtra(0);
        if (empty($extra)) {
            $extra = 1;
        }
        if (is_null($A)) {
            return null;
        }
        return round($A/$extra, $this->get("maxDecimals"));
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
        $extra = $this->getExtra(0);
        if (empty($extra)) {
            $extra = 1;
        }
        if (is_null($value)) {
            return null;
        }
        return (int)($value * $extra);
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string The string to decode
    *
    * @return array
    */
    public function decode($string)
    {
        $extra = $this->pDecode($string, 1);
        $extra[0] = $this->decodeInt(substr($string, 0, 4), 2);
        $this->input()->set("extra", $extra);
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function encode()
    {
        $string  = $this->pEncode(1);
        $string .= $this->encodeInt($this->getExtra(0), 2);
        return $string;
    }

}

?>
