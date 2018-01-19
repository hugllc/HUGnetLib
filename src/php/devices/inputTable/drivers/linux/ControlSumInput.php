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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ControlSumInput extends \HUGnet\devices\inputTable\Driver
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Control Value Sum Input",
        "shortName" => "ControlSumInput",
        "unitType" => "Units",
        "storageUnit" => 'units',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            0 => "Priority",
            1 => "Control Channel",
            2 => "Gain",
            3 => "Offset",
            4 => "Min",
            5 => "Max",
            6 => "Noise Min",
            7 => "Noise Max",
            8 => "Mode"
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            5, array(), 7, 15, 15, 15, 6, 6,
            array(0 => "Normal", 1 => "Float")
        ),
        "extraDefault" => array(128, 0, 1, 0, 0, 16777215, 0, 0, 0),
        "extraDesc" => array(
            0 => "The number of times to run per second.  0.5 to 129",
            1 => "Control Channel to use for our input",
            2 => "The input is multiplied by this",
            3 => "This is added to the input",
            4 => "The minimum this intput can be",
            5 => "The maximum this input can be",
            6 => "The minimum the added noise can be.  Set to 0 for no noise.",
            7 => "The maximum the added noise can be.  Set to 0 for no noise.",
            8 => "The mode the data should be set to the data channel as."
        ),
        "extraNames" => array(
            "frequency"    => 0,
            "controlchan0" => 1,
            "gain"         => 2,
            "offset"       => 3,
            "min"          => 4,
            "max"          => 5,
            "noisemin"     => 6,
            "noisemax"     => 7,
            "mode"         => 8,
        ),
        "maxDecimals" => 0,
        "inputSize" => 4,
        "requires" => array("CC"),
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
        $A = $this->signedInt($A, 4);
        $mode = $this->getExtra(8);
        if ($mode == 1) {
            $A = $this->decodeFloat($A);
        }
        return $A;
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
        $val = $this->getRaw(
            $value, $channel, $deltaT, $prev, $data
        );
        if (!is_null($val)) {
            $mode = $this->getExtra(8);
            if ($mode == 1) {
                return $this->encodeFloat($val);
            } else {
                return $this->intToStr((int)$val);
            }
        }
        return "";
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
        if ($name == "extraValues") {
            $ret[1] = $this->input()->device()->controlChannels()->select();
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
        $extra[1] = $this->decodeInt(substr($string, 2, 2), 1);
        $extra[2] = $this->decodeInt(substr($string, 4, 4), 2, true);
        $extra[3] = $this->decodeInt(substr($string, 8, 8), 4, true);
        $extra[4] = $this->decodeInt(substr($string, 16, 8), 4, true);
        $extra[5] = $this->decodeInt(substr($string, 24, 8), 4, true);
        $extra[6] = $this->decodeInt(substr($string, 32, 4), 2, true);
        $extra[7] = $this->decodeInt(substr($string, 36, 4), 2, true);
        $extra[8] = $this->decodeInt(substr($string, 40, 2), 1, false);
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
        $string .= $this->encodeInt($this->getExtra(1), 1);
        $string .= $this->encodeInt($this->getExtra(2), 2);
        $string .= $this->encodeInt($this->getExtra(3), 4);
        $string .= $this->encodeInt($this->getExtra(4), 4);
        $string .= $this->encodeInt($this->getExtra(5), 4);
        $string .= $this->encodeInt($this->getExtra(6), 2);
        $string .= $this->encodeInt($this->getExtra(7), 2);
        $string .= $this->encodeInt($this->getExtra(8), 1);
        return $string;
    }

}


?>
