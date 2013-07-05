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
require_once dirname(__FILE__)."/../Driver.php";
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
class MaximumWindDirection extends \HUGnet\devices\inputTable\Driver
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /** @var array These our the direction masks */
    protected $directions = array(
        "00000001" => 0.0,
        "00000011" => 22.5,
        "00000010" => 45.0,
        "00000110" => 67.5,
        "00000100" => 90.0,
        "00001100" => 112.5,
        "00001000" => 135.0,
        "00011000" => 157.5,
        "00010000" => 180.0,
        "00110000" => 202.5,
        "00100000" => 225.0,
        "01100000" => 247.5,
        "01000000" => 270.0,
        "11000000" => 292.5,
        "10000000" => 315.0,
        "10000001" => 337.5,
    );
    /**
    * This is the array of sensor information.
    */
    protected $params = array(
        "longName" => "Maximum Inc Wind Direction Sensor",
        "shortName" => "MaxWindDir",
        "unitType" => "Direction",
        "storageUnit" => '&#176;',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Priority",
            "Terminal 1",
            "Terminal 2",
            "Terminal 3",
            "Terminal 4",
            "Terminal 5",
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            5,
            array(
                1  => "Port 1",
                2  => "Port 2",
                3  => "Port 3",
                4  => "Port 4",
                5  => "Port 5",
                6  => "Port 6",
                7  => "Port 7",
                8  => "Port 8",
                9  => "Port 9",
                10 => "Port 10",
                11 => "Port 11",
                12 => "Port 12",
                13 => "Port 13",
                14 => "Port 14",
                15 => "Port 15",
                16 => "Port 16",
            ),
            array(
                1  => "Port 1",
                2  => "Port 2",
                3  => "Port 3",
                4  => "Port 4",
                5  => "Port 5",
                6  => "Port 6",
                7  => "Port 7",
                8  => "Port 8",
                9  => "Port 9",
                10 => "Port 10",
                11 => "Port 11",
                12 => "Port 12",
                13 => "Port 13",
                14 => "Port 14",
                15 => "Port 15",
                16 => "Port 16",
            ),
            array(
                1  => "Port 1",
                2  => "Port 2",
                3  => "Port 3",
                4  => "Port 4",
                5  => "Port 5",
                6  => "Port 6",
                7  => "Port 7",
                8  => "Port 8",
                9  => "Port 9",
                10 => "Port 10",
                11 => "Port 11",
                12 => "Port 12",
                13 => "Port 13",
                14 => "Port 14",
                15 => "Port 15",
                16 => "Port 16",
            ),
            array(
                1  => "Port 1",
                2  => "Port 2",
                3  => "Port 3",
                4  => "Port 4",
                5  => "Port 5",
                6  => "Port 6",
                7  => "Port 7",
                8  => "Port 8",
                9  => "Port 9",
                10 => "Port 10",
                11 => "Port 11",
                12 => "Port 12",
                13 => "Port 13",
                14 => "Port 14",
                15 => "Port 15",
                16 => "Port 16",
            ),
            array(
                1  => "Port 1",
                2  => "Port 2",
                3  => "Port 3",
                4  => "Port 4",
                5  => "Port 5",
                6  => "Port 6",
                7  => "Port 7",
                8  => "Port 8",
                9  => "Port 9",
                10 => "Port 10",
                11 => "Port 11",
                12 => "Port 12",
                13 => "Port 13",
                14 => "Port 14",
                15 => "Port 15",
                16 => "Port 16",
            ),
        ),
        "extraDefault" => array(
            128, 8, 9, 12, 10, 11
        ),
        "extraDesc" => array(
            "The number of times to run per second.  0.5 to 129",
            "The port that terminal 1 of the sensor is tied to",
            "The port that terminal 2 of the sensor is tied to",
            "The port that terminal 3 of the sensor is tied to",
            "The port that terminal 4 of the sensor is tied to",
            "The port that terminal 5 of the sensor is tied to",
        ),
        "inputSize" => 3,
        "maxDecimals" => 0,
    );
    /**
    * Returns a numeric direction in degrees from the numeric bit
    * field that is returned by the Maximum Inc wind direction sensor.
    *
    * In this bit field the even bits are the cardinal directions and
    * the odd bits are the ordinal directions.  Only one bit in each
    * set can be set, and if there is a bit set in each they have to be
    * 45 degrees apart (0 sometimes counts as 360 because degrees are
    * circular).
    *
    * - If the ordinal direction is null it returns the cardinal direction.
    * - If the cardinal direction is null it returns the ordinal direction.
    * - Otherwise it retuns the average of the two.  This is only valid where
    *   the difference between the two is 45, so it checks this first.
    * - One special case is when the cardinal direction is north (0) and
    *   the ordinal direction is NW (315).  In this case the cardinal
    *   direction needs to be changed to 360 for the averaging to work
    *   properly.
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
        foreach ($this->directions as $mask => $dir) {
            if ($A === bindec($mask)) {
                return (float)$dir;
            }
        }
        return null;
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
        foreach ($this->directions as $mask => $dir) {
            if ($value === $dir) {
                return bindec($mask);
            }
        }
        return null;
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
        $extra    = $this->input()->get("extra");
        $extra[0] = $this->decodePriority(substr($string, 2, 2));
        $extra[1] = $this->decodeInt(substr($string, 4, 2), 1);
        $extra[2] = $this->decodeInt(substr($string, 6, 2), 1);
        $extra[3] = $this->decodeInt(substr($string, 8, 2), 1);
        $extra[4] = $this->decodeInt(substr($string, 10, 2), 1);
        $extra[5] = $this->decodeInt(substr($string, 12, 2), 1);
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
        $string  = "00";  // Subdriver is always 0
        $string .= $this->encodePriority($this->getExtra(0));
        $string .= $this->encodeInt($this->getExtra(1), 1);
        $string .= $this->encodeInt($this->getExtra(2), 1);
        $string .= $this->encodeInt($this->getExtra(3), 1);
        $string .= $this->encodeInt($this->getExtra(4), 1);
        $string .= $this->encodeInt($this->getExtra(5), 1);
        return $string;
    }
}

?>
