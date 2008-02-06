<?php
/**
 * Main class for dealing with endpoints.
 *
 * PHP version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, 
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/** Where in the config string the hardware part number starts  */
define("ENDPOINT_HW_START", 10);
/** Where in the config string the firmware part number starts  */
define("ENDPOINT_FW_START", 20);
/** Where in the config string the firmware version starts  */
define("ENDPOINT_FWV_START", 30);
/** Where in the config string the group starts  */
define("ENDPOINT_GROUP", 36);    
/** Where in the config string the boredom constant starts  */
define("ENDPOINT_BOREDOM", 42);
/** Where in the config string the configuration ends  */
define("ENDPOINT_CONFIGEND", 44);

if (!defined(HUGNET_INCLUDE_PATH)) define("HUGNET_INCLUDE_PATH", dirname(__FILE__));

require_once HUGNET_INCLUDE_PATH."/EPacket.php";
require_once HUGNET_INCLUDE_PATH."/sensor.php";
require_once HUGNET_INCLUDE_PATH."/devInfo.php";
require_once HUGNET_INCLUDE_PATH."/filter.php";
require_once HUGNET_INCLUDE_PATH."/database/device.php";
require_once HUGNET_INCLUDE_PATH."/database/gateway.php";
require_once HUGNET_INCLUDE_PATH."/database/Analysis.php";
require_once HUGNET_INCLUDE_PATH."/unitConversion.php";
require_once HUGNET_INCLUDE_PATH."/lib/plugins.inc.php";
require_once HUGNET_INCLUDE_PATH."/drivers/endpoints/eDEFAULT.php";

