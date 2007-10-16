<?php
/**
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


require_once(HUGNET_INCLUDE_PATH."/packet.inc.php");
require_once(HUGNET_INCLUDE_PATH."/sensors.inc.php");
require_once(HUGNET_INCLUDE_PATH."/filters.inc.php");
require_once(HUGNET_INCLUDE_PATH."/device.inc.php");
require_once(HUGNET_INCLUDE_PATH."/unitConversion.inc.php");

/**
 * Class for talking with HUGNet endpoints
 *
 *    All communication with endpoints should go through this class.
  */
class driver {
    var $_maxDecimalPlaces = array(
        'Revs' => 0,
        'counts' => 0,
        "Direction" => 0,
        "NumDir" => 0,
        'mA' => 0,
    );
    
    var $_defUnits = array(
        '&#176;C' => '&#176;F',
        'Revs' => 'RPM',
        'HalfRevs' => 'MPH',
        'numDir' => 'Direction',
    );

    var $_defType = array(
        'RPM' => 'diff',
        'MPH' => 'diff',
    );
    var $_decimalPlaces = 2;


    /** The error number.  0 if no error occurred  */ 
    var $Errno = 0;
    /** Error String  */
    var $Error = "";
    /** An array of driver classes.  */
    var $drivers = array();
    /** The drivers and what software and hardware they encompass are mapped here  */
    var $dev = array();
    /** How many times the poll interval has to pass before we show an error on it     */
    var $PollWarningIntervals = 2;        
    /** The display colors to use for different error codes     */
    var $ErrorColors = array(
        "DevOnBackup" => array("Severity" => "Low", "Description" => "Device is currently being polled on one of the backup servers", "Style" => "#00E000"),
    );    

    var $device_table = "devices";
    var $analysis_table = "analysis";
    var $raw_history_table = "history_raw";
    var $packet_log_table = "PacketLog";

    /**
     *   Queries health information from the database.
     *   
     ** @todo This should be moved to the device class
     *   
     ** @param string $where Extra where clause for the SQL
     ** @param int $days The number of days back to go
     ** @param string|int $start The start date of the health report
     ** @return array The array of health information
      */
    function health($where, $days = 7, $start=NULL) {

        if ($start === NULL) {
            $start = time();
        } else if (is_string($start)) {
            $start = strtotime($start);
        }
        $end = $start - (86400 * $days);
        $cquery = "SELECT COUNT(DeviceKey) as count FROM ".$this->device_table." ";
        $cquery .= " WHERE PollInterval > 0 ";
        if (!empty($where)) $cquery .= " AND ".$where;
        $res = $this->db->getArray($cquery);
        $count = $res[0]['count'];
        if (empty($count)) $count = 1;
        
        $query = " SELECT " .
                 "  ROUND(AVG(AverageReplyTime), 2) as ReplyTime " .
                 ", ROUND(STD(AverageReplyTime), 2) as ReplyTimeSTD " .
                 ", ROUND(MIN(AverageReplyTime), 2) as ReplyTimeMIN " .
                 ", ROUND(MAX(AverageReplyTime), 2) as ReplyTimeMAX " .
                 ", ROUND(AVG(AveragePollTime), 2) as PollInterval " . 
                 ", ROUND(STD(AveragePollTime), 2) as PollIntervalSTD " . 
                 ", ROUND(MIN(AveragePollTime), 2) as PollIntervalMIN " . 
                 ", ROUND(MAX(AveragePollTime), 2) as PollIntervalMAX " . 
                 ", ROUND(AVG(PollInterval), 2) as PollIntervalSET " . 
                 ", ROUND(AVG(PollInterval/AveragePollTime), 2) as PollDensity " . 
                 ", ROUND(STD(PollInterval/AveragePollTime), 2) as PollDensitySTD " . 
                 ", ROUND(MIN(PollInterval/AveragePollTime), 2) as PollDensityMIN " . 
                 ", ROUND(MAX(PollInterval/AveragePollTime), 2) as PollDensityMAX " . 
                 ", '1.0' as PollDensitySET " . 
                 ", SUM(Powerups) as Powerups " .
                 ", SUM(Reconfigs) as Reconfigs " .
                 ", ROUND(SUM(Polls) / ".$days.") as DailyPolls ".
                 ", ROUND((1440 / AVG(PollInterval)) * ".$count.") as DailyPollsSET ".
                 " ";
        $query .= " FROM " . $this->analysis_table;

        $query .= " LEFT JOIN " . $this->device_table . " ON " . 
                 $this->device_table . ".DeviceKey=" . $this->analysis_table . ".DeviceKey ";

        $query .= " WHERE " .
                  $this->analysis_table . ".Date <= ".$this->db->qstr(date("Y-m-d H:i:s", $start)).
                  " AND " .
                  $this->analysis_table . ".Date >= ".$this->db->qstr(date("Y-m-d H:i:s", $end));
    
        if (!empty($where)) $query .= " AND ".$where;
                 
        $res = $this->db->getArray($query);
        if (isset($res[0])) $res = $res[0];
        return $res;
    }



