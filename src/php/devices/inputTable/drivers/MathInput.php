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
class MathInput extends \HUGnet\devices\inputTable\Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Endpoint Math Input",
        "shortName" => "MathInput",
        "unitType" => "Units",
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_DIFF,
        "extraText" => array(
            "Priority",
            "Data Channel",
            "Operator",
            "Data Channel",
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            5, array(),
            array(0 => "+", 1 => "-"), array(),
        ),
        "extraDefault" => array(
            1, 0, 1, 0
        ),
        "maxDecimals" => 8,
        "inputSize" => 4,
    );
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param int    $channel The channel to decode
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
        $A = null;
        if (!is_null($string)) {
            $A = $this->getRawData($string, $channel);
        }
        $me = $this->get("channel");
        $read = 0;
        $chan = $this->getExtra(1);
        if ($chan != $me) {
            $dataChan0 = $this->input()->device()->dataChannel($chan);
            $read = $dataChan0->decode($A);
        }
        $chan = $this->getExtra(3);
        if ($chan != $me) {
            $dataChan1 = $this->input()->device()->dataChannel($chan);
            $read -= $dataChan1->decode(0);
        }
        return $read;
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
        $me = $this->input()->get("channel");
        $val = 0;
        $chan = $this->getExtra(1);
        if ($chan !== $me) {
            $dataChan0 = $this->input()->device()->dataChannel($chan);
            $val = $this->decodeInt($dataChan0->encode($value), 4);
        }
        $chan = $this->getExtra(3);
        if ($chan != $me) {
            $dataChan1 = $this->input()->device()->dataChannel($chan);
            $val -= $this->decodeInt($dataChan1->encode(0), 4);
        }
        if (!is_null($val)) {
            return $this->intToStr((int)$val);
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
            $data = $this->input()->device()->dataChannels()->select();
            $ret[1] = $data;
            $ret[3] = $data;
        } else if ($name == "storageUnit") {
            if ($this->getExtra(1) != $this->get("channel")) {
                $dataChan = $this->input()->device()->dataChannel($this->getExtra(1));
                $ret = $dataChan->get("storageUnit");
            } else {
                $ret = "Unknown";
            }
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
        $extra[0] = $this->decodeInt(substr($string, 0, 2), 1);
        $epChan   = $this->decodeInt(substr($string, 2, 2), 1);
        $dataChan = $channels->epChannel($epChan);
        $extra[1] = $dataChan->get("channel");

        $index = 4;
        for ($i = 2; $i < count($this->params["extraText"]); $i+=2) {
            $extra[$i] = $this->decodeInt(substr($string, $index, 2), 1);
            $index += 2;
            $epChan   = $this->decodeInt(substr($string, $index, 2), 1);
            if ($epChan == 0xFF) {
                $extra[$i+1] = 0xFF;
            } else {
                $dataChan = $channels->epChannel($epChan);
                $extra[$i+1] = $dataChan->get("channel");
            }
            $index += 2;
        }

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
        $string   = $this->encodeInt($this->getExtra(0), 1);
        $dataChan = $channels->dataChannel((int)$this->getExtra(1));
        $epChan   = (int)$dataChan->get("epChannel");
        $string  .= $this->encodeInt($epChan, 1);

        for ($i = 2; $i < count($this->params["extraText"]); $i+=2) {
            $string  .= $this->encodeInt($this->getExtra($i), 1);
            $chan = (int)$this->getExtra($i+1);
            if ($chan == 0xFF) {
                $epChan = 0xFF;
            } else {
                $dataChan = $channels->dataChannel($chan);
                $epChan   = (int)$dataChan->get("epChannel");
            }
            $string  .= $this->encodeInt($epChan, 1);
        }
        return $string;
    }

}


?>