/**
 * Class for talking with HUGNet endpoints
 *
 *  All communication with endpoints should go through this class.
 *
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Driver
{
    /** This is the default number of decimal places to use if 
     *  it is not specified anywhere else 
      */
    var $_decimalPlaces = 2;

    /** The error number.  0 if no error occurred  */ 
    var $Errno = 0;
    /** Error String  */
    var $Error = "";
    /** An array of driver classes.  */
    var $drivers = array();
    /** The drivers and what software and hardware they encompass are mapped here  */
    var $dev = array();
    /** The display colors to use for different error codes      */
    var $ErrorColors = array(
        "DevOnBackup" => array(
            "Severity" => "Low", 
            "Description" => "Device is polled on backup server", 
            "Style" => "#00E000"
       ),
    );    

    var $device_table = "devices";
    var $analysis_table = "analysis";
    var $raw_history_table = "history_raw";
    var $packet_log_table = "PacketLog";

    /**
     * This function is simply a wrapper for device::health
     *
     * @param string $where A valid SQL where string.
     * @param int    $days  The number of days back to go
     * @param string $start The start date in 'YYYY-MM-DD HH:MM:SS' notation.
     *
     * @return string
     *
     * @see device::health
      */
    function health($where, $days = 7, $start=null) 
    {
        return $this->analysis->health($where, $days, $start);
    }
    /**
     * Wrapper for device::Diagnose
     *
     * @param array $Info devInfo array with device to use
     *
     * @return string
     *
     * @see device::Diagnose
      */
    function diagnose($Info) 
    {
        return $this->device->Diagnose($Info);
    }        
    /**
     * Runs a function using the correct driver for the endpoint
     *
     * This checks for the function in both the specific class for the endpoint
     * driver and the default driver.  If the classes or the methods don't exist
     * then it complains.
     *
     * @param array  &$Info    Infomation about the device to use
     * @param string $function The name of the function to run
     *
     * @return mixed false if the function does not exist.  Otherwise passes
     *   the function return through.
     *
      */
    function runFunction (&$Info, $function) 
    {
        if (!is_array($Info)) return false;
        $return     = array();
        $function   = trim($function);

        $use_class = $this->_methodExists($Info, $function);
        if ($use_class === false) return false;

        $args    = func_get_args();
        $args[0] = &$Info;
        unset($args[1]);
        $class  = &$this->drivers[$use_class];
        $return = call_user_func_array(array(&$class, $function), $args);

        if (is_array($return)) {        
            if (isset($return["Errno"]) || isset($return["Error"])) { 
                $this->Error = $return["Error"];        
                $this->Errno = $return["Errno"];
                $return      = false;
            }
        }
        return $return;
        
    }

    /**
     * Returns the name of the driver to use for the function given or 
     * 'false' if the function is not found
     *
     * @param array  &$Info    Infomation about the device to use
     * @param string $function The name of the function to run
     *
     * @return mixed
     */
    private function _methodExists(&$Info, $function) 
    {
        $use_driver = isset($Info["Driver"]) ? $Info['Driver'] : 'eDEFAULT';
        if ($use_driver == "eDEFAULT") {
            $use_driver        = $this->FindDriver($Info);
            $Info["OldDriver"] = $Info["Driver"];
            $Info["Driver"]    = $use_driver;
        }

        if (is_object($this->drivers[$use_driver]) 
            && method_exists($this->drivers[$use_driver], $function)) {
            return $use_driver;
        } else if (is_object($this->drivers["eDEFAULT"]) 
            && method_exists($this->drivers["eDEFAULT"], $function)) {
            return "eDEFAULT";
        } else {
            //add_debug_output("All Drivers (including eDEFAULT) failed.<BR>\n");
            return false;
        }    
    }

    /**
     *  Tries to run a function defined by what is called.  This should
     * replace all of the really short functions calling RunFunction.
     *
     * @param string $m The name of the function to call
     * @param array  $a The array of arguments
     *
     * @return mixed
      */
    function __call($m, $a) 
    {
        if (is_array($a[0])) {
            $args = array($a[0]);
            unset($a[0]);
        } else {
            $args = array(array());
        }
        $args[] = $m;
        if (is_array($a)) {
            $args = array_merge($args, $a);
        }

        $ret = call_user_func_array(array($this, "RunFunction"), $args);

        // Only one of these can run at any one time.
        $ret = $this->_callRead($args[0], $ret, $m);
        $ret = $this->_callLoad($args[0], $ret, $m);
        return $ret;
        
    }

    /**
     * Deals with calling 'read' functions
     *
     * @param array  $Info     Infomation about the device to use
     * @param array  $packet   The packets to send
     * @param string $function The name of the function that is being run
     *
     * @return mixed
     */
    private function _callRead($Info, $packet, $function) 
    {
        if (substr($function, 0, 4) == "read") {
            // Send these packets
            $ret = $this->packet->sendPacket($Info, $packet);
            // If we don't get anything, return
            if (!is_array($ret)) return $ret;
            // Check to see if there is a corresponding 'interp' function.
            $interp = str_replace("read", "interp", $function);
            $interpRet = $this->runFunction($Info, $interp, $ret);
            // If we got an array back, return that.
            if (is_array($interpRet)) return $interpRet;
            // Otherwise, return what we got before.
            return $ret;
        }
        // If this is not a read or set function just return what we were given
        return $packet;
    }
    /**
     * Deals with calling 'read' functions
     *
     * @param array  $Info     Infomation about the device to use
     * @param array  $packet   The packets to send
     * @param string $function The name of the function that is being run
     *
     * @return mixed
     */
    private function _callLoad($Info, $packet, $function) 
    {
        if (substr($function, 0, 4) == "load") {
            // Send these packets
            $ret = $this->packet->sendPacket($Info, $packet);
            return $ret;
        }
        // If this is not a read or set function just return what we were given
        return $packet;
    }                
        
    /**
     * Runs a function using the correct driver for the endpoint
     *
     * @param array  $Info    Infomation about the device to use
     *
     * @return null
     */
    function done($Info) 
    {
        $this->packet->Close($Info);
    }
    
    /**
     * Runs a function using the correct driver for the endpoint
     *
     * @param array $DevInfo Array of information about the device with the data from the incoming packet
     * @param bool  $force   Force the update even if the serial number and hardware part number don't match
     *
     * @return bool
     */
    function UpdateDevice($Packets, $force=false) 
    {
        $devInfo = $this->interpConfig($Packets);
        $ret1 = $this->device->updateDevice($devInfo, $force);                    
        $ret2 = $this->updateConfig($devInfo);
        return ($ret1 || $ret2);
    }
    
    /**
     * Wrapper for device::getDevice
     *
     * @see device::getDevice
     *
     * @param mixed $id   This is either the DeviceID, DeviceName or DeviceKey
     * @param int   $type The type of the 'id' parameter.  It is "ID" for DeviceID,
     *         "NAME" for DeviceName or "KEY" for DeviceKey.  "KEY" is the default.
     *
     * @return array
     */    
    function getDevice($id, $type="KEY") 
    {

        $devInfo = $this->device->getDevice($id, $type);
        if (is_array($devInfo)) {
            if (is_object($this->_driver)) $devInfo = $this->_driver->DriverInfo($devInfo);

            $query = "select * from calibration where DeviceKey='".$id."' ORDER BY StartDate DESC LIMIT 0,1";

            $cal                    = $this->query($query);
            $devInfo["Calibration"] = array();
            if (is_array($cal[0])) {
                $devInfo["Calibration"] = $this->getCalibration($devInfo, $cal[0]['RawCalibration']);
            }

            $query = "select * from gateways where GatewayKey='".$devInfo['GatewayKey']."'";

            $gw = $this->query($query);
            if (is_array($gw)) {
                $devInfo['Gateway'] = $gw[0];
            }
        }
        return $devInfo;
        
    }
    /**
     * Checks to see if this is a controller.
     *
     * @param array &$info This is a device information array
     *
     * @return bool
     */
    function isController(&$info)
    {
        return method_exists($this->drivers[$info['Driver']], "checkProgram");
    }


    /**
     * Runs a function using the correct driver for the endpoint
     *
     * @param array $Info       Infomation about the device to use
     * @param int   $GatewayKey The gateway to try first.
     *
     * @return array
     */
    function GetInfo($Info, $GatewayKey = 0) 
    {
        $DeviceID = $Info["DeviceID"];
        //add_debug_output("Getting Configuration for ".$Info["DeviceID"]."<BR>\n");
        if ($GatewayKey == 0) {
            $usegw = $this->gateway->get($Info["GatewayKey"]);
        } else {
            $usegw = $this->gateway->get($GatewayKey);
        }
        if ($usegw["BackupKey"] == 0) {
            $gw = $usegw;
        } else {
            $gw = $this->gateway->get($usegw["BackupKey"]);
        }

        if ($gw !== false) {
            $Info = array_merge($Info, $usegw);
            $data = $this->readConfig($Info);
            $this->Info[$DeviceID] = $gw;
            $dev = $this->interpConfig (array($Info));
            if (is_array($dev[0])) {
                $this->Info[$DeviceID] = array_merge($this->Info[$DeviceID], $dev[0]);
            }
//            $this->Info[$DeviceID] = $this->RunFunction($this->Info[$DeviceID], "GetInfo");
        }
        return($this->Info[$DeviceID]);
            
    }
    /**
     * Interpret a configuration packet.
     *
     * @param array $packets Infomation about the device to use plus a configuration packet            
     *
     * @return mixed array of device information on success, false on failure 
     *
     * @todo Move this back to the driver class?
     */
    function interpConfig ($packets) 
    {
//        if (isset($packets['RawData'])) $packets = array($packets);
        if (!is_array($packets)) return false;

        $dev = array();
        $Info = array();
        foreach ($packets as $packet) {
//            $Info = $packet;
            devInfo::DeviceID($packet);
            devInfo::RawData($packet);

            // Check for a basic setup packet
            if ($packet['sendCommand'] == PACKET_COMMAND_GETSETUP) {
                // Since we got a basic setup packet lets deal with it
                if (empty($Info['DeviceID'])) $Info["DeviceID"] = $packet["DeviceID"];
                if ($Info['DeviceID'] == $packet['DeviceID']) {
                    // Make sure this one is for the correct DeviceID
                    if (empty($Info['DeviceKey'])) $Info['DeviceKey'] = $packet['DeviceKey'];
                    $Info["CurrentGatewayKey"] = $packet["GatewayKey"];
                    $Info["Date"] = $packet["Date"];
                }
            }
            
            // Save all of the raw data by DeviceID.  We don't necessarily know what
            // Device we are dealing with.
            $RawData[$packet['DeviceID']][$packet['sendCommand']] = $packet['RawData'];

        }
        if (is_array($RawData[$Info['DeviceID']])) {
            $Info['RawData'] = $RawData[$Info['DeviceID']];
            eDEFAULT::interpConfig($Info);
            $Info["Driver"] = $this->FindDriver($Info);
        }

        if (!empty($Info['Driver']) && ($Info['Driver'] != "eDEFAULT")) {
            $this->RunFunction($Info, "interpConfig");
        }
        return $Info;
    }




    /**
     * Adds the driver information to the array given to it
     *
     * @param array $Info Infomation about the device to use
     *
     * @return array Returns $Info with added information from the driver.
     */
    function DriverInfo($Info) 
    {
        $Info['sendCommand'] = EDEFAULT_CONFIG_COMMAND;
        $this->RunFunction($Info, "interpConfig");
        $Info['history_table'] = $this->getHistoryTable($Info);
        $Info['average_table'] = $this->getAverageTable($Info);
        $Info['location_table'] = $this->getLocationTable($Info);
        return($Info);    
    }

    /**
     * Runs a function using the correct driver for the endpoint
     *
     * @param array $Info Infomation about the device to use
     *
     * @return string
     */
    function FindDriver($Info) 
    {
        if (isset($this->dev[$Info["HWPartNum"]][$Info["FWPartNum"]][$Info["FWVersion"]])) {
            $return = $this->dev[$Info["HWPartNum"]][$Info["FWPartNum"]][$Info["FWVersion"]];
        }else if (isset($this->dev[$Info["HWPartNum"]][$Info["FWPartNum"]]["BAD"])) {
            $return = "eDEFAULT";
        } else if (isset($this->dev[$Info["HWPartNum"]][$Info["FWPartNum"]]["DEFAULT"])) {
            $return = $this->dev[$Info["HWPartNum"]][$Info["FWPartNum"]]["DEFAULT"];
        } else if (isset($this->dev[$Info["HWPartNum"]]["DEFAULT"]["DEFAULT"])) {
            $return = $this->dev[$Info["HWPartNum"]]["DEFAULT"]["DEFAULT"];
        } else {
            $return = "eDEFAULT";
        }
        return $return;
    }
    /**
     * Wrapper for UnitConversion::modifyUnits()
     *
     * @param array &$history The history to modify.  This array gets directly modified.
     * @param array &$devInfo The devInfo array to modify.  This array gets directly modified.
     * @param int   $dPlaces  The maximum number of decimal places to show.
     * @param array &$type    The types to change to
     * @param array &$units   The units to change to
     *
     * @return null
     */
    function modifyUnits(&$history, &$devInfo, $dPlaces, &$type=null, &$units=null) 
    {
        // This uses defaults if nothing exists for a particular sensor
        $this->sensors->checkUnits($devInfo['Types'], $devInfo['params']['sensorType'], $units, $type);
        $this->unit->modifyUnits($history, $devInfo, $dPlaces, $type, $units);
    }    

    /**
     * Wrapper for filter::filter()
     *
     * @param array $history The history to modify.  This array gets directly modified.
     * @param array $devInfo The devInfo array to modify.  This array gets directly modified.
     * @param array $filters Extra filters to use
     *
     * return none
     */
    function filter(&$history, &$devInfo, $filters = null) 
    {
        // This uses defaults if nothing exists for a particular sensor
        $this->filters->filter($history, $devInfo["params"]["filters"]);
        $this->filters->filter($history, $filters);
    }

    /**
     * Register a endpoint driver.
     *
     * @param mixed $class The name of the sensor class to register, or the actual object to register
     * @param string $name The name of the sensor class if the above is an object.  The default is the class name.
     *
     * @return bool true on success, false on failure
     */
    public function registerDriver($class, $name=false) 
    {
        if (is_string($class) && class_exists($class)) {
            $this->drivers[$class] = new $class($this);
        } else if (is_object($class)) {
            if (empty($name)) $name = get_class($class);
            $this->drivers[$name] = $class;
            $class = $name;
        } else {
            return false;
        }
        if (is_array($this->drivers[$class]->devices)) {
            foreach ($this->drivers[$class]->devices as $fw => $Firm) {
                foreach ($Firm as $hw => $ver) {
                    $dev = explode(",", $ver);
                    foreach ($dev as $d) {
                        if (!isset($this->dev[$hw][$fw][$d])) {
                            $this->dev[$hw][$fw][$d] = $class;
                            //add_debug_output("Found driver for Hardware ".$hw." Firmware ".$fw." Version ".$d."<BR>\n");
                        }
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    
    }

    
    /**
     * Constructor    
     *
     * @param mixed  $db      Either a PDO object, or a file name for the sqlite driver
     * @param object $plugins A plugin object
     */
    function __construct(&$db=null, $plugins = "") 
    {

        $this->db = &$db;
        $this->packet = new EPacket(null, $this->verbose);
        $this->analysis = new analysis($db);
        $this->gateway = new gateway($db);
        $this->device = new device($db);
        $this->unit = new unitConversion();

        $this->drivers["eDEFAULT"] = new eDEFAULT($this);

        if (!is_object($plugins)) {
            if (!isset($_SESSION["incdir"])) $_SESSION["incdir"] = dirname(__FILE__)."/";
            $plugins = new Plugins(dirname(__FILE__)."/drivers/", "php");
        }
        // This has to go after the plugin registrations about
        $this->sensors = new sensor($plugins);
        // This has to go after the plugin registrations about
        $this->filters = new filter($plugins);
        if (is_array($plugins->plugins["Generic"]["driver"])) {
            foreach ($plugins->plugins["Generic"]["driver"] as $driver) {
                $this->registerDriver($driver["Class"]);
            }
        } else {
            // Do something here as we didn't find any plugins.
        }
    }

}


    

?>
