<?php
/**
 *   Main class for dealing with endpoints.
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *   
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *   
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *   
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Endpoints
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$    
 *
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
/** The default command to read config  */
define("eDEFAULT_CONFIG_COMMAND", "5C");
/** The default command to read sensors  */
define("eDEFAULT_SENSOR_READ", "55");
/** The default command to set the group  */
define("eDEFAULT_SETGROUP", "5B");

if (!defined(HUGNET_INCLUDE_PATH)) define("HUGNET_INCLUDE_PATH", dirname(__FILE__));

require_once(HUGNET_INCLUDE_PATH."/EPacket.php");
require_once(HUGNET_INCLUDE_PATH."/sensor.php");
require_once(HUGNET_INCLUDE_PATH."/filter.php");
require_once(HUGNET_INCLUDE_PATH."/device.php");
require_once(HUGNET_INCLUDE_PATH."/unitConversion.php");
require_once(HUGNET_INCLUDE_PATH."/lib/plugins.inc.php");
require_once(HUGNET_INCLUDE_PATH."/drivers/endpoints/eDEFAULT.php");

/**
 * Class for talking with HUGNet endpoints
 *
 *    All communication with endpoints should go through this class.
  */
class driver {
    /** This is the default number of decimal places to use if it is not specified anywhere else */
    var $_decimalPlaces = 2;

    /** The error number.  0 if no error occurred  */ 
    var $Errno = 0;
    /** Error String  */
    var $Error = "";
    /** An array of driver classes.  */
    var $drivers = array();
    /** The drivers and what software and hardware they encompass are mapped here  */
    var $dev = array();
    /** The display colors to use for different error codes     */
    var $ErrorColors = array(
        "DevOnBackup" => array("Severity" => "Low", "Description" => "Device is currently being polled on one of the backup servers", "Style" => "#00E000"),
    );    

    var $device_table = "devices";
    var $analysis_table = "analysis";
    var $raw_history_table = "history_raw";
    var $packet_log_table = "PacketLog";

    /**
      * @see device::health
      */
    function health($where, $days = 7, $start=NULL) {
        return $this->device->health($where, $days, $start);
    }
    /**
     * @see device::Diagnose
     */
    function Diagnose($Info) {
        return $this->device->Diagnose($Info);
    }        
    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     * @param $function String The name of the function to run
     * @return FALSE if the function does not exist.  Otherwise passes
            the function return through.

        This checks for the function in both the specific class for the endpoint
        driver and the default driver.  If the classes or the methods don't exist
        then it complains.
     */
    function RunFunction (&$Info, $function) {
        if (!is_array($Info)) return FALSE;
        $return = array();
        $function = trim($function);
        $use_driver = isset($Info["Driver"]) ? $Info['Driver'] : 'eDEFAULT';
        if ($use_driver == "eDEFAULT") {
            $use_driver = $this->FindDriver($Info);
            $Info["OldDriver"] = $Info["Driver"];
            $Info["Driver"] = $use_driver;
        }

        if (is_object($this->drivers[$use_driver]) && method_exists($this->drivers[$use_driver], $function)) {
            $use_class = $use_driver;
        } else if (is_object($this->drivers["eDEFAULT"]) && method_exists($this->drivers["eDEFAULT"], $function)) {
            $use_class = "eDEFAULT";
        } else {
            //add_debug_output("All Drivers (including eDEFAULT) failed.<BR>\n");
            return(FALSE);
        }

        $args = func_get_args();
        $args[0] = &$Info;
        unset($args[1]);
        $class = &$this->drivers[$use_class];
        $return = call_user_func_array(array(&$class, $function), $args);

//        $return = $this->drivers[$use_class]->$function($Info);
        if (is_array($return)) {        
            if (isset($return["Errno"]) || isset($return["Error"])) { 
                $this->Error = $return["Error"];        
                $this->Errno = $return["Errno"];
                $return = FALSE;
            }
        }
        return $return;
        
    }

