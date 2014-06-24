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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class SolarPanelProcess extends \HUGnet\devices\processTable\Driver
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
        "longName" => "SolarPanel Process",
        "shortName" => "SolarPanel",
        "extraText" => array(
            0 => "Control Updates / Sec",
            1 => "Tank Temperature Input",
            2 => "Panel Temperature Input",
            3 => "Pump Control",
            4 => "Alarm Control",
            5 => "On Constant",
            6 => "Off Constant",
            7 => "Alarm Threshold",
        ),
        "extraDesc" => array(
            "The max number of times this should run each second (0.5 - 128)",
            "Input with the tank temperature.",
            "Input with the panel temperature",
            "The pump control output",
            "The alarm control output",
            "The constant for the on hysteresis.",
            "The constant for the off hysteresis.",
            "The temperature where the tank is too hot.",
        ),
        "extraNames" => array(
            "frequency"    => 0,
            "datachan0"    => 1,
            "datachan1"    => 2,
            "controlchan0" => 3,
            "controlchan1" => 4,
            "onconstant"   => 5,
            "offconstant"  => 6,
            "alarmthresh"  => 7,
        ),
        "extraDefault" => array(
            128, 0, 0, 0, 0, 8, 32, 150
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            0 => 4, 
            1 => array(),
            2 => array(),
            3 => array(),
            4 => array(),
            5 => 10,
            6 => 10,
            7 => 10,
        ),
        "requires" => array("DC", "DC", "CC", "CC", "FREQ"),
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
            $control = $this->process()->device()->controlChannels()->select();
            $dataChans = $this->process()->device()->dataChannels()->select();
            $ret[1] = $dataChans;
            $ret[2] = $dataChans;
            $ret[3] = $control;
            $ret[4] = $control;
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
        $channels = $this->process()->device()->dataChannels();
        $extra = (array)$this->process()->get("extra");
        $index = 0;
        // Priority
        $extra[0] = $this->decodePriority(substr($string, $index, 2));
        $index += 2;
        // Tank Temp
        $epChan = $this->decodeInt(substr($string, $index, 2), 1);
        $dataChan = $channels->epChannel($epChan);
        $extra[1] = (int)$dataChan->get("channel");
        $index += 2;
        // Panel Temp
        $epChan = $this->decodeInt(substr($string, $index, 2), 1);
        $dataChan = $channels->epChannel($epChan);
        $extra[2] = (int)$dataChan->get("channel");
        $index += 2;
        // Pump Control
        $extra[3] = $this->decodeInt(substr($string, $index, 2), 1);
        $index += 2;
        // Alarm Control
        $extra[4] = $this->decodeInt(substr($string, $index, 2), 1);
        $index += 2;
        // Contant 0
        $extra[5] = $this->decodeInt(substr($string, $index, 4), 2);
        $index += 4;
        // Constant 1
        $extra[6] = $this->decodeInt(substr($string, $index, 4), 2);
        $index += 4;
        // Alarm Thresh
        $thresh = $this->decodeInt(substr($string, $index, 4), 2);
        $thresh = (0xFFFF - ($thresh<<6));
        $dataChan = $channels->dataChannel($extra[1]);
        $extra[7] = $dataChan->decode(
            (float)$thresh
        );
        $this->process()->set("extra", $extra);
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function encode()
    {
        $channels = $this->process()->device()->dataChannels();
        $data  = "";
        $chan   = $this->getExtra(1);
        $data  .= $this->encodePriority($this->getExtra(0));
        $data  .= $this->encodeInt($chan, 1);
        $data  .= $this->encodeInt($this->getExtra(2), 1);
        $data  .= $this->encodeInt($this->getExtra(3), 1);
        $data  .= $this->encodeInt($this->getExtra(4), 1);
        $data  .= $this->encodeInt($this->getExtra(5), 2);
        $data  .= $this->encodeInt($this->getExtra(6), 2);
        $dataChan = $channels->dataChannel($chan);
        $epChan   = (int)$dataChan->get("epChannel");
        $thresh   = $dataChan->encode(
            (float)$this->getExtra(7)
        );
        $thresh   = (0xFFFF - $thresh)/64;
        $data  .= $this->encodeInt($thresh, 2);
        return $data;
    }

}


?>