    /**
     * Sends out an all call so all boards respond.
     * @param $Info Array Infomation about the device to get stylesheet information for
     * @return The return should be put inside of style="" css tags in your HTML
    
        Returns a style based on the condition of the endpoint.  Useful for displaying
        a list of endpoints and quickly seeing which ones have problems.
     */
    function Diagnose($Info) {

        $problem = array();
        if ($Info["PollInterval"] > 0) {
            $timelag = time() - strtotime($Info["LastPoll"]);
            $pollhistory = (strtotime($Info["LastPoll"]) - strtotime($Info["LastHistory"]));
            if ($pollhistory < 0) $pollhistory = (-1)*$pollhistory;
            
            if (($timelag > ($this->PollWarningIntervals*60*$Info["PollInterval"]))){
                $problem[] = "Last Poll ".$this->get_ydhms($timelag)." ago\n";
            }
            if ($pollhistory > 1800) {
                $problem[] = "History ".$this->get_ydhms($pollhistory)." old\n";
            }
            if (($Info["GatewayKey"] != $Info["CurrentGatewayKey"]) && ($Info["CurrentGatewayKey"] != 0)) {
//                $problem[] = "Polling on backup gateway\n";
            }
            if ($Info['ActiveSensors'] == 0) {
                $problem[] = "No Active Sensors\n";
            }
        }
        return($problem);        
    }
    