    /**
     *  Tries to run a function defined by what is called.  This should
     * replace all of the really short functions calling RunFunction.
     *
     */
    function __call($m, $a) {
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
        return call_user_func_array(array($this, "RunFunction"), $args);
        
    }

    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     */
    function SetConfig($Info, $start, $data) {
        //add_debug_output("Setting Configuration:<br>\n");
         $pkts = $this->RunFunction($Info, "SetConfig", $start, $data);
        $this->Error = "";
        if ($pkts !== FALSE) {
            $return = $this->packet->SendPacket($Info, $pkts, FALSE);
            if ($return == FALSE) {
                $this->Error .= " Setting Config Failed. \n";
                $this->Errno = -1;
            }
        }
        return($return);
    }
        
                
        
    /**
     * Runs a function using the correct driver for the endpoint
     */
    function done($Info) {
        $this->packet->Close($Info);
    }
    
    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Packet Array Array of information about the device with the data from the incoming packet
     * @param $force Boolean Force the update even if the serial number and hardware part number don't match
     */
    function UpdateDevice($Packet, $force=FALSE){

        return $this->device->UpdateDevice($Packet, $force);                    
    }
    
    /**
     * Wrapper for device::getDevice
     *
     * @see device::getDevice
     */    
    function getDevice($id, $type="KEY") {

        return $this->device->getDevice($id, $type);
    }
    /**
     * Converts a packet array into an array for inserting into the packet log tables in the database.
     * @param $Packet Array The packet that came in.
     * @param $Gateway Integer the gateway key of the gateway this packet came from
     * @param $Type String They type of packet it is.
     */
    function PacketLog($Packet, $Gateway, $Type="UNSOLICITED") {
        //$this->device->lookup($Packet["from"], "DeviceID");
//        $Info = $this->device->lookup[0];
        $Info = array();
        if (isset($Packet["DeviceKey"])) {
            $Info["DeviceKey"] = $Packet["DeviceKey"];
        }
        $Info['ReplyTime'] = isset($Packet['ReplyTime']) ? $Packet['ReplyTime'] : 0 ;
        $Info["GatewayKey"]= $Gateway["GatewayKey"];
        $Info["RawData"] = $Packet["RawData"];
        $Info["Date"] = date("Y-m-d H:i:s", $Packet["Time"]);
        $Info["PacketFrom"] = $Packet["From"];
        $Info["Command"] = $Packet["Command"];
        $Info["sendCommand"] = isset($Packet["sendCommand"]) ? $Packet["sendCommand"] : '  ';
        if (!empty($Type)) {
            $Info["Type"] = $Type;
        } else {
            $Info["Type"] = "UNSOLICITED";        
        }

        return($Info);
    }

    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     */
    function FindDevice($Info) {
        $gw = $this->gateway->getAll();
        $return = $this->packet->FindDevice($Info, $gw);
        return($return);
    }

        
    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     * @param GatewayKey Int The gateway to try first.
     */
    function GetInfo($Info, $GatewayKey = 0) {
        $DeviceID = $Info["DeviceID"];
        //add_debug_output("Getting Configuration for ".$Info["DeviceID"]."<BR>\n");
        if ($GatewayKey == 0) {
            $usegw = $this->FindDevice($Info);
        } else {
            $usegw = $this->gateway->get($GatewayKey);
        }
        if ($usegw["BackupKey"] == 0) {
            $gw = $usegw;
        } else {
            $gw = $this->gateway->get($usegw["BackupKey"]);
        }

        if ($gw !== FALSE) {
            $Info = array_merge($Info, $usegw);
            $data = $this->ReadConfig($Info);
            $this->Info[$DeviceID] = $gw;
            $dev = $this->InterpConfig (array($Info));
            if (is_array($dev[0])) {
                $this->Info[$DeviceID] = array_merge($this->Info[$DeviceID], $dev[0]);
            }
//            $this->Info[$DeviceID] = $this->RunFunction($this->Info[$DeviceID], "GetInfo");
        }
        return($this->Info[$DeviceID]);
            
    }
    /**
     * Interpret a configuration packet.
     * @param $packet Array Infomation about the device to use plus a configuration packet            
     * @return Array of device information on success, FALSE on failure 
     * @todo Move this back to the driver class?
     */
    function InterpConfig ($packets, $forceDriver=NULL) {
//        if (isset($packets['RawData'])) $packets = array($packets);
        if (!is_array($packets)) return FALSE;

        $dev = array();
        $return = array();
        foreach($packets as $packet) {
//            $return = $packet;
            if (!isset($return['DeviceID'])) {
                if (isset($packet['PacketFrom'])) {
                    $return['DeviceID'] = $packet['PacketFrom'];
                } else if (isset($packet['From'])) {
                    $return['DeviceID'] = $packet['From'];
                }
            } else {
                if (!empty($packet['PacketFrom'])) {
                    if ($return['DeviceID'] != $packet['PacketFrom']) {
                        continue;
                    }
                } else if (!empty($packet['From'])) {
                    if ($return['DeviceID'] != $packet['From']) {
                        continue;
                    }
                } else if (!empty($packet['DeviceID'])) {
                    if ($return['DeviceID'] != $packet['DeviceID']) {
                        continue;
                    }
                }
            }
            if (!isset($packet["RawData"])) {
                if (isset($packet["Data"])) {
                    $packet["RawData"] = $packet["Data"];
                } else if (isset($packet["rawdata"])) {
                    $packet["RawData"] = $packet["rawdata"];
                } else if (isset($packet["RawSetup"])) {
                    $packet['RawData'] = $packet['RawSetup'];
                }
            }
            if (isset($packet["Date"])) {
                $return["LastConfig"] = $packet["Date"];
            } else {
                $return["LastConfig"] = date("Y-m-d H:i:s");
            }
            if (($return['Driver'] == 'eDEFAULT') || empty($return['Driver'])) {
                if (isset($dev[$return['From']])) {
                       $return['Driver'] = $dev[$return['From']];    
                } else if (isset($packet['Driver'])) {
                    $return['Driver'] = $packet['Driver'];
                }
            }
            if (empty($return['DeviceKey'])) {
                $return['DeviceKey'] = $packet['DeviceKey'];
            }

            switch (trim(strtoupper($packet['sendCommand']))) {
                case PACKET_COMMAND_GETSETUP:
                case PACKET_COMMAND_GETSETUP_GROUP:
    
                    if (strlen($packet["RawData"]) >= PACKET_CONFIG_MINSIZE) 
                    {
                        $return["CurrentGatewayKey"] = $packet["GatewayKey"];
                        if (isset($packet["Date"])) {
                            $return["LastConfig"] = $packet["Date"];
                        } else {
                            $return["LastConfig"] = date("Y-m-d H:i:s");
                        }
                        $return["SerialNum"] = hexdec(substr($packet["RawData"], 0, 10));
                        if (empty($return["DeviceID"])) {
                            $return["DeviceID"] = EPacket::hexify($return["SerialNum"], 6);
                        }
                        $return["HWPartNum"] =     trim(strtoupper(substr($packet["RawData"], ENDPOINT_HW_START, 4)."-".
                                                                                substr($packet["RawData"], ENDPOINT_HW_START+4, 2)."-".
                                                                                substr($packet["RawData"], ENDPOINT_HW_START+6, 2)."-".
                                                                                chr(hexdec(substr($packet["RawData"], ENDPOINT_HW_START+8, 2)))));
                        $return["FWPartNum"] =     trim(strtoupper(substr($packet["RawData"], ENDPOINT_FW_START, 4)."-".
                                                                                substr($packet["RawData"], ENDPOINT_FW_START+4, 2)."-".
                                                                                substr($packet["RawData"], ENDPOINT_FW_START+6, 2)."-".
                                                                                chr(hexdec(substr($packet["RawData"], ENDPOINT_FW_START+8, 2)))));
                        $return["FWVersion"] =     trim(strtoupper(substr($packet["RawData"], ENDPOINT_FWV_START, 2).".".
                                                                                substr($packet["RawData"], ENDPOINT_FWV_START+2, 2).".".
                                                                                substr($packet["RawData"], ENDPOINT_FWV_START+4, 2)));
                
                        if (strlen($packet["RawData"]) >= (ENDPOINT_GROUP+6)) {
                            $return["DeviceGroup"] = trim(strtoupper(substr($packet["RawData"], ENDPOINT_GROUP, 6)));
                        }    
                        if (strlen($packet["RawData"]) >= (ENDPOINT_BOREDOM+2)) {
                            $return["BoredomThreshold"] =     hexdec(trim(strtoupper(substr($packet["RawData"], ENDPOINT_BOREDOM, 2))));
                        }            
                        $return["RawSetup"] = $packet["RawData"];
                        $return["Driver"] = $this->FindDriver($return);
                        if ($return['Driver'] != 'eDEFAULT') {
                            $dev[$return['From']] = $return['Driver'];
                        }

                    }
                
                    break;
                case PACKET_COMMAND_GETCALIBRATION:
                    $return['RawCalibration'] = $packet['RawData'];
                    break;
            }
            $return['RawData'][$packet['sendCommand']] = $packet['RawData'];
        }
        if (isset($return['Driver']) && ($return['Driver'] != "eDEFAULT")) {
            $this->RunFunction($return, "InterpConfig");
        }
        return $return;
    }


