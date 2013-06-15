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
namespace HUGnet\devices\inputTable\drivers;
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
class DifferenceInput extends \HUGnet\devices\inputTable\Driver
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Endpoint Math Input",
        "shortName" => "MathInput",
        "unitType" => "Units",
        "storageUnit" => "units",
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "Priority",
            "Data Channel",
            "Data Channel",
            "Offset"
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            5, array(), array(), 15
        ),
        "extraDefault" => array(
            128, 0, 0, 0
        ),
        "extraDesc" => array(
            "The number of times to run per second.  0.5 to 129",
            "The first number",
            "The number to subtract from the first number",
            "The offset to add to the math",
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
    protected function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        $bits = 32;
        $A = (int)($A & (pow(2, $bits) - 1));
        /* Calculate the top bit */
        $topBit = pow(2, ($bits - 1));
        /* Check to see if the top bit is set */
        if (($A & $topBit) == $topBit) {
            /* This is a negative number */
            $A = -(pow(2, $bits) - $A);
        }
        return $A;
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
            $data = $this->input()->device()->dataChannels()->select();
            $ret[1] = $data;
            $ret[2] = $data;
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
        $channels = $this->input()->device()->dataChannels();
        $extra    = $this->input()->get("extra");
        $extra[0] = $this->decodePriority(substr($string, 0, 2));
        $index    = 2;
        for ($i = 1; $i < 3; $i++) {
            $epChan   = $this->decodeInt(substr($string, $index, 2), 1);
            $dataChan = $channels->epChannel($epChan);
            $extra[$i] = $dataChan->get("channel");
            $index += 2;
        }
        $extra[3] = $this->decodeInt(substr($string, $index, 8), 4);
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
        $channels = $this->input()->device()->dataChannels();
        $string   = $this->encodePriority($this->getExtra(0));

        for ($i = 1; $i < 3; $i++) {
            $chan = (int)$this->getExtra($i);
            $dataChan = $channels->dataChannel($chan);
            $epChan   = (int)$dataChan->get("epChannel");
            $string  .= $this->encodeInt($epChan, 1);
        }
        $string  .= $this->encodeInt($this->getExtra(3), 4);
        return $string;
    }

}


?>
