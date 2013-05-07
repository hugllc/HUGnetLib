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
namespace HUGnet\devices\processTable\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../Driver.php";
/** This is our interface */
require_once dirname(__FILE__)."/../DriverInterface.php";

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
 */
class LevelHolderProcess extends \HUGnet\devices\processTable\Driver
    implements \HUGnet\devices\processTable\DriverInterface
{
    /*
    const CGND_OFFSET = 0.95;
    const STEP_VOLTAGE = 0.0006103515625;  // 2.5 / 4096
    const MAX_VOLTAGE = 1.2;
    */
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "LevelHolder Process",
        "shortName" => "LevelHolder",
        "extraText" => array(
            0  => "Priority",
            1  => "Control",
            2  => "Step",
            3  => "Limiter 1 Data Channel",
            4  => "Limiter 1 High",
            5  => "Limiter 1 Low",
            6  => "Limiter 2 High",
            7  => "Limiter 2 Low",
            8  => "Limiter 2 Tolerance",
            9  => "Data Channel",
            10 => "Set Point",
            11 => "Tolerance",
        ),
        "extraDesc" => array(
            "0-255 The minimum number of 1/128th of a second",
            "The control channel to use",
            "-32768 to 32767 The amount added or subracted from the control channel",
            "The data channel to use for the first limiter",
            "(Units for data channel) The maximum for the first limiter",
            "(Units for data channel) The minimum for the first limiter",
            "The data channel to use for the second limiter",
            "(Units for data channel) The maximum for the second limiter",
            "(Units for data channel) The minimum for the second limiter",
            "The data channel to use for the control",
            "(Units for data channel) The set point to use for the control",
            "(Units for data channel) The tolerance to use for the control",
        ),
        "extraDefault" => array(
            34, 0, 2, 0xFF, 0, 0, 0xFF, 0, 0, 0, 0, 0.01,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            4, array(), 10, array(), 15, 15, array(), 15, 15, array(), 15, 15
        ),
    );
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
            $control = $this->process()->device()->controlChannels()->select(
                array("" => "None")
            );
            $dataChans = $this->process()->device()->dataChannels();
            $data = $dataChans->select(array(0xFF => "None"), true);
            $ret[1] = $control;
            $ret[3] = $data;
            $ret[6] = $data;
            $ret[9] = $dataChans->select(array(), true);
        }
        return $ret;
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
        $extra = (array)$this->process()->get("extra");
        $extra[0] = $this->decodeInt(substr($string, 0, 2), 1);
        $extra[1] = $this->decodeInt(substr($string, 2, 2), 1);
        $extra[2] = $this->decodeInt(substr($string, 4, 4), 2, true);
        // min is hard coded at substr($string, 6, 8)
        // max is hard coded at substr($string, 14, 8)
        $this->_decodeChannels(substr($string, 24), $extra);
        $this->process()->set("extra", $extra);
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string The string to decode
    * @param array  &$extra The extra array to use
    *
    * @return array
    */
    private function _decodeChannels($string, &$extra)
    {
        $index = 0;
        $channels = $this->process()->device()->dataChannels();
        for ($i = 3; $i < 9; $i += 3) {
            $epChan = substr($string, $index, 2);
            if (($epChan == "FF") || ($epChan === false)) {
                // Empty string or slot
                unset($extra[$i]);
                unset($extra[$i+1]);
                unset($extra[$i+2]);
                continue;
            }
            $epChan = $this->decodeInt($epChan, 1);
            $dataChan = $channels->epChannel($epChan);

            $extra[$i] = $dataChan->get("channel");
            $index += 2;

            $low = $dataChan->decode(
                substr($string, $index, 8)
            );
            $index += 8;
            $high = $dataChan->decode(
                substr($string, $index, 8)
            );
            $index += 8;
            $extra[$i+1] = round(
                $high,
                $dataChan->get("decimals")
            );
            $extra[$i+2] = round(
                $low,
                $dataChan->get("decimals")
            );
        }
        $epChan = substr($string, $index, 2);
        $epChan = $this->decodeInt($epChan, 1);
        $dataChan = $channels->epChannel($epChan);

        $extra[$i] = $dataChan->get("channel");
        $index += 2;

        $low = $dataChan->decode(
            substr($string, $index, 8)
        );
        $index += 8;
        $high = $dataChan->decode(
            substr($string, $index, 8)
        );
        $index += 8;
        $extra[$i+1] = round(
            ($low + $high) / 2,
            $dataChan->get("decimals")
        );
        $extra[$i+2] = round(
            abs($high - $low) / 2,
            $dataChan->get("decimals")
        );
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function encode()
    {
        $data  = "";
        $data .= $this->encodeInt($this->getExtra(0), 1);
        $data .= $this->encodeInt($this->getExtra(1), 1);
        $data .= $this->encodeInt($this->getExtra(2), 2);
        $output = $this->process()->device()->controlChannels()->controlChannel(
            $this->getExtra(1)
        );
        $data .= $this->encodeInt($output->get("min"), 4);
        $data .= $this->encodeInt($output->get("max"), 4);
        $data .= $this->_encodeChannels();
        return $data;
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @return array
    */
    private function _encodeChannels()
    {
        $channels = $this->process()->device()->dataChannels();
        $data = "";
        // Limiters
        for ($i = 3; $i < 9; $i += 3) {
            $chan = $this->getExtra($i);
            $chan = (int)$chan;
            if ($chan == 0xFF) {
                $data .= "FF"."FFFFFFFF"."FFFFFFFF";
                continue;
            }
            $dataChan  = $channels->dataChannel($chan);
            $epChan    = (int)$dataChan->get("epChannel");
            $low = $dataChan->encode(
                (float)$this->getExtra($i+1)
            );
            $low = substr($low."00000000", 0, 8);
            $high = $dataChan->encode(
                (float)$this->getExtra($i+2)
            );
            $high = substr($high."00000000", 0, 8);
            $data .= $this->encodeInt($epChan, 1).$low.$high;
        }
        // Setpoint
        $chan = $this->getExtra($i);
        $chan = (int)$chan;
        $dataChan  = $channels->dataChannel($chan);
        $epChan    = (int)$dataChan->get("epChannel");
        $setpoint  = (float)$this->getExtra($i+1);
        $tolerance = (float)$this->getExtra($i+2);
        $low = $dataChan->encode(
            $setpoint - $tolerance
        );
        $low = substr($low."00000000", 0, 8);
        $high = $dataChan->encode(
            $setpoint + $tolerance
        );
        $high = substr($high."00000000", 0, 8);
        $data .= $this->encodeInt($epChan, 1).$low.$high;
        return $data;
    }

}


?>
