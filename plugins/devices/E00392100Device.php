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
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is a required class */
require_once dirname(__FILE__).'/../../base/DeviceDriverLoadableBase.php';
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
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class E00392100Device extends DeviceDriverLoadableBase
    implements DeviceDriverInterface
{
    /** The placeholder for the reading the downstream units from a controller */
    const COMMAND_READDOWNSTREAM = "56";

    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00392100",
        "Type" => "device",
        "Class" => "E00392100Device",
        "Flags" => array(
            "0039-20-01-C:0039-21-01-A:DEFAULT",
            "0039-20-14-C:0039-21-02-A:DEFAULT",
        ),
    );
    /** This is where we store the actual view sensors */
    protected $actualSensors = null;
    /** @var This is to register the class */
    protected $outputLabels = array(
        "PhysicalSensors" => "Physical Sensors",
        "VirtualSensors" => "Virtual Sensors",
        "CPU" => "CPU",
        "SensorConfig" => "Sensor Configuration",
        "bootloader" => "In Bootloader",
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
        $this->myDriver->DriverInfo["PhysicalSensors"] = 6;
        $this->myDriver->DriverInfo["VirtualSensors"] = 4;
        $this->fromSetupString($string);
    }
    /**
    * Says whether this device is a controller board or not
    *
    * This default always returns false.   This is a controller baord, so we
    * return true
    *
    * @return bool False
    */
    public function controller()
    {
        return true;
    }
    /**
    * Reads the setup out of the device.
    *
    * If the device is using outdated firmware we have to
    *
    * @return bool True on success, False on failure
    */
    public function readSetup()
    {
        $ret = $this->readConfig();
        if ($ret) {
            if ($this->myDriver->Driver !== self::$registerPlugin["Name"]) {
                // Reset config time so this device is checked again.
                //$this->readSetupTimeReset();
                // Try to just run the application first
                $this->runApplication();
                // Wrong Driver  We should exit with a failure unless the setup
                // returns us with the right one
                $ret = $this->readConfig();
                if ($this->myDriver->Driver !== self::$registerPlugin["Name"]) {
                    $this->vprint(
                        "Running the Application:  Failed",
                        HUGnetClass::VPRINT_NORMAL
                    );
                    $ret = null;
                } else {
                    $this->vprint(
                        "Running the Application:  Succeeded",
                        HUGnetClass::VPRINT_NORMAL
                    );
                }
            }
        }
        if ($ret) {
            $this->_setFirmware();
            $ver = $this->myFirmware->compareVersion($this->myDriver->FWVersion);
            if ($ver < 0) {
                $this->vprint(
                    "Found new firmware ".$this->myFirmware->FWPartNum
                    ." v".$this->myFirmware->Version
                    ." > v".$this->myDriver->FWVersion,
                    HUGnetClass::VPRINT_NORMAL
                );

                // Crash the running program so the board can be reloaded
                $this->runBootloader();
                // This forces us to not just run the application again
                $this->readConfig();
                // This is because the program needs to be reloaded.  It can
                // only be reloaded if it is using the 00392101 driver.
                $ret = null;
            }
        }
        if ($ret) {
            // This doesn't count towards whether the config passes or fails because
            // the packet is currently too big to go through the new controller
            // board.  If it works it works.  If it doesn't it doesn't.
            $this->readDownstreamDevices();
        }
        return $this->setLastConfig($ret);
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    protected function readDownstreamDevices()
    {
        for ($key = 0; $key < 2; $key++) {
            // Send the packet out
            $ret = $this->sendPkt(
                self::COMMAND_READDOWNSTREAM,
                $this->stringSize($key, 2)
            );
            $downstream = &$this->myDriver->params->DriverInfo["Downstream"];
            if (is_string($ret) && !empty($ret)) {
                $downstream[(int)$key] = array();
                $dev = new DeviceContainer();
                $devs = str_split($ret, 6);
                foreach ($devs as $d) {
                    $dev->clearData();
                    $id = hexdec($d);
                    if (!empty($id)) {
                        $downstream[(int)$key][] = $d;
                        $dev->getRow($id);
                        $dev->ControllerKey = $this->myDriver->id;
                        $dev->ControllerIndex = $key;
                        $dev->updateRow(array("ControllerKey", "ControllerIndex"));
                    }
                }
                $ret = true;;
            }
        }
        return (bool) $ret;
    }
    /**
    * Reads the setup out of the device
    *
    * @return bool True on success, False on failure
    */
    private function _setFirmware()
    {
        $this->myFirmware->clearData();
        $this->myFirmware->fromArray(
            array(
                "HWPartNum" => $this->myDriver->HWPartNum,
                "FWPartNum" => $this->myDriver->FWPartNum,
            )
        );
        $this->myFirmware->getLatest();
    }
    /**
    * This always forces the sensors to the same thing (world view)
    *
    * Here is the actual sensor array (actual view):
    *    Input 0: HUGnet2 Current
    *    Input 1: HUGnet2 Temp
    *    Input 2: HUGnet2 Voltage Low
    *    Input 3: HUGnet2 Voltage High
    *    Input 4: HUGnet1 Voltage High
    *    Input 5: HUGnet1 Voltage Low
    *    Input 6: HUGnet1 Temp
    *    Input 7: HUGnet1 Current
    *
    * This is what we put forward to the world (world view):
    *    Output 0: HUGnet1 Voltage
    *    Output 1: HUGnet1 Current
    *    Output 2: HUGnet1 Temp
    *    Output 3: HUGnet2 Voltage
    *    Output 4: HUGnet2 Current
    *    Output 5: HUGnet2 Temp
    *
    * @param string $string This is totally ignored.
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function fromSetupString($string)
    {
        $this->myDriver->DriverInfo["TimeConstant"] = 1;
        if (is_object($this->myDriver->sensors)) {
            $this->myDriver->sensors->Sensors = 6;
            $this->myDriver->sensors->fromTypeArray(
                array(
                    0 => $this->_voltageSensor("HUGnet 1 Voltage"),
                    1 => $this->_currentSensor("HUGnet 1 Current"),
                    2 => $this->_tempSensor("HUGnet 1 FET Temperature"),
                    3 => $this->_voltageSensor("HUGnet 2 Voltage"),
                    4 => $this->_currentSensor("HUGnet 2 Current"),
                    5 => $this->_tempSensor("HUGnet 2 FET Temperature"),
                )
            );
        }
    }

    /**
    * This crunches the actual numbers for the sensor data
    *
    * Here is the actual sensor array (actual view):
    *    Input 0: HUGnet2 Current
    *    Input 1: HUGnet2 Temp
    *    Input 2: HUGnet2 Voltage Low
    *    Input 3: HUGnet2 Voltage High
    *    Input 4: HUGnet1 Voltage High
    *    Input 5: HUGnet1 Voltage Low
    *    Input 6: HUGnet1 Temp
    *    Input 7: HUGnet1 Current
    *
    * This is what we put forward to the world (world view):
    *    Output 0: HUGnet1 Voltage
    *    Output 1: HUGnet1 Current
    *    Output 2: HUGnet1 Temp
    *    Output 3: HUGnet2 Voltage
    *    Output 4: HUGnet2 Current
    *    Output 5: HUGnet2 Temp
    *
    * @param string $string  The string of sensor data
    * @param string $command The command that was used to get the data
    * @param float  $deltaT  The time difference between this packet and the next
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function decodeData($string, $command="", $deltaT=0)
    {
        return $this->_decodeSensorData($string, $deltaT);
    }
    /**
    * This decodes the senor data
    *
    * @param string $string The string of sensor data
    * @param float  $deltaT The time difference between this packet and the next
    *
    * @return null
    */
    private function _decodeSensorData($string, $deltaT)
    {
        $this->myDriver->DriverInfo["TimeConstant"] = 1;
        if (!is_object($this->actualSensors)) {
            $this->actualSensors = new DeviceSensorsContainer(
                array(
                    "PhysicalSensors" => 8,
                    "VirtualSensors" => 0,
                    "forceSensors" => true,
                    0 => $this->_currentSensor("HUGnet 1 Current"),
                    1 => $this->_tempSensor("HUGnet 1 FET Temperature"),
                    2 => $this->_voltageSensor("HUGnet 1 Voltage Low"),
                    3 => $this->_voltageSensor("HUGnet 1 Voltage High"),
                    4 => $this->_voltageSensor("HUGnet 2 Voltage High"),
                    5 => $this->_voltageSensor("HUGnet 2 Voltage Low"),
                    6 => $this->_tempSensor("HUGnet 2 FET Temperature"),
                    7 => $this->_currentSensor("HUGnet 2 Current"),
                ),
                $this->myDriver
            );
        }
        $data = $this->_decodeSensorString($string);
        $actual = $this->actualSensors->decodeSensorData($data);
        $ret = array(
            "DataIndex" => $data["DataIndex"],
            "timeConstant" => 1,
            "deltaT" => $deltaT,
            0 => $actual[3],
            1 => $actual[0],
            2 => $actual[1],
            3 => $actual[4],
            4 => $actual[7],
            5 => $actual[6],
        );
        $ret[0]["value"] -= $actual[2]["value"];
        $ret[3]["value"] -= $actual[5]["value"];
        return $ret;
    }

    /**
    * Decodes the sensor string
    *
    * @param string $string The string of sensor data
    *
    * @return null
    */
    private function _decodeSensorString($string)
    {
        $ret = $this->sensorStringArrayToInts(str_split(substr($string, 2), 4));
        $ret["DataIndex"] = hexdec(substr($string, 0, 2));
        return $ret;
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
            "type" => "Controller",
            "location" => $location,
            "extra" => array(180, 27),
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
            "type" => "Controller",
            "location" => $location,
            "extra" => array(0.5, 7),
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
    private function _tempSensor($location)
    {
        return array(
            "id" => 0x02,
            "type" => "BCTherm2322640",
            "location" => $location,
            "extra" => array(100, 10),
            "bound" => true,
        );

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
        $ret["CPU"] = "Atmel Mega16";
        $ret["SensorConfig"] = "Fixed";
        $ret["bootloader"] = "No";
        return $ret;
    }
}

?>
