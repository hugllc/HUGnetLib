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
            0 => "Priority",
            1 => "Control Channel",
            2 => "Data Channel",
            3 => "Input Offset",
            4 => "Setpoint",
            5 => "Error Threshold",
            6 => "P",
            7 => "I",
            8 => "D",
            9 => "Output Offset",
        ),
        "extraDefault" => array(
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            4, array(), array(), 15, 15, 15, 15, 15, 15, 15
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
            $ret[1] = $this->process()->device()->controlChannels()->select();
            $ret[2] = $this->process()->device()->dataChannels()->select(
                array(), true
            );
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
        $extra[0] = $this->decodeInt($str, 1);
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
        $index += 8;
        $extra[9] = $this->decodeInt($str, 4, true);
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
        $data .= $this->encodeInt($this->getExtra(0), 1);
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
        $data .= $this->encodeInt($output->get("min"), 4);
        $data .= $this->encodeInt($output->get("max"), 4);
        return $data;
    }

}


?>
