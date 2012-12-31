<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @copyright  2012 Hunt Utilities Group, LLC
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

/**
 * Driver for reading voltage based pressure sensors
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class PIDProcess extends \HUGnet\devices\processTable\Driver
{
    /*
    const CGND_OFFSET = 0.95;
    const STEP_VOLTAGE = 0.0006103515625;  // 2.5 / 4096
    const MAX_VOLTAGE = 1.2;
    */
    /**
     * The minimum value for the DAC
     *  (int)(self::CGND_OFFSET / self::STEP_VOLTAGE);
     */
    private $_min = 1556;
    /**
     * The maximum value for the DAC
     *  (int)((self::MAX_VOLTAGE + self::CGND_OFFSET) / self::STEP_VOLTAGE);
     */
    private $_max = 3522;
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "PID Process",
        "shortName" => "PID",
        "extraText" => array(
            "Priority",
            "Control Channel",
            "Data Channel",
            "Input Offset",
            "Setpoint",
            "P",
            "I",
            "D",
            "Output Offset",
        ),
        "extraDefault" => array(
            0, 0, 0, 0, 0, 0, 0, 0, 0
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            4, array(), array(), 15, 15, 15, 15, 15, 15
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
            $ret[2] = $this->process()->device()->dataChannels()->select();
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
        for ($i = 0; $i < 3; $i++) {
            $str   = substr($string, $index, 2);
            $extra[$i] = $this->_getProcessIntStr($str, 1);
            $index += 2;
        }
        $str   = substr($string, $index, 8);
        $index += 8;
        $extra[3] = $this->_getProcessIntStr($str, 4);
        $str   = substr($string, $index, 8);
        $index += 8;
        $extra[4] = $this->_getProcessIntStr($str, 4);
        $str   = substr($string, $index, 8);
        $index += 8;
        $extra[5] = round($this->_getProcessIntStr($str, 4)/(1<<16), 6);
        $str   = substr($string, $index, 8);
        $index += 8;
        $extra[6] = round($this->_getProcessIntStr($str, 4)/(1<<16), 6);
        $str   = substr($string, $index, 8);
        $index += 8;
        $extra[7] = round($this->_getProcessIntStr($str, 4)/(1<<16), 6);
        $str   = substr($string, $index, 8);
        $index += 8;
        $extra[8] = $this->_getProcessIntStr($str, 4);
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
        for ($i = 0; $i < 3; $i++) {
            $data .= $this->_getProcessStrInt($this->getExtra($i), 1);
        }
        $data .= $this->_getProcessStrInt($this->getExtra(3), 4);
        $data .= $this->_getProcessStrInt($this->getExtra(4), 4);
        $data .= $this->_getProcessStrInt($this->getExtra(5)*(1<<16), 4);
        $data .= $this->_getProcessStrInt($this->getExtra(6)*(1<<16), 4);
        $data .= $this->_getProcessStrInt($this->getExtra(7)*(1<<16), 4);
        $data .= $this->_getProcessStrInt($this->getExtra(8), 4);
        $data .= $this->_getProcessStrInt(0, 4);
        $data .= $this->_getProcessStrInt(4096, 4);
        return $data;
    }
    /**
    * This builds the string for the levelholder.
    *
    * @param int $val   The value to use
    * @param int $bytes The number of bytes to set
    *
    * @return string The string
    */
    private function _getProcessStrInt($val, $bytes = 4)
    {
        $val = (int)$val;
        for ($i = 0; $i < $bytes; $i++) {
            $str .= sprintf(
                "%02X",
                ($val >> ($i * 8)) & 0xFF
            );
        }
        return $str;

    }
    /**
    * This builds the string for the levelholder.
    *
    * @param string $val   The value to use
    * @param int    $bytes The number of bytes to set
    *
    * @return string The string
    */
    private function _getProcessIntStr($val, $bytes = 4)
    {
        $int = 0;
        for ($i = 0; $i < $bytes; $i++) {
            $int += hexdec(substr($val, ($i * 2), 2))<<($i * 8);
        }
        return $int;

    }

}


?>
