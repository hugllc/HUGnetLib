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
class LevelHolderProcess extends \HUGnet\devices\processTable\Driver
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
        "longName" => "LevelHolder Process",
        "shortName" => "LevelHolder",
        "extraText" => array(
            "Priority",
            "Control",
            "Step",
            "Data Channel 0",
            "Set Point 0",
            "Tolerance 0",
            "Data Channel 1",
            "Set Point 1",
            "Tolerance 1",
            "Data Channel 2",
            "Set Point 2",
            "Tolerance 2",
        ),
        "extraDefault" => array(
            34, 0, 2, "", 0, 0.01, "", 0, 0.01, "", 0, 0.01,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            4, array(), 3, array(), 15, 15, array(), 15, 15, array(), 15, 15
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
            $data = $dataChans->select(array("" => "None"), true);
            $ret[1] = $control;
            $ret[3] = $dataChans->select(array(), true);
            $ret[6] = $data;
            $ret[9] = $data;
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
        $extra[0] = $this->_getProcessIntStr(substr($string, 0, 2), 1);
        $extra[1] = $this->_getProcessIntStr(substr($string, 2, 2), 1);
        $extra[2] = $this->_getProcessIntStr(substr($string, 4, 2), 1);
        // min is hard coded at substr($string, 6, 8)
        // max is hard coded at substr($string, 14, 8)
        $this->_decodeChannels(substr($string, 22), $extra);
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
        for ($i = 3; $i < count($this->params["extraText"]); $i += 3) {
            $epChan = substr($string, $index, 2);
            if (($epChan == "FF") || ($epChan === false)) {
                // Empty string or slot
                unset($extra[$i]);
                unset($extra[$i+1]);
                unset($extra[$i+2]);
                continue;
            }
            $epChan = $this->_getProcessIntStr($epChan, 1);
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
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function encode()
    {
        $data  = "";
        $data .= $this->_getProcessStrInt($this->getExtra(0), 1);
        $data .= $this->_getProcessStrInt($this->getExtra(1), 1);
        $data .= $this->_getProcessStrInt($this->getExtra(2), 1);
        $data .= $this->_getProcessStrInt($this->_min, 4);
        $data .= $this->_getProcessStrInt($this->_max, 4);
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
        for ($i = 3; $i < count($this->params["extraText"]); $i += 3) {
            $chan = $this->getExtra($i);
            if ((strlen((string)$chan) == 0) || (!is_numeric($chan))) {
                break;
            }
            $chan = (int)$chan;
            $dataChan  = $channels->dataChannel($chan);
            $epChan    = (int)$dataChan->get("epChannel");
            $setpoint  = (float)$this->getExtra($i+1);
            $tolerance = (float)$this->getExtra($i+2);
            $low = $dataChan->encode(
                $setpoint - $tolerance
            );
            if (strlen($low) == 0) {
                break;
            }
            $high = $dataChan->encode(
                $setpoint + $tolerance
            );
            if (strlen($high) == 0) {
                break;
            }
            $data .= $this->_getProcessStrInt($epChan, 1).$low.$high;
        }
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
