<?php
/**
 * Sensor driver for wind direction sensors
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
 * @subpackage PluginsSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class WattNode extends \HUGnet\devices\inputTable\DriverPulse
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is the array of sensor information.
    */
    protected $params = array(
        "longName" => "CCS WattNode Pulse Output Power Meter",
        "shortName" => "WattNode",
        "unitType" => "Power",
        "storageUnit" => 'kWh',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Watt Hours / Pulse",
            "Clock Base",
            "Port",
            "Debounce"
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            7,
            array(0 => "Counter"),
            array(),
            2,
        ),
        "extraDefault" => array(5, 0, 0, 3),
        "extraDesc" => array(
            "The number of matching samples to count as a pulse.",
            "How many watt hours are in each pulse of the WattNode?",
            "The clock base to use to do the pulse counting",
            "The port to count pulses on",
        ),
        "maxDecimals" => 3,
        "total" => true,
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
        $Wh = $A * $extra;
        if ($Wh < 0) {
            return null;
        }
        return round($Wh / 1000, 4);
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
        $Wh = $value * 1000;
        if ($extra == 0) {
            return null;
        }
        $A = $Wh / $extra;
        return round($A);

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
        $extra    = $this->pDecode($string, 1);
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
