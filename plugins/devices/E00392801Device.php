<?php
/**
 * This is the driver code for the HUGnet data collector (0039-26).
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @subpackage PluginsDevices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is a required class */
require_once dirname(__FILE__).'/E00392800Device.php';
/** This is a required class */
require_once dirname(__FILE__).'/../../interfaces/DeviceDriverInterface.php';
/** This is a required class */
require_once dirname(__FILE__).'/../../interfaces/PacketConsumerInterface.php';

/**
 * Driver for the polling script (0039-26-01-P)
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsDevices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class E00392801Device extends E00392800Device
    implements DeviceDriverInterface
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00392801",
        "Type" => "device",
        "Class" => "E00392801Device",
        "Flags" => array(
            "0039-20-18-C:0039-28-01-A:DEFAULT",
            "0039-20-18-C:DEFAULT:DEFAULT",
        ),
    );
    /** @var This is to register the class */
    protected $outputLabels = array(
        "PhysicalSensors" => "Physical Sensors",
        "VirtualSensors" => "Virtual Sensors",
        "CPU" => "CPU",
        "SensorConfig" => "Sensor Configuration",
        "c0-0" => "Output 1 c0",
        "c1-0" => "Output 1 c1",
        "c0-1" => "Output 2 c0",
        "c1-1" => "Output 2 c1",
        "c0-2" => "Output 3 c0",
        "c1-2" => "Output 3 c1",
        "c0-3" => "Output 4 c0",
        "c1-3" => "Output 4 c1",
        "alarm0" => "Alarm Threshold 1",
        "alarm1" => "Alarm Threshold 2",
        "alarm2" => "Alarm Threshold 3",
        "alarm3" => "Alarm Threshold 4",
    );
    /**
    * Builds the class
    *
    * @param object &$obj   The object that is registering us
    * @param mixed  $string The string we will use to build the object
    *
    * @return null
    */
    public function __construct(&$obj, $string = "")
    {
        parent::__construct($obj, $string);
        $this->myDriver->DriverInfo["PhysicalSensors"] = 16;
        $this->myDriver->DriverInfo["VirtualSensors"] = 4;
        $this->fromSetupString($string);
    }
    /**
    * This always forces the sensors to the same thing (world view)
    *
    * The last 8 sensors are actually set to output.  The only thing that they
    * can be is pulse counters.
    *
    * @param string $string This is totally ignored.
    *
    * @return null
    */
    public function fromSetupString($string)
    {
        parent::fromSetupString($string);
        $con = substr($string, 34, 16);
        for ($i = 0; $i < 8; $i+=2) {
            $out = (int)($i / 2);
            $this->myDriver->DriverInfo["c0-".$out] = hexdec(
                substr($con, $i * 2, 2)
            );
            $this->myDriver->DriverInfo["c1-".$out] = hexdec(
                substr($con, (($i * 2) + 2), 2)
            );
        }
        $alarm = substr($string, 50, 16);
        for ($i = 0; $i < 8; $i+=2) {
            $out = (int)($i / 2);
            $low = hexdec(
                substr($alarm, $i * 2, 2)
            );
            $high = hexdec(
                substr($alarm, (($i * 2) + 2), 2)
            );
            $val = $low + ($high * 0x100);
            if (is_object($this->myDriver->sensors)) {
                $val = (0xFFFF - ($val * 64));
                $val = $this->myDriver->sensor($i+1)->getReading($val);
                $this->myDriver->sensor($i+1)->convertUnits($val);
                $val = ((float)$val)." ".$this->myDriver->sensor($i+1)->units;
            }
            $this->myDriver->DriverInfo["alarm".$out] = $val;
        }
        if (is_object($this->myDriver->sensors)) {
            for ($i = 8; $i < 12; $i++) {
                if (empty($this->myDriver->sensor($i)->location)) {
                    $this->myDriver->sensor($i)->location = "Output ".($i - 7);
                }
            }
            for ($i = 12; $i < 16; $i++) {
                if (empty($this->myDriver->sensor($i)->location)) {
                    $this->myDriver->sensor($i)->location = "Alarm ".($i - 11);
                }
            }
        }
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    */
    public function toOutput($cols = null)
    {
        $ret = parent::toOutput($cols);
        $ret["CPU"] = "Atmel Mega168";
        $ret["SensorConfig"] = "1-8 analog, 9-16 digital";
        return $ret;
    }

}

?>
