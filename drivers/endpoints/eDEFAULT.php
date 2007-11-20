<?php
/**
 *   This is the default endpoint driver and the base for all other
 *   endpoint drivers.
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

if (!class_exists('eDEFAULT')) {
    /**
     * The default driver class
     *   
     *   This is the default driver class.  All drivers MUST inherit this class.  They should
     *   build on it.  The class has some necessary stuff that doesn't need to be duplicated
     *   in each of the drivers themselves.
     *
     *  This class should only be created by the driver class.  It is specifically designed
     *  this way and creating it any other way will produce unexpected results.
     */
    class eDEFAULT {
        /**
         * Stores the device information for this driver
        
         * This array stores information on which hardware and firmware combinations
         * this driver supports.  The format is as follows:
         * @code
         * var $devices = array(
         *     "Firmwware Part #" => array(
         *         "Hardware Part #" => "Firmware version",
         *     ),
         * );
         * @endcode
         * Any of those can be set to the keyword "DEFAULT".  This matches anything
         * in that category (hardware, firmware, or firmware version).  Wildcards are
         * not currently supported.
         * 
         * @par Example
         * @code    
         * var $devices = array(
         *     "0039-20-03-C" => array(
         *         "0039-12-02-A" => "DEFAULT",
         *         "0039-12-02-B" => "DEFAULT",
         *     ),
         *     "DEFAULT" => array(
         *         "0039-12-00-A" => "DEFAULT",
         *         "0039-12-01-A" => "DEFAULT",
         *         "0039-12-02-A" => "DEFAULT",
         *         "0039-12-01-B" => "DEFAULT",
         *         "0039-12-02-B" => "DEFAULT",
         *     ),
         * 
         * );
         * @endcode
         */
        public $devices = array();
    
        /** The hardware name */
        protected $HWName = "Default";
        
        /** history table */
        protected $history_table = "history";
        /** location table
         *  @deprecated This is now stored in the 'params' field in the devices table
         */
        protected $location_table = "location";
        /** Average Table */
        protected $average_table = "average";
        /** Raw history Table */
        protected $raw_history_table = "history_raw";
        
        /** The default labels for the sensor outputs. */
        protected $labels = array(0 => "");
        /** The array of units used by the device sensor outputs (ie Degrees F, Degrees C) */
        protected $units = array(0 => "");
        /** These are the columns that all devices share  */
        private $defcols = array(
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
        private $defeditcols = array(
            "DeviceLocation" => "Location",
            "DeviceJob" => "Job",
            "BoredomThreshold" => "Boredom Threshold",
        );
        /** These are the editable columns that all devices share  */
        protected $editcols = array();
    
        /** This is where the hardware devices default configurations go. */
        public $config = array(
            "DEFAULT" => array("Function" => "Unknown", "Sensors" => 0),        
        );
    
        /** Calibration data */
        protected $caldata = array();                
        /** The columns that are device specific go here */
        protected $cols = array();                    

        var $var = array();            
    
        /** Default location variable definition  */
        protected $deflocation = array();
    
        /** The maximum value of the AtoD convertor  */
        var $AtoDMax = 1023;
    
    
        /** 
         * Default configuraion variable definition
         */
        private $configvars = array();
        /**
         * Returns the packet to send to read the sensor data out of an endpoint
         * @param array $Info Infomation about the device to use
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
         * @param array $Info Infomation about the device to use
         * @param array $packet The packet to save.
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
         * @param array $Info Infomation about the device to use
         * @param array $packet The packet to save.
         * @note This should only be defined in a driver that inherits this class if the packet differs
         */
        function updateConfig($Info) {
            $return = TRUE;
            return($return);
        }
    
        /**
         * Checks a database record to see if it should be interpreted.
         * @param array $data a packet that might need the 'Data' array created
         * @return array The same packet with the 'Data' array created
         */
        final function checkDataArray(&$work) {
            if (!is_array($work['Data'])) {
                for ($i = 0; $i < (strlen($work["RawData"])/2); $i++) {
                    $work['Data'][$i] = hexdec(substr($work['RawData'], ($i*2), 2));
                }
            }
    
            return $work;
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
         *  Gets the order of the sensors in an endpoint.
         */    
        protected function getOrder($Info, $key, $rev = FALSE) {
            if (isset($this->config[$Info["FWPartNum"]]["DisplayOrder"])) { 
                $Order = explode(",", $this->config[$Info["FWPartNum"]]["DisplayOrder"]);
                if ($rev) $Order = array_flip($Order);
                $ukey = $Order[$key];
            } else {
                $ukey = $key;
            }
            return $ukey;
        }
    
        /**
         * Read the memory of an endpoint
         *
         * @param array $Info The information array on the device
         * @return array A packet array to be sent to the packet structure ({@see EPacket})
         */
        function ReadMem($Info) {
        
            switch($Info["MemType"]) {
                case EEPROM:
                    $Type = eDEFAULT_EEPROM_READ;
                    break;
                case SRAM:
                default:
                    $Type = eDEFAULT_SRAM_READ;
                    break;
            }
            $return = array();
            $Info["Command"] = $Type;
            $Info["To"] = $Info["DeviceID"];
            $Info["Data"][0] = "00" ;
            $Info["Data"][1] = $Info["MemAddress"] & 0xFF;
            $Info["Data"][2] = $Info["MemLength"] & 0xFF;
            return($return);
        }
        
        /**
         * Gets the configuration variables from the device configuration
         *
         * These differ from the returnn of eDEFAULT::GetCols in that these are stored
         * in the device itself, rather than in the database.
         *
         * @return array The names of all of the configuration variables
         */
        function GetConfigVars() {
            $return = array_merge($this->defconfigvars, $this->configvars);
            return($return);    
        }
        
        
        /**
         * Returns the packet to send to read the configuration out of an endpoint
         * @param array $Info Infomation about the device to use
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
         * Does something with an unsolicited packet.
         *
         * @param array $Info Infomation about the device to use including the unsolicited packet.
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
         * @param array $Info Infomation about the device to use including the unsolicited packet.
         */
        function InterpConfig(&$Info) {
            eDEFAULT::InterpBaseConfig($Info);
            eDEFAULT::InterpConfigDriverInfo($Info);
            eDEFAULT::InterpCalibration($Info);
        }

        /**
         *
         */
        function InterpBaseConfig(&$Info) {
            if (isset($Info['RawData'][PACKET_COMMAND_GETSETUP])) {
                $pkt = &$Info['RawData'][PACKET_COMMAND_GETSETUP];
                if (strlen($pkt) >= PACKET_CONFIG_MINSIZE) 
                {
                    $Info["SerialNum"] = hexdec(substr($pkt, 0, 10));
                    $Info["HWPartNum"] = trim(strtoupper(substr($pkt, ENDPOINT_HW_START, 4)."-".
                                                         substr($pkt, ENDPOINT_HW_START+4, 2)."-".
                                                         substr($pkt, ENDPOINT_HW_START+6, 2)."-".
                                                         chr(hexdec(substr($pkt, ENDPOINT_HW_START+8, 2)))));
                    $Info["FWPartNum"] = trim(strtoupper(substr($pkt, ENDPOINT_FW_START, 4)."-".
                                                         substr($pkt, ENDPOINT_FW_START+4, 2)."-".
                                                         substr($pkt, ENDPOINT_FW_START+6, 2)."-".
                                                         chr(hexdec(substr($pkt, ENDPOINT_FW_START+8, 2)))));
                    $Info["FWVersion"] = trim(strtoupper(substr($pkt, ENDPOINT_FWV_START, 2).".".
                                                         substr($pkt, ENDPOINT_FWV_START+2, 2).".".
                                                         substr($pkt, ENDPOINT_FWV_START+4, 2)));
            
                    if (strlen($pkt) >= (ENDPOINT_GROUP+6)) {
                        $Info["DeviceGroup"] = trim(strtoupper(substr($pkt, ENDPOINT_GROUP, 6)));
                    }    
                    if (strlen($pkt) >= (ENDPOINT_BOREDOM+2)) {
                        $Info["BoredomThreshold"] =     hexdec(trim(strtoupper(substr($pkt, ENDPOINT_BOREDOM, 2))));
                    }            
                    $Info["RawSetup"] = $pkt;
                    devInfo::setDate($Info, "LastConfig");                    

                }
            }
        
        }

        function InterpConfigDriverInfo(&$Info) {
            if (empty($Info["DriverInfo"]) && !empty($Info["RawSetup"])) {
                $Info["DriverInfo"] = substr($Info["RawSetup"], ENDPOINT_BOREDOM+2);
            }
        
        }
        function InterpCalibration(&$Info) {
            if (isset($Info['RawData'][PACKET_COMMAND_GETCALIBRATION])) {
                $pkt = &$Info['RawData'][PACKET_COMMAND_GETCALIBRATION];
                $Info['RawCalibration'] = $pkt;
            }        
        }

        /**
         * Finds the correct error code for why it was called
         * @param array $Info Infomation about the device to use
         * @param string $fct The function that the code tried to run
         * @return bool Always FALSE
            @warning This function MUST NOT be implemented in any drivers that inherit this class
         */
        final function BadDriver($Info, $fct) {
            return FALSE;
        }    
        
        /**
         * The routine that interprets returned sensor data
         * @param array $Info The device info array
         * @param array $Packets An array of packets to interpret
         * @note This method MUST be implemented by each driver that inherits this class.
         *
         *   This is a minimal implementation that only picks out the common things
         *   in all packets: DataIndex.  This happens so that if there is a driver that 
         *   the polling software doesn't know about, it will still at least try to download
         *   sensor readings from the endpoint.
         *   
         */
        function InterpSensors($Info, $Packets) {
            $Info = $this->InterpConfig($Info);
            $ret = array();
            foreach($Packets as $key => $data) {
                $data = $this->checkDataArray($data);
                if(isset($data['RawData'])) {
    
                    $return = $data;
    
                    $index = 0; 
                    $Info['NumSensors'] = $Info['NumSensors'];
                    $Info["DataIndex"] = $data["Data"][$index++];
                    $Info["Driver"] = $Info["Driver"];
                    if (!isset($data["Date"])) {
                        $Info["Date"] = date("Y-m-d H:i:s");
                    }
                    
                    if (!isset($Info['DeviceKey'])) $Info["DeviceKey"] = $Info["DeviceKey"];
    
                    $return = $this->CheckRecord($Info, $return);
                    $ret[] = $return;
                }
            }
        
            return($ret);
        }
    
    
        /**
         * Get the columns in the database that are for this endpoint
         * @param array $Info Infomation about the device to use
         * @return array The columns that pertain to this endpoint
         * @note Sound NOT be implemented in child classes that class needs it to work differently
         *
         *   This is used to easily display the pertinent columns for any endpoint.
         */
        final function GetCols($Info){
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
         * @param array $Info Infomation about the device to use
         * @return array The columns that can be edited
         * @note Sound NOT be implemented in child classes that class needs it to work differently
         */
        final function GetEditCols($Info){
            $Columns = $this->defeditcols;
            if (is_array($this->editcols)) {
                $Columns = array_merge($Columns, $this->editcols);
            }
            return($Columns);
        }
        
        /**
         * I am not sure what this function was for.
         *
         * @param array $Info Infomation about the device to use
         * @todo Figure out what this function was supposed to do and
         *    either fix it or remove it.
         */
        function SetAllConfig($Info) {
        }
        /**
         * Gets calibration data for this endpoint
         * @param array $Info Infomation about the device to use
         * @param string $rawcal The raw calibration data to use
         * @todo make this function work?
         */
        function GetCalibration($Info, $rawcal) {
        }
    
        /**
         * Returns a packet that will set the configuration data in an endpoint
         *
         * @param array $Info Infomation about the device to use
         * @param int $start Infomation about the device to use
         * @param mixed $data The data either as an array or in hexified form
         * @return FALSE on failure, The packet in array form on success
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
        final function CompareFWVersion($ver1, $ver2) {
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
         * Gets the name of the history table for a particular device
         *
         * @param $Info Array Infomation about the device to use
         * @return mixed The name of the table as a string on success, FALSE on failure
         */
        final public function getHistoryTable() {
            return $this->history_table;
        }
    
        /**
         * Gets the name of the average table for a particular device
         *
         * @param $Info Array Infomation about the device to use
         * @return mixed The name of the table as a string on success, FALSE on failure
         */
        final public function getAverageTable() {
            return $this->average_table;
        }
    
        /**
         * Gets the name of the location table for a particular device
         *
         * @param $Info Array Infomation about the device to use
         * @return mixed The name of the table as a string on success, FALSE on failure
         */
        final public function getLocationTable() {
            return $this->location_table;
        }
            
        
        /**
         * Constructor.
         *   
         *   This function sets up $this->history, $this->location, and $this->averages to
         *   their default value.
         *
         * @param object $driver An object of class Driver.
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
}
?>