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
            0  => "Control Updates / Sec",
            1  => "Control",
            2  => "Step (%)",
            3  => "Limiter 1 Data Channel",
            4  => "Limiter 1 High",
            5  => "Limiter 1 Low",
            6  => "Limiter 2 Data Channel",
            7  => "Limiter 2 High",
            8  => "Limiter 2 Low",
            9  => "Data Channel",
            10 => "Set Point",
            11 => "Tolerance",
            12 => "Control Chan Min",
            13 => "Control Chan Max",
        ),
        "extraDesc" => array(
            "The max number of times this should run each second (0.5 - 128)",
            "The control channel to use",
            "The amount added or subracted from the control channel.  % of the
             full scale of the output selected.",
            "The data channel to use for the first limiter",
            "(Units for data channel) The maximum for the first limiter",
            "(Units for data channel) The minimum for the first limiter",
            "The data channel to use for the second limiter",
            "(Units for data channel) The maximum for the second limiter",
            "(Units for data channel) The minimum for the second limiter",
            "The data channel to use for the control",
            "(Units for data channel) The set point to use for the control",
            "(Units for data channel) The tolerance to use for the control",
            "The minimum value for the control channel.  Empty means use default",
            "The maximum value for the control channel.  Empty means use default",
        ),
        "extraNames" => array(
            "frequency"     => 0,
            "controlchan0"  => 1,
            "step"          => 2,
            "limit1channel" => 3,
            "limit1high"    => 4,
            "limit1low"     => 5,
            "limit2channel" => 6,
            "limit2high"    => 7,
            "limit2low"     => 8,
            "datachan0"     => 9,
            "setpoint"      => 10,
            "tolerance"     => 11,
            "min"           => 12,
            "max"           => 13,
        ),
        "extraDefault" => array(
            34, 0, 2, 0xFF, 0, 0, 0xFF, 0, 0, 0, 0, 0.01, "", ""
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            4, array(), 10, array(), 15, 15, array(), 15, 15, array(), 15, 15, 7, 7
        ),
        "requires" => array("DC", "CC", "FREQ"),
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
                array()
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
        $extra[0] = $this->decodePriority(substr($string, 0, 2));
        $extra[1] = $this->decodeInt(substr($string, 2, 2), 1);
        $step = $this->decodeInt(substr($string, 4, 4), 2, true);
        $output = $this->process()->device()->controlChannels()->controlChannel(
            $this->getExtra(1)
        );
        $split = $output->get("max") - $output->get("min");
        $extra[2]  = 100.0 * round($step / $split, 8);
        $extra[12] = $this->decodeInt(substr($string, 8, 8), 4, true);
        $extra[13] = $this->decodeInt(substr($string, 16, 8), 4, true);
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
                $extra[$i] = 0xFF;
                $index += 18;
                continue;
            }
            $epChan = $this->decodeInt($epChan, 1);
            $dataChan = $channels->epChannel($epChan);

            $extra[$i] = (int)$dataChan->get("channel");
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
        $set = $dataChan->decode(
            substr($string, $index, 8)
        );
        $index += 8;
        $extra[$i+1] = round(
            $set,
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
        $output = $this->process()->device()->controlChannels()->controlChannel(
            $this->getExtra(1)
        );
        $oMin = $output->get("min");
        $oMax  = $output->get("max");
        $data .= $this->encodePriority($this->getExtra(0));
        $data .= $this->encodeInt($this->getExtra(1), 1);
        $perc  = $this->getExtra(2);
        if ($perc > 50) {
            $perc = 50;
        } else if ($perc < -50) {
            $perc = -50;
        }
        $step  = ($perc / 100) * ($oMax - $oMin);
        if ($step > 32767) {
            $step = 32767;
        } else if ($step < -32767) {
            $step = -32767;
        }
        $data .= $this->encodeInt($step, 2);
        $min   = $this->getExtra(12);
        if (($min === "") || ($min < $oMin)) {
            $min = $oMin;
        }
        $data .= $this->encodeInt($min, 4);
        $max   = $this->getExtra(13);
        if (($max === "") || ($max > $oMax)) {
            $max = $oMax;
        }
        $data .= $this->encodeInt($max, 4);
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
            $high = $dataChan->encode(
                (float)$this->getExtra($i+1)
            );
            $high = substr($high."00000000", 0, 8);
            $low = $dataChan->encode(
                (float)$this->getExtra($i+2)
            );
            $low = substr($low."00000000", 0, 8);
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
        $set = $dataChan->encode(
            $setpoint
        );
        $set = substr($set."00000000", 0, 8);
        
        $data .= $this->encodeInt($epChan, 1).$low.$high.$set;
        return $data;
    }

}


?>
