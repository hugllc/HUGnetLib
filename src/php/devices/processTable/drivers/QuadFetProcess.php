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
class QuadFetProcess extends \HUGnet\devices\processTable\Driver
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
        "longName" => "QuadFet Process",
        "shortName" => "QuadFet",
        "extraText" => array(
            0 => "Control Updates / Sec",
            1 => "Port 1 Mode",
            2 => "Port 1 Threshold",
            3 => "Port 2 Mode",
            4 => "Port 2 Threshold",
            5 => "Port 3 Mode",
            6 => "Port 3 Threshold",
            7 => "Port 4 Mode",
            8 => "Port 4 Threshold",
        ),
        "extraDesc" => array(
            "The max number of times this should run each second (0.5 - 128)",
            "The mode to use for Port 1",
            "The threshold to use for Port 1",
            "The mode to use for Port 2",
            "The threshold to use for Port 2",
            "The mode to use for Port 3",
            "The threshold to use for Port 3",
            "The mode to use for Port 4",
            "The threshold to use for Port 4",
        ),
        "extraNames" => array(
            "frequency"     => 0,
            "port1mode"     => 1,
            "port1thresh"   => 2,
            "port2mode"     => 3,
            "port2thresh"   => 4,
            "port3mode"     => 5,
            "port3thresh"   => 6,
            "port4mode"     => 7,
            "port4thresh"   => 8,
        ),
        "extraDefault" => array(
            128, 0, 0, 0, 0, 0, 0, 0, 0
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            0 => 4, 
            1 => array(
                0 => "Voltage",
                1 => "Current",
            ), 
            2 => 10,
            3 => array(
                0 => "Voltage",
                1 => "Current",
            ), 
            4 => 10,
            5 => array(
                0 => "Voltage",
                1 => "Current",
            ), 
            6 => 10,
            7 => array(
                0 => "Voltage",
                1 => "Current",
            ), 
            8 => 10,
        ),
        "requires" => array("FREQ"),
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
        $output = $this->process()->device()->controlChannels()->controlChannel(0);
        $min    = $output->get("min");
        $max    = $output->get("max");
        $data  .= $this->encodePriority($this->getExtra(0));
        $dcstr  = "";
        $valstr = "";
        for ($i = 0; $i < 4; $i++) {
            $base  = $i * 2;
            $mode  = $this->getExtra($base + 1);
            $set   = $this->getExtra($base + 2);
            if ($mode == 0) {
                $base++;
            }
            $chan    = $this->process()->device()->dataChannels()->dataChannel(
                $base
            );
            $epChan  = (int)$chan->get("epChannel");
            $value   = $chan->encode($set);
            $dcstr  .= $this->encodeInt($epChan, 1);
            $valstr .= $this->encodeInt($value, 2);
        }
        $data .= $dcstr.$valstr;
        $data .= $this->encodeInt($min, 2);
        $data .= $this->encodeInt($max, 2);
        return $data;
    }

}


?>