    function get_ydhms ($seconds, $digits=0) {
        $years = (int)($seconds/60/60/24/365.25);
        $seconds -= $years*60*60*24*365.25;
        $days = (int)($seconds/60/60/24);
        $seconds -= $days*60*60*24;
        $hours = (int)($seconds/60/60);
        $seconds -= $hours*60*60;
        $minutes = (int)($seconds/60);
        $seconds -= $minutes*60;
        $seconds = number_format($seconds, $digits);

        $return = "";
        if ($years > 0) $return .= $years."Y ";
        if ($days > 0) $return .= $days."d ";
        if ($hours > 0) $return .= $hours."h ";
        if ($minutes > 0) $return .= $minutes."m ";
        $return .= $seconds."s";
        return($return);
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
    function RunFunction ($Info, $function) {
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

        //add_debug_output("Using driver: '".$use_class."' with function ".$function."<BR>\n");
        $command = '$return = $this->drivers["'.$use_class.'"]->'.$function.'($Info';
        $args = func_get_args();
        for ($i = 2; $i < func_num_args(); $i++) {
            $command .= ", \$args[".$i."]";
        }
        $command .= ");";

        eval($command);
//        $return = $this->drivers[$use_class]->$function($Info);
        if (is_array($return)) {        
            if (isset($return["Errno"]) || isset($return["Error"])) { 
                $this->Error = $return["Error"];        
                $this->Errno = $return["Errno"];
                $return = FALSE;
            }
        }
        return($return);
        
    }

    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     * @param $Type Integer The type of memory to read
     * @param $Address Integer the address to read from memory
     * @param $Length Integer the length of data to read from memory
     */
/*
    function ReadMem($Info, $Type, $Address, $Length) {
    
        $Info["MemType"] = $Type;    
        $Info["MemAddress"] = $Address;    
        $Info["MemLength"] = $Length;    
        $return = $this->RunFunction($Info, "ReadMem");
        if ($return !== FALSE) {
            $return = $this->packet->SendPacket($Info, $return);
        }
        return($return);
    }
 */    
    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     */
    function ReadConfig($Info) {
        $return = $this->RunFunction($Info, "ReadConfig");
        return($return);
    }
    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     */
    function InterpSensors($Info, $Packets) {
        $return = $this->RunFunction($Info, "InterpSensors", $Packets);
        return($return);
    }

    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     */
    function saveSensorData($Info, $Packets) {
        $return = $this->RunFunction($Info, "saveSensorData", $Packets);
        return($return);
    }

    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     */
    function saveConfigData($Info, $Packets) {
        $return = $this->RunFunction($Info, "saveConfigData", $Packets);
        return($return);
    }
    
    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     */
    function GetConfigVars($Info) {
        $return = $this->RunFunction($Info, "GetConfigVars");
        return($return);    
    }

    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     */
    function     SetConfig($Info, $start, $data) {
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
     * Decodes data coming from the endpoint
     * @param $data Array Infomation about the device to use
     */
    function DecodeData($data) {
        $return = $this->RunFunction($data, "DecodeData");
        return($return);
    }

    /**
     * Checks a data record to determine what its status is.  It changes
     * Rec['Status'] to reflect the status and adds Rec['Statusold'] which
     * is the status that the record had originally.
     *
     * @param array $Info The information array on the device
     * @param array $Rec The data record to check
      */
    function CheckRecord($Info, &$Rec) {
        $this->RunFunction($Info, "CheckRecord", $Rec);
    }
    
        
    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     */
    function ReadSensors($Info) {
        $return = $this->RunFunction($Info, "ReadSensors");
        return($return);        
    }
                
    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     * @param $which Which label to retrieve. 
     */
    function GetLabel($Info, $which) {
    
        $Info["GetLabel"] = $which;    
        $return = $this->RunFunction($Info, "GetLabel");
        return($return);
    }

    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     * @param $which Which label to retrieve. 
     */
    function GetUnits($Info, $which) {    
        $Info["GetUnits"] = $which;    
        $return = $this->RunFunction($Info, "GetUnits");
        return($return);
    }
    
    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     */
    function GetCols($Info){
        $return = $this->RunFunction($Info, "GetCols");
        return($return);
    }

    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     */
    function GetCalibration($Info, $rawcal){
        $return = $this->RunFunction($Info, "GetCalibration", $rawcal);
        return($return);
    }

        
    /**
     * Runs a function using the correct driver for the endpoint
     */
    function done() {
        $this->packet->close();
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
     * Runs a function using the correct driver for the endpoint
     * @param $Packet Array The incoming packet
     * @param $GatewayKey Integer The gateway the packet came from
     */
    function UnsolicitedConfigCheck($Packet, $GatewayKey) {
        if (!isset($Packet["DeviceID"])) $Packet["DeviceID"] = strtoupper($Packet["From"]);
        if (!isset($Packet["GatewayKey"])) $Packet["GatewayKey"] = $Packet["Socket"];
        $return = FALSE;
        switch($Packet["Command"]) {
            case "5D":
            case "5E":
            case "5F":
                $return = $this->RunFunction($Packet, "ReadConfig");
                if ($return !== FALSE) {
                    $return = $this->packet->SendPacket($Packet, $return, -1, FALSE);
                }
                break;
            default:
                break;
        }
        return($return);
    }
    
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
        $this->gateway->reset();
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
            if (!isset($packet["RawData"])) {
                if (isset($packet["Data"])) {
                    $packet["RawData"] = $packet["Data"];
                } else if (isset($packet["rawdata"])) {
                    $packet["RawData"] = $packet["rawdata"];
                } else if (isset($packet["RawSetup"])) {
                    $packet['RawData'] = $packet['RawSetup'];
                }
            }
            if (!isset($return['DeviceID'])) {
                if (isset($packet['PacketFrom'])) {
                    $return['DeviceID'] = $packet['PacketFrom'];
                } else if (isset($packet['From'])) {
                    $return['DeviceID'] = $packet['From'];
                }
            } else {
                if (!empty($packet['PacketFrom']))
                    if ($return['DeviceID'] != $packet['PacketFrom']) continue;
                if (!empty($packet['From']))
                    if ($return['DeviceID'] != $packet['From']) continue;
                if (!empty($packet['DeviceID']))
                    if ($return['DeviceID'] != $packet['DeviceID']) continue;
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
     * Adds the driver information to the array given to it
     * @param $Info Array Infomation about the device to use
     * @return Returns $Info with added information from the driver.
     */
    function getHistoryTable($Info) {
        if (is_object($this->drivers[$Info['Driver']]))
        {
            return $this->drivers[$Info['Driver']]->history_table;
        }
        return FALSE;
    }

    /**
     * Adds the driver information to the array given to it
     * @param $Info Array Infomation about the device to use
     * @return Returns $Info with added information from the driver.
     */
    function getAverageTable($Info) {
        if (is_object($this->drivers[$Info['Driver']]))
        {
            return $this->drivers[$Info['Driver']]->average_table;
        }
        return FALSE;
    }

    /**
     * Adds the driver information to the array given to it
     * @param $Info Array Infomation about the device to use
     * @return Returns $Info with added information from the driver.
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
        //add_debug_output("Checking for driver<br>\n");
        //add_debug_output(get_stuff($this->dev, "driver"));
        //add_debug_output("Hardware: ".$Info["HWPartNum"]." Firmware: ".$Info["FWPartNum"]." Version: ".$Info["FWVersion"]."<br>\n");
        if (isset($this->dev[$Info["HWPartNum"]][$Info["FWPartNum"]][$Info["FWVersion"]])) {
            //add_debug_output("Using specific driver for ".$Info["HWPartNum"]." ".$Info["FWPartNum"]." ".$Info["FWVersion"]."<br>\n");
            $return = $this->dev[$Info["HWPartNum"]][$Info["FWPartNum"]][$Info["FWVersion"]];
        }else if (isset($this->dev[$Info["HWPartNum"]][$Info["FWPartNum"]]["BAD"])) {
            //add_debug_output("Bad Combination ".$Info["HWPartNum"]." ".$Info["FWPartNum"]."<br>\n");
            $return = $this->dev[$Info["HWPartNum"]][$Info["FWPartNum"]]["BAD"];
        } else if (isset($this->dev[$Info["HWPartNum"]][$Info["FWPartNum"]]["DEFAULT"])) {
            //add_debug_output("Defaulting to generic driver for ".$Info["HWPartNum"]." ".$Info["FWPartNum"]."<br>\n");
            $return = $this->dev[$Info["HWPartNum"]][$Info["FWPartNum"]]["DEFAULT"];
        } else if (isset($this->dev[$Info["HWPartNum"]]["DEFAULT"]["DEFAULT"])) {
            //add_debug_output("Defaulting to generic driver for ".$Info["HWPartNum"]."<br>\n");
            $return = $this->dev[$Info["HWPartNum"]]["DEFAULT"]["DEFAULT"];
        } else {
            //add_debug_output("No driver found!<br>\n");
            $return = "eDEFAULT";
        }
        return($return);
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
                                 // Do nothing by default
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
                        if (isset($units[$i])) {
                            $to = $units[$i];

                            $from = isset($val['Units'][$i]) ? $val['Units'][$i] : $devInfo['Units'][$i];
                            $func = $this->unit->getConvFunct($from, $to, $type[$i]);

                            if (!empty($func) && ($history[$key]['Data'.$i] !== NULL)) {
                                if (!isset($cTo[$i])) $cTo[$i] = $to;
                                $history[$key]['Data'.$i] = $this->unit->{$func}($history[$key]['Data'.$i], $history[$key]['deltaT'], $type[$i], $extra[$i]);
                            }
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
     * Constructor    
     */
    function driver(&$db=NULL, $plugins = "", $direct=TRUE) {

        // This creates one database connection that everyone shares.
//        if (!isset($options['dbWrite'])) $options['dbWrite'] = FALSE;
//        $mdb =& MultiDBQueryTool::getWriteDSN($servers, $db, $options);
/*
        if ($db == NULL) {
            $db = HUGNET_DATABASE;
        }
 */
        

        $this->db = &$db;
        $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
        if ($direct) {
            $this->packet = new EPacket();
        } else {
            $this->packet = new EPacket(NULL, $this->verbose, $this->db);
        }
        $this->gateway = new gateway($this);
        $this->device = new device($this);
        $this->unit = new unitConversion();

        $this->drivers["eDEFAULT"] = new eDEFAULT($this);

        if (!is_object($plugins)) {
            if (!isset($_SESSION["incdir"])) $_SESSION["incdir"] = dirname(__FILE__)."/";
            $plugins = new plugins(dirname(__FILE__)."/drivers/", "inc.php");
        }
        // This has to go after the plugin registrations about
        $this->sensors = new sensor($plugins);
        // This has to go after the plugin registrations about
        $this->filters = new filter($plugins);

        foreach($plugins->plugins["Generic"]["driver"] as $driver) {
            if (class_exists($driver["Class"])) {
                $class = $driver["Class"];
                $this->drivers[$class] = new $class($this);
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
                }
            }
        }
        
//        print get_stuff($this);

    }

}

/**
 * The default driver class
    
    This is the default driver class.  All drivers MUST inherit this class.  They should
    build on it.  The class has some necessary stuff that doesn't need to be duplicated
    in each of the drivers themselves.
 */
class eDEFAULT {
    /**
 * Stores the device information for this driver
    
    This array stores information on which hardware and firmware combinations
    this driver supports.  The format is as follows:
    @code
    var $devices = array(
        "Firmwware Part #" => array(
            "Hardware Part #" => "Firmware version",
        ),
    );
    @endcode
    Any of those can be set to the keyword "DEFAULT".  This matches anything
    in that category (hardware, firmware, or firmware version).  Wildcards are
    not currently supported.

    @par Example
    @code    
    var $devices = array(
        "0039-20-03-C" => array(
            "0039-12-02-A" => "DEFAULT",
            "0039-12-02-B" => "DEFAULT",
        ),
        "DEFAULT" => array(
            "0039-12-00-A" => "DEFAULT",
            "0039-12-01-A" => "DEFAULT",
            "0039-12-02-A" => "DEFAULT",
            "0039-12-01-B" => "DEFAULT",
            "0039-12-02-B" => "DEFAULT",
        ),

    );
    @endcode
     */
    var $history_table = "history";
    var $location_table = "location";
    var $average_table = "average";
    var $raw_history_table = "history_raw";
    
    var $devices = array();

    var $labels = array(0 => "");        //!< The default labels for the sensor outputs.
    var $units = array(0 => "");        //!< The array of units used by the device sensor outputs (ie Degrees F, Degrees C)
    /** These are the columns that all devices share  */
    var $defcols = array(
        "DeviceKey" => "Key", 
        "DeviceName" => "Name",
        "DeviceID" => "ID", 
        "SerialNum" => "Serial Number", 
        "DeviceGroup" => "Group",
        "HWPartNum" => "Hardware Part #",
        "FWPartNum" => "Firmware Part #",
        "FWVersion" => "Firmware Version",
        "Function" => "Function",
        "Driver" => "Driver",
        "OldDriver" => "Old Driver",
        "RawSetup" => "Raw Setup",
        "DeviceLocation" => "Location",
        "DeviceJob" => "Job",
        "PollInterval" => "Poll Interval",
        "LastPoll" => "Last Poll",
        "LastHistory" => "Last History Update",
        "LastAnalysis" => "Last Analysis",
        "LastConfig" => "Last Config Update",
        "BoredomThreshold" => "Boredom Threshold",
        "GatewayName" => "Gateway",
        "Controller" => "Controller",
        "ControllerIndex" => "Controller Port",
    );

    /** These are the editable columns that all devices share  */
    var $defeditcols = array(
        "DeviceLocation" => "Location",
        "DeviceJob" => "Job",
        "BoredomThreshold" => "Boredom Threshold",
    );

    /** This is where the hardware devices default configurations go. */
    var $config = array(
        "DEFAULT" => array("Function" => "Unknown", "Sensors" => 0, "SensorLength" => 0),        
    );

    var $caldata = array();                //!< Calibration data
    var $cols = array();                    //!< I don't know why this is here
    var $Columns = array();                //!< The columns that are device specific go here
    var $var = array();            //!< The variable that are devices specific go here.

    /** 
     * Default configuraion variable definition
        
     */
    /** Default location variable definition  */
    var $deflocation = array();

    /** The maximum value of the AtoD convertor  */
    var $AtoDMax = 1023;


    var $configvars = array();
    /**
     * Returns the packet to send to read the sensor data out of an endpoint
     * @param $Info Array Infomation about the device to use
     * @note This should only be defined in a driver that inherits this class if the packet differs
     */
    function ReadSensors($Info) {

        $packet[0] = array(
            "Command" => eDEFAULT_SENSOR_READ,
            "To" => $Info["DeviceID"],
        );
        $Packets = $this->packet->SendPacket($Info, $packet);
        if (is_array($Packets)) {
            $return = $this->InterpSensors($Info, $Packets);
            if ($return == FALSE) $return = $Packets;
        } else {
            $return = FALSE;
        }
        return($return);
    }

    /**
     * Returns the packet to send to read the sensor data out of an endpoint
     * @param $Info Array Infomation about the device to use
     * @param $packet Array The packet to save.
     * @note This should only be defined in a driver that inherits this class if the packet differs
     */
    function saveSensorData($Info, $Packets) {
        foreach($Packets as $packet) {
            if (($packet["Status"] == "GOOD")){
                if (!isset($packet['DeviceKey'])) $packet['DeviceKey'] = $Info['DeviceKey'];
                $return = $this->driver->db->AutoExecute($this->history_table, $packet, 'INSERT');
            } else {
                $return = FALSE;
            }
        }
        return($return);
    }

    /**
     * Returns the packet to send to read the sensor data out of an endpoint
     * @param $Info Array Infomation about the device to use
     * @param $packet Array The packet to save.
     * @note This should only be defined in a driver that inherits this class if the packet differs
     */
    function updateConfig($Info) {
        $return = TRUE;
        return($return);
    }

    /**
     * Checks a database record to see if it should be interpreted.
     * @param $data Array a packet that might need the 'Data' array created
     * @return Array The same packet with the 'Data' array created
     */
    function checkDataArray(&$work) {
        if (!is_array($work['Data'])) {
            for ($i = 0; $i < (strlen($work["RawData"])/2); $i++) {
                $work['Data'][$i] = hexdec(substr($work['RawData'], ($i*2), 2));
            }
        }

        return($work);
    }

    /**
     * Checks a data record to determine what its status is.  It changes
     * Rec['Status'] to reflect the status and adds Rec['Statusold'] which
     * is the status that the record had originally.
     *
     * @param array $Info The information array on the device
     * @param array $Rec The data record to check
      */
    function CheckRecord($Info, &$Rec) {
        $Rec["Status"] = 'UNRELIABLE';
    }


    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     * @note This method MUST be implemented by each driver that inherits this class
      */
    function ReadMem($Info) {
        $return = $this->BadDriver($Info, "ReadMem");    
        return($return);
    }
    
    /**
     * Gets the configuration variables from the device configuration
     *
     * These differ from the returnn of eDEFAULT::GetCols in that these are stored
     * in the device itself, rather than in the database.
     *
     * @return Array The names of all of the configuration variables
     */
    function GetConfigVars() {
        $return = array_merge($this->defconfigvars, $this->configvars);
        return($return);    
    }
    
    
    /**
     * Returns the packet to send to read the configuration out of an endpoint
     * @param $Info Array Infomation about the device to use
     * @note This should only be defined in a driver that inherits this class if the packet differs
     */
    function ReadConfig($Info) {
        $packet = array(
            array(
                "To" => $Info["DeviceID"],
                "Command" => PACKET_COMMAND_GETSETUP,
            ),
            array(
                "To" => $Info["DeviceID"],
                "Command" => PACKET_COMMAND_GETCALIBRATION,
            ),
        );
        $Packets = $this->packet->SendPacket($Info, $packet);
        return($Packets);
    }
    
    
    /**
     * Decodes the data retuned by the endpoint
     * @param $data Array Infomation about the device to use
     * @note This method MUST be implemented by each driver that inherits this class
     */
    function DecodeData($data) {
        $return = $this->BadDriver($data, "ReadMem");
        return($return);        
    }
    
    /**
     * Does something with an unsolicited packet.
     * @param $Info Array Infomation about the device to use including the unsolicited packet.
     * @return always TRUE
     * @note This method MUST be implemented by each driver that inherits this class
     */
    function Unsolicited($Info) {
        //add_debug_output("Unsolicited default failing silently.<br>\n");
        print "Unsolicited default failing silently.\n";
        return(TRUE);    
    }
    
    /**
     * Interprets a config packet
     * @param $Info Array Infomation about the device to use including the unsolicited packet.
     */
    function InterpConfig($Info) {
//        $Info['ActiveSensors'] = 0;
        return($Info);
    }
    /**
     * Finds the correct error code for why it was called
     * @param $Info Array Infomation about the device to use
     * @param $fct string The function that the code tried to run
     * @return array The error code depending on what the problem was.
        @warning This function MUST NOT be implemented in any drivers that inherit this class
     */
    function BadDriver($Info, $fct) {
        $return = FALSE;
        return($return);
    }    
    
    /**
     * The routine that interprets returned sensor data
     * @param $Info array The packet to interpret
     * @note This method MUST be implemented by each driver that inherits this class.

        This is a minimal implementation that only picks out the common things
        in all packets: DataIndex.  This happens so that if there is a driver that 
        the polling software doesn't know about, it will still at least try to download
        sensor readings from the endpoint.
        
        
        
     */
    function InterpSensors($Info, $Packets) {
//        $return = $this->BadDriver($Info, "InterpSensors");    
        $Info = $this->InterpConfig($Info);
        $ret = array();
        foreach($Packets as $key => $data) {
            $data = $this->checkDataArray($data);
            if(isset($data['RawData'])) {

//                $data = $this->InterpConfig($data);
                $return = $data;

                $index = 0; 
                $return['NumSensors'] = $Info['NumSensors'];
                $return["DataIndex"] = $data["Data"][$index++];
                $return["Driver"] = $Info["Driver"];
                if (!isset($data["Date"])) {
                    $return["Date"] = date("Y-m-d H:i:s");
                }
                
                if (!isset($return['DeviceKey'])) $return["DeviceKey"] = $Info["DeviceKey"];

                $return = $this->CheckRecord($Info, $return);
                $ret[] = $return;
            }
        }
    
        return($ret);
    }


    /**
     * Get the columns in the database that are for this endpoint
     * @param $Info Array Infomation about the device to use
     * @return The columns that pertain to this endpoint
     * @note Sound NOT be implemented in child classes that class needs it to work differently
     *
        This is used to easily display the pertinent columns for any endpoint.
     */
    function GetCols($Info){
        $Columns = $this->defcols;
        if (is_array($this->cols)) {
            $Columns = array_merge($Columns, $this->cols);
        }
        return($Columns);
    }

    /**
     * Get the columns in the database that are editable by the user
     *
     *   This function is here so that it is easy to create pages that allow these
     *   columns to be changed.
     *
     * @param $Info Array Infomation about the device to use
     * @return The columns that can be edited
     * @note Sound NOT be implemented in child classes that class needs it to work differently
     */
    function GetEditCols($Info){
        $Columns = $this->defeditcols;
        if (is_array($this->editcols)) {
            $Columns = array_merge($Columns, $this->editcols);
        }
        return($Columns);
    }
    
    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     * @todo make this function work?
     */
    function SetAllConfig($Info) {
    }
    /**
     * Runs a function using the correct driver for the endpoint
     * @param $Info Array Infomation about the device to use
     * @todo make this function work?
     */
    function GetCalibration($Info, $rawcal) {
    }

    /**
     * Returns a packet that will set the configuration data in an endpoint
     * @param $Info Array Infomation about the device to use
     * @return FALSE on failuer, The packet in array form on success
     * @todo Document this better.
     */
    function SetConfig($Info, $start, $data) {

        $buffersize = 7;


        if (is_array($data)) {
            $pktData = '';
            foreach($data as $val) {
                if (is_int($val)) {
                    $val = dechex($val);
                    $val = substr($val, 0, 2);
                    $val = str_pad($val, 2, "0", STR_PAD_LEFT);
                    $pktData .= $val;
                } else if (is_string($val)) {
                    $pktData .= $val;
                }
            }
        } else {
            $pktData = $data;    
        }
        $packets = array();
        for ($i = 0; $i < (strlen($pktData)/2); $i+=$buffersize) {
            $pkt = array();
            $pkt['Command'] = '5B';
            $pkt['To'] = $Info['DeviceID'];
            $hstart = dechex($start+$i);
            $hstart = substr($hstart, 0, 2);
            $hstart = str_pad($hstart, 2, "0", STR_PAD_LEFT);
    
            $pkt['Data'] = $hstart;

            $pkt['Data'] .= substr($pktData, ($i*2), ($buffersize*2));
            $packets[] = $pkt;            
        }
        return($packets);

    }
    
    /**
     * Runs a function using the correct driver for the endpoint
     *
     * Should NOT be implemented in child classes unless that class needs it to work differently
     *
     * @param string $ver1 The first version to use in the compare
     * @param string $ver2 The second version to use in the compare        
     * @return int -1 if $ver1 < $ver2, 0 if $ver1 == $ver2, 1 if $ver1 > $ver2
     */
    function CompareFWVersion($ver1, $ver2) {
        $v1 = explode(".", $ver1);
        $v2 = explode(".", $ver2);
        for ($i = 0; $i < 3; $i++) {
            if ($v1[$i] > $v2[$i]) {
                return(1);
            } else if ($v1[$i] < $v2[$i]) {
                return(-1);
            }
        }
        return(0);

    }        
    
    
    /**
     * Constructor.
     *   
     *   This function sets up $this->history, $this->location, and $this->averages to
     *   their default value.
     */
    function eDEFAULT(&$driver) {

        $this->driver =& $driver;
        $this->packet =& $driver->packet;
        $this->device =& $driver->device;
        $this->history =& $driver->history;
        $this->location =& $driver->location;
        $this->average =& $driver->average;
    }
}    

    

?>