    /**
     * Gets the name of the history table for a particular device
     *
     * @param $Info Array Infomation about the device to use
     * @return mixed The name of the table as a string on success, FALSE on failure
     */
    function getHistoryTable($Info) {
        if (is_object($this->drivers[$Info['Driver']]))
        {
            return $this->drivers[$Info['Driver']]->history_table;
        }
        return FALSE;
    }

    /**
     * Gets the name of the average table for a particular device
     *
     * @param $Info Array Infomation about the device to use
     * @return mixed The name of the table as a string on success, FALSE on failure
     */
    function getAverageTable($Info) {
        if (is_object($this->drivers[$Info['Driver']]))
        {
            return $this->drivers[$Info['Driver']]->average_table;
        }
        return FALSE;
    }

    /**
     * Gets the name of the location table for a particular device
     *
     * @param $Info Array Infomation about the device to use
     * @return mixed The name of the table as a string on success, FALSE on failure
     */
    function getLocationTable($Info) {
        if (is_object($this->drivers[$Info['Driver']]))
        {
            return $this->drivers[$Info['Driver']]->location_table;
        }
        return FALSE;
    }


    /**
     * Adds the driver information to the array given to it
     * @param $Info Array Infomation about the device to use
     * @return Returns $Info with added information from the driver.
     */
    function DriverInfo($Info) {
        $Info['sendCommand'] = eDEFAULT_CONFIG_COMMAND;
        $Info = $this->RunFunction($Info, "InterpConfig");
        $Info['history_table'] = $this->getHistoryTable($Info);
        $Info['average_table'] = $this->getAverageTable($Info);
        $Info['location_table'] = $this->getLocationTable($Info);
        return($Info);    
    }

    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     */
    function FindDriver($Info) {
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
 *
 * @param
 * @return
    
     */
    function modifyUnits(&$history, &$devInfo, $dPlaces, &$type=NULL, &$units=NULL) {
        // This uses defaults if nothing exists for a particular sensor
        $this->sensors->checkUnits($devInfo['Types'], $devInfo['params']['sensorType'], $units, $type);

        $lastRecord = NULL;
        if (!is_array($history)) $history = array();
        foreach($history as $key => $val) {
           if (is_array($val)) {
                if (($lastRecord !== NULL) || (count($history) < 2)) {
                    for($i = 0; $i < $devInfo['ActiveSensors']; $i ++) {
                        if ($type[$i] != $devInfo["dType"][$i]) {
                            switch($type[$i]) {
                            case 'diff':
                                if (!isset($val['deltaT'])) $history[$key]['deltaT'] = strtotime($val['Date']) - strtotime($lastRecord['Date']);
                                $history[$key]["Data".$i] = $lastRecord["Data".$i] - $val["Data".$i];
                                break;
                            case 'ignore':
                                unset($history[$key]["Data".$i]);
                                break;
                            default:
                                 // Do nothing by default.
                                 // That means we need to make sure we change the data type
                                 // in the $type array to reflect what we have not done.  ;)
                                if (!empty($devInfo["dType"][$i])) {
                                    $type[$i] = $devInfo["dType"][$i];
                                }
                                break;
                            }
                        }
  
                        if (!$this->sensors->checkPoint($history[$key]['Data'.$i], $devInfo['Types'][$i], $devInfo['params']['sensorType'][$i], $devInfo['Units'][$i], $devInfo['dType'][$i])) {
                            $history[$key]['Data'.$i] = NULL;
                            $history[$key]['data'][$i] = NULL;
                        }
                    }            
                    $lastRecord = $val;
                } else {
                    $lastRecord = $val;
                    unset($history[$key]);
                }
                if (isset($history[$key])) {
                    for($i = 0; $i < $devInfo['ActiveSensors']; $i ++) {
                        if (isset($units[$i]) && isset($history[$key]['Data'.$i])) {
                            if (!isset($cTo[$i])) $cTo[$i] = $units[$i];

                            $from = isset($val['Units'][$i]) ? $val['Units'][$i] : $devInfo['Units'][$i];
                            $history[$key]['Data'.$i] = $this->unit->convert($history[$key]['Data'.$i], $from, $cTo[$i], $history[$key]['deltaT'], $type[$i], $extra[$i]);
                        }
                        if (isset($dPlaces) && is_numeric($dPlaces) && is_numeric($history[$key]["Data".$i])) {
                            $history[$key]["Data".$i] = round($history[$key]["Data".$i], $dPlaces);
                        }
                        $history[$key]['data'][$i] = $history[$key]['Data'.$i];
                    }
                }
            }
        }
        if (is_array($cTo)) {
            foreach($cTo as $key => $val) {
                $devInfo["Units"][$key] = $val;
            }
        }
    }

    /**
     * Register a endpoint driver.
     *
     * @param mixed $class The name of the sensor class to register, or the actual object to register
     * @param string $name The name of the sensor class if the above is an object.  The default is the class name.
     * @return bool TRUE on success, FALSE on failure
     */
    public function registerDriver($class, $name=FALSE) {
            if (is_string($class) && class_exists($class)) {
                $this->drivers[$class] = new $class($this);
            } else if (is_object($class)) {
                if (empty($name)) $name = get_class($class);
                $this->drivers[$name] = $class;
                $class = $name;
            } else {
                return FALSE;
            }
            if (is_array($this->drivers[$class]->devices)) {
                foreach($this->drivers[$class]->devices as $fw => $Firm) {
                    foreach($Firm as $hw => $ver) {
                        $dev = explode(",", $ver);
                        foreach($dev as $d) {
                            if (!isset($this->dev[$hw][$fw][$d])) {
                                $this->dev[$hw][$fw][$d] = $class;
                                //add_debug_output("Found driver for Hardware ".$hw." Firmware ".$fw." Version ".$d."<BR>\n");
                            }
                        }
                    }
                }
                return TRUE;
            } else {
                return FALSE;
            }
    
    }

    
    /**
     * Constructor    
     */
    function driver(&$db=NULL, $plugins = "", $direct=TRUE) {        

        $this->db = &$db;
        if (is_object($this->db)) {
            $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
        }
        if ($direct) {
            $this->packet = new EPacket(NULL, $this->verbose);
        } else {
            $this->packet = new EPacket(NULL, $this->verbose, $this->db);
        }
        $this->gateway = new gateway($this);
        $this->device = new device($this);
        $this->unit = new unitConversion();

        $this->drivers["eDEFAULT"] = new eDEFAULT($this);

        if (!is_object($plugins)) {
            if (!isset($_SESSION["incdir"])) $_SESSION["incdir"] = dirname(__FILE__)."/";
            $plugins = new plugins(dirname(__FILE__)."/drivers/", "php");
        }
        // This has to go after the plugin registrations about
        $this->sensors = new sensor($plugins);
        // This has to go after the plugin registrations about
        $this->filters = new filter($plugins);
        if (is_array($plugins->plugins["Generic"]["driver"])) {
            foreach($plugins->plugins["Generic"]["driver"] as $driver) {
                $this->registerDriver($driver["Class"]);
            }
        } else {
            // Do something here as we didn't find any plugins.
        }
    }

}


    

?>
