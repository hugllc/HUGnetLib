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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is a required class */
require_once dirname(__FILE__).'/../../base/DeviceDriverBase.php';
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class E00391201Device extends DeviceDriverBase
    implements DeviceDriverInterface
{
    /** @var This is the digital mode */
    const MODE_DIGITAL = 0;
    /** @var This is the digital mode */
    const MODE_HIGHZ = 1;
    /** @var This is the digital mode */
    const MODE_VOLTAGE = 2;
    /** @var This is the digital mode */
    const MODE_CURRENT = 3;
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00391201",
        "Type" => "device",
        "Class" => "E00391201Device",
        "Flags" => array(
            "0039-11-06-A:0039-12-00-A:BAD",
            "0039-11-06-A:0039-12-01-A:BAD",
            "0039-11-06-A:0039-12-02-A:BAD",
            "0039-11-06-A:0039-12-01-B:DEFAULT",
            "0039-11-06-A:0039-12-02-B:DEFAULT",
            "0039-11-07-A:0039-12-00-A:BAD",
            "0039-11-07-A:0039-12-01-A:BAD",
            "0039-11-07-A:0039-12-02-A:BAD",
            "0039-11-07-A:0039-12-01-B:DEFAULT",
            "0039-11-07-A:0039-12-02-B:DEFAULT",
            "0039-11-08-A:0039-12-01-B:DEFAULT",
            "0039-11-08-A:0039-12-02-B:DEFAULT",
            "0039-20-04-C:0039-12-02-B:DEFAULT",
            "0039-20-05-C:0039-12-02-B:DEFAULT",
        ),
    );
    /** Modes for the FET */
    var $modes = array(
        self::MODE_DIGITAL => 'Digital',
        self::MODE_HIGHZ => 'Analog - High Z',
        self::MODE_VOLTAGE => 'Analog - Voltage',
        self::MODE_CURRENT => 'Analog - Current'
    );
    /** @var This is to register the class */
    protected $outputLabels = array(
        "PhysicalSensors" => "Physical Sensors",
        "VirtualSensors" => "Virtual Sensors",
        "CPU" => "CPU",
        "SensorConfig" => "Sensor Configuration",
        "FET0Mode" => "FET0 Mode",
        "FET0Value" => "FET0 Value",
        "FET1Mode" => "FET1 Mode",
        "FET1Value" => "FET1 Value",
        "FET2Mode" => "FET2 Mode",
        "FET2Value" => "FET2 Value",
        "FET3Mode" => "FET3 Mode",
        "FET3Value" => "FET3 Value",
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
        $this->myDriver->DriverInfo["PhysicalSensors"] = 9;
        $this->myDriver->DriverInfo["VirtualSensors"] = 4;
        $this->fromSetupString($string);
    }

    /**
    * Decodes the sensor data
    *
    * @param string $string  The string of sensor data
    * @param string $command The command that was used to get the data
    * @param float  $deltaT  The time difference between this packet and the next
    * @param float  $prev    The previous record
    *
    * @return null
    */
    public function decodeData($string, $command="", $deltaT = 0, $prev = null)
    {
        $ret = parent::decodeData($string, $command, $deltaT, $prev);
        foreach (array(1, 3, 5, 7) as $key) {
            $ret[$key]["value"] = $ret[8]["value"] - $ret[$key]["value"];
        }
        return $ret;
    }
    /**
    * This always forces the sensors to the same thing (world view)
    *
    * This is always the sensor array:
    *    Input 0: Out1 Current
    *    Input 1: Out1 Voltage
    *    Input 2: Out2 Current
    *    Input 3: Out2 Voltage
    *    Input 4: Out3 Current
    *    Input 5: Out3 Voltage
    *    Input 6: Out4 Current
    *    Input 7: Out4 Voltage
    *    Input 8: Main Voltage
    *
    * @param string $string This is totally ignored.
    *
    * @return null
    */
    public function fromSetupString($string)
    {
        $Info = &$this->myDriver->DriverInfo;
        $Info["TimeConstant"] = 1;
        $Info["Setup"]        = hexdec(substr($string, 0, 2));
        for ($i = 0; $i < 4; $i++) {
            $mode           = (($Info["Setup"]>>($i*2)) & 3);
            $Info["FET".$i] = array(
                "mode"       => $mode,
                "name"       => $this->modes[$mode],
                "value"      => hexdec(substr($string, (2+(2*$i)), 2)),
                "multiplier" => hexdec(substr($string, (10+(2*$i)), 2)),
            );
        }
        if (is_object($this->myDriver->sensors)) {
            $this->myDriver->sensors->Sensors = 9;
            $this->myDriver->sensors->fromTypeArray(
                array(
                    0 => $this->_currentSensor("Out1 Current"),
                    1 => $this->_voltageSensor("Out1 Voltage"),
                    2 => $this->_currentSensor("Out2 Current"),
                    3 => $this->_voltageSensor("Out2 Voltage"),
                    4 => $this->_currentSensor("Out3 Current"),
                    5 => $this->_voltageSensor("Out3 Voltage"),
                    6 => $this->_currentSensor("Out4 Current"),
                    7 => $this->_voltageSensor("Out4 Voltage"),
                    8 => $this->_voltageSensor("Main Voltage"),
                )
            );
        }
    }
    /**
    * This returns an array to build a voltage sensor for the controller
    *
    * @param string $location The location to add to the sensors
    *
    * @return array The array of sensor information
    */
    private function _voltageSensor($location)
    {
        return array(
            "id" => 0x40,
            "type" => "fetBoard",
            "location" => $location,
            "bound" => true,
        );
    }
    /**
    * This returns an array to build a voltage sensor for the controller
    *
    * @param string $location The location to add to the sensors
    *
    * @return array The array of sensor information
    */
    private function _currentSensor($location)
    {
        return array(
            "id" => 0x50,
            "type" => "fetBoard",
            "location" => $location,
            "bound" => true,
        );

    }
    /**
    * Decodes the sensor string
    *
    * @param string $string The string of sensor data
    *
    * @return null
    */
    protected function decodeSensorString($string)
    {
        $ret = $this->sensorStringArrayToInts(str_split(substr($string, 2), 4));
        $ret["DataIndex"] = hexdec(substr($string, 0, 2));
        $ret["timeConstant"] = 1;
        return $ret;
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
        foreach (array("FET0", "FET1", "FET2", "FET3") as $fet) {
            $ret[$fet."Mode"] = $ret[$fet]["name"];
            $ret[$fet."Value"] = $this->getOutputValue($ret[$fet]);
        }
        $ret["CPU"] = "Atmel Tiny26";
        $ret["SensorConfig"] = "Fixed";
        return $ret;
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $info The info array on the fet to get the value of
    *
    * @return string
    */
    protected function getOutputValue($info)
    {
        if ($info["mode"] == self::MODE_DIGITAL) {
            if ($info["value"] >= 0x80) {
                return "On";
            } else {
                return "Off";
            }
        } else if ($info["mode"] == self::MODE_HIGHZ) {
            return "None";
        } else {
            return $info["value"]." Multiplier: ".$info["multiplier"];
        }
    }
}

?>
