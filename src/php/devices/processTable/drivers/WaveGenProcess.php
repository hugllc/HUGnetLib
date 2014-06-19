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
class WaveGenProcess extends \HUGnet\devices\processTable\Driver
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
        "longName" => "WaveGen Process",
        "shortName" => "WaveGen",
        "extraText" => array(
            0 => "Control Updates / Sec",
            1 => "Control",
            2 => "On Time (s)",
            3 => "Off Time (s)",
            4 => "On Value",
            5 => "Off Value",
            6 => "Control Chan Min",
            7 => "Control Chan Max",
        ),
        "extraDesc" => array(
            "The max number of times this should run each second (0.5 - 128)",
            "The control channel to use",
            "The time to stay on in seconds.",
            "The time to stay off in seconds.",
            "The value to write to the control channel during on time",
            "The value to write to the control channel during off time",
            "The minimum value for the control channel.  Empty means use default",
            "The maximum value for the control channel.  Empty means use default",
        ),
        "extraNames" => array(
        ),
        "extraDefault" => array(
            128, 0, 1, 1, 1, -1, "", ""
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            4, array(), 6, 6, 15, 15, 15, 15
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
        // Priority
        $extra[0] = $this->decodePriority(substr($string, $index, 2));
        $index += 2;
        // Control
        $extra[1] = $this->decodeInt(substr($string, $index, 2), 1);
        $index += 2;
        // On Time
        $extra[2] = $this->decodeInt(substr($string, $index, 4), 2);
        $index += 4;
        // Off Time
        $extra[3] = $this->decodeInt(substr($string, $index, 4), 2);
        $index += 4;
        // On Value
        $extra[4] = $this->decodeInt(substr($string, $index, 8), 4, true);
        $index += 8;
        // Off Value
        $extra[5] = $this->decodeInt(substr($string, $index, 8), 4, true);
        $index += 8;
        // Min
        $extra[6] = $this->decodeInt(substr($string, $index, 8), 4, true);
        $index += 8;
        // Max
        $extra[7] = $this->decodeInt(substr($string, $index, 8), 4, true);
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
        $output = $this->process()->device()->controlChannels()->controlChannel(
            $this->getExtra(1)
        );
        $oMin  = $output->get("min");
        $oMax  = $output->get("max");
        $data .= $this->encodePriority($this->getExtra(0));
        $data .= $this->encodeInt($this->getExtra(1), 1);
        $data .= $this->encodeInt($this->getExtra(2), 2);
        $data .= $this->encodeInt($this->getExtra(3), 2);
        $data .= $this->encodeInt($this->getExtra(4), 4);
        $data .= $this->encodeInt($this->getExtra(5), 4);
        $min   = $this->getExtra(6);
        if (($min === "") || ($min < $oMin)) {
            $min = $oMin;
        }
        $data .= $this->encodeInt($min, 4);
        $max   = $this->getExtra(7);
        if (($max === "") || ($max > $oMax)) {
            $max = $oMax;
        }
        $data .= $this->encodeInt($max, 4);
        return $data;
    }

}


?>
