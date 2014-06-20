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
class PIDProcess extends \HUGnet\devices\processTable\Driver
    implements \HUGnet\devices\processTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "PID Process",
        "shortName" => "PID",
        "extraText" => array(
            0 => "Control Updates / Sec",
            1 => "Control Channel",
            2 => "Data Channel",
            3 => "Input Offset",
            4 => "Setpoint",
            5 => "Error Threshold",
            6 => "P",
            7 => "I",
            8 => "D",
            9 => "Output Offset",
            10 => "Control Chan Min",
            11 => "Control Chan Max",
            12 => "Sign Control Channel",
            13 => "Sign Bit Sense",
        ),
        "extraDesc" => array(
            0 => "The max number of times this should run each second (0.5 - 128)",
            1 => "The control channel to use",
            2 => "The data channel to use for the control",
            3 => "Added to the data channel before the check",
            4 => "(Units for data channel) The set point to use for the control",
            5 => "Inside this limit, I and D are ignored",
            6 => "Proportional Coefficient (Shifted down 27 bits in endpoint)",
            7 => "Integral Coefficient (Shifted down 27 bits in endpoint)",
            8 => "Differential Coefficient (Shifted down 16 bits in endpoint)",
            9 => "Added to the control before the channel is set",
            "The minimum value for the control channel.  Empty means use default",
            "The maximum value for the control channel.  Empty means use default",
            12 => "The control channel to put the sign bit into",
            13 => "Normal means the sign bit output as is.  Inverted inverts it",
        ),
        "extraNames" => array(
            "frequency"     => 0,
            "controlchan0"  => 1,
            "datachan0"     => 2,
            "inputoffset"   => 3,
            "setpoint"      => 4,
            "errorthresh"   => 5,
            "p"             => 6,
            "i"             => 7,
            "d"             => 8,
            "outputoffset"  => 9,
            "min"           => 10,
            "max"           => 11,
            "signcontrolchan" => 12,
            "signbitsense"  => 13,
        ),
        "extraDefault" => array(
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, "", "", 0, 0xFF
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            4, array(), array(), 15, 15, 15, 15, 15, 15, 15, 7, 7, array(), 
            array(0xFF => "No sign bit output", 1 => "Normal", 2 => "Inverted")
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
            $cchans = $this->process()->device()->controlChannels()->select();
            $ret[1] = $cchans;
            $ret[2] = $this->process()->device()->dataChannels()->select(
                array(), true
            );
            $ret[12] = $cchans;
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
        $index = 0;
        $str   = substr($string, $index, 2);
        $extra[0] = $this->decodePriority($str);
        $index += 2;
        $str   = substr($string, $index, 2);
        $extra[1] = $this->decodeInt($str, 1);
        $index += 2;
        $str   = substr($string, $index, 2);
        $epChan = $this->decodeInt($str, 1);
        $dataChan = $this->process()->device()->dataChannels()->epChannel(
            $epChan
        );
        $extra[2] = $dataChan->get("channel");
        $index += 2;
        $str   = substr($string, $index, 8);
        $index += 8;
        $extra[3] = $this->decodeInt($str, 4, true);
        $str   = substr($string, $index, 8);
        $index += 8;
        $extra[4] = $dataChan->decode($str);
        $str   = substr($string, $index, 4);
        $index += 4;
        $extra[5] = $this->decodeInt($str, 2);
        $str   = substr($string, $index, 8);
        $index += 8;
        $extra[6] = round($this->decodeInt($str, 4, true)/(1<<16), 6);
        $str   = substr($string, $index, 8);
        $index += 8;
        $extra[7] = round($this->decodeInt($str, 4, true)/(1<<16), 6);
        $str   = substr($string, $index, 8);
        $index += 8;
        $extra[8] = round($this->decodeInt($str, 4, true)/(1<<16), 6);
        $str   = substr($string, $index, 8);
        $extra[9] = $this->decodeInt($str, 4, true);
        $index += 8;
        $extra[10] = $this->decodeInt(substr($string, $index, 8), 4, true);
        $index += 8;
        $extra[11] = $this->decodeInt(substr($string, $index, 8), 4, true);
        $index += 8;
        $extra[12] = $this->decodeInt(substr($string, $index, 2), 1);
        $index += 2;
        $extra[13] = $this->decodeInt(substr($string, $index, 2), 1);
        $this->process()->set("extra", $extra);
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function encode()
    {
        $data  = "";
        $data .= $this->encodePriority($this->getExtra(0));
        $data .= $this->encodeInt($this->getExtra(1), 1);
        $dataChan = $this->process()->device()->dataChannel($this->getExtra(2));
        $data .= $this->encodeInt($dataChan->get("epChannel"), 1);
        $data .= $this->encodeInt($this->getExtra(3), 4);
        $setpoint = $dataChan->encode($this->getExtra(4));
        $data .= substr($setpoint."00000000", 0, 8);
        $data .= $this->encodeInt($this->getExtra(5), 2);
        for ($i = 6; $i < 9; $i++) {
            $value = $this->getExtra($i) *(0x10000);
            $str = $this->encodeInt((int)$value, 4);
            $data .= $str;
        }
        $data .= $this->encodeInt($this->getExtra(9), 4);

        $output = $this->process()->device()->controlChannels()->controlChannel(
            $this->getExtra(1)
        );
        $oMin = $output->get("min");
        $oMax = $output->get("max");
        $min  = $this->getExtra(10);
        if (($min === "") || ($min < $oMin) || ($min > $oMax)) {
            $min = $oMin;
        }
        $data .= $this->encodeInt($min, 4);
        $max   = $this->getExtra(11);
        if (($max === "") || ($max > $oMax) || ($max < $oMin)) {
            $max = $oMax;
        }
        $data .= $this->encodeInt($max, 4);
        $data .= $this->encodeInt($this->getExtra(12), 1);
        $data .= $this->encodeInt($this->getExtra(13), 1);
        return $data;
    }

}


?>
