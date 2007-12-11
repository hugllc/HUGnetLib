<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
 *
 */
/** The default command to read config  */
define("EDEFAULT_CONFIG_COMMAND", "5C");
/** The default command to read sensors  */
define("EDEFAULT_SENSOR_READ", "55");
/** The default command to set the group  */
define("EDEFAULT_SETGROUP", "5B");

if (!class_exists('eDEFAULT')) {
    /**
     * The default driver class
     * 
     * This is the default driver class.  All drivers MUST inherit this class.  They should
     * build on it.  The class has some necessary stuff that doesn't need to be duplicated
     * in each of the drivers themselves.
     *
     *  This class should only be created by the driver class.  It is specifically designed
     *  this way and creating it any other way will produce unexpected results.
     *
     * @category   Drivers
     * @package    HUGnetLib
     * @subpackage Endpoints
     * @author     Scott Price <prices@hugllc.com>
     * @copyright  2007 Hunt Utilities Group, LLC
     * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
     * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
     */
    class eDEFAULT {
        /**
         * Stores the device information for this driver
        
         * This array stores information on which hardware and firmware combinations
         * this driver supports.  The format is as follows:
         * @code
         * var $devices = array(
         *   "Firmwware Part #" => array(
         *       "Hardware Part #" => "Firmware version",
         *   ),
         * );
         * @endcode
         * Any of those can be set to the keyword "DEFAULT".  This matches anything
         * in that category (hardware, firmware, or firmware version).  Wildcards are
         * not currently supported.
         * 
         * @par Example
         * @code    
         * var $devices = array(
         *   "0039-20-03-C" => array(
         *       "0039-12-02-A" => "DEFAULT",
         *       "0039-12-02-B" => "DEFAULT",
         *   ),
         *   "DEFAULT" => array(
         *       "0039-12-00-A" => "DEFAULT",
         *       "0039-12-01-A" => "DEFAULT",
         *       "0039-12-02-A" => "DEFAULT",
         *       "0039-12-01-B" => "DEFAULT",
         *       "0039-12-02-B" => "DEFAULT",
         *   ),
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

        /** Not sure here */
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
         *
         * This should only be defined in a driver that inherits this class if the packet differs
         *
         * @param array $Info Infomation about the device to use
         *
         * @return none
         */
        public function readSensors($Info) {
    
            return array(
                array(
                    "To"      => $Info["DeviceID"],
                    "Command" => EDEFAULT_SENSOR_READ,
                ),
            );
        }
    
        /**
         * Returns the packet to send to read the sensor data out of an endpoint
         *
         * This should only be defined in a driver that inherits this class if the packet differs
         *
         * @param array $Info Infomation about the device to use
         * @param array $packet The packet to save.
         *
         * @return bool
         */
        public function saveSensorData($Info, $Packets) {
            foreach ($Packets as $packet) {
                if (($packet["Status"] == "GOOD")) {
                    if (!isset($packet['DeviceKey'])) $packet['DeviceKey'] = $Info['DeviceKey'];
                    $return = $this->driver->db->AutoExecute($this->history_table, $packet, 'INSERT');
                } else {
                    $return = false;
                }
            }
            return $return;
        }
    
        /**
         * Not sure what this function was supposed to do
         *
         * @param array $Info Infomation about the device to use
         * @param array $packet The packet to save.
         *
         * @return bool Always true
         */
        public function updateConfig($Info) 
        {
            return true;
        }
    
        /**
         * Checks a database record to see if it should be interpreted.
         *
         * @param array $data a packet that might need the 'Data' array created
         *
         * @return array The same packet with the 'Data' array created
         */
        final public function checkDataArray(&$work) 
        {
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
         *
         * @return none
         */
        public function checkRecord($Info, &$Rec) 
        {
            $Rec["Status"] = "UNRELIABLE";
        }    
        /**
         * Checks a data record to determine what its status is.  It changes
         * Rec['Status'] to reflect the status and adds Rec['Statusold'] which
         * is the status that the record had originally.
         *
         * @param array $Info The information array on the device
         * @param array $Rec The data record to check
         *
         * @return none
         */
        protected function _checkRecordBase($Info, &$Rec) 
        {
        
            if (isset($Rec['Status'])) {
                $Rec['StatusOld'] = $Rec['Status'];
            }
    
            if (empty($Rec['RawData'])) {
                $Rec["Status"] = 'BAD';
                return;
            }
            $Rec['Status'] = "GOOD";            
            
            $Bad = 0;
    
            $zero = true;
            for ($i = 0; $i < $Rec['NumSensors']; $i ++) {
                if (!is_null($Rec['Data'.$i])) {
                    $zero = false;
                    break;
                }
            }
    
            if ($zero && ($i > 3)) {
                $Rec["Status"] = "BAD";
                $Rec["StatusCode"] = "All Bad";
                return;
            }
        }

        /**
         *  Gets the order of the sensors in an endpoint.
         *
         * @param array $Info devInfo array for the device we are working with
         * @param int   $key  The array key we are currently working with
         * @param bool  $rev  Should we do them in reverse order
         *
         * @return int
         */    
        protected function _getOrder($Info, $key, $rev = false) 
        {
            if (isset($this->config[$Info["FWPartNum"]]["DisplayOrder"])) { 
                $Order = explode(",", $this->config[$Info["FWPartNum"]]["DisplayOrder"]);
                if ($rev) $Order = array_flip($Order);
                return $Order[$key];
            } else {
                return $key;
            }
        }
    
        /**
         * Read the memory of an endpoint
         *
         * @param array $Info The information array on the device
         *
         * @return array A packet array to be sent to the packet structure ({@see EPacket})
         */
        public function readMem($Info) 
        {
        
            switch($Info["MemType"]) {
                case EEPROM:
                    $Type = EDEFAULT_EEPROM_READ;
                    break;
                case SRAM:
                default:
                    $Type = EDEFAULT_SRAM_READ;
                    break;
            }
            $return = array();
            $Info["Command"] = $Type;
            $Info["To"]      = $Info["DeviceID"];
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
        public function getConfigVars() 
        {
            $return = array_merge($this->defconfigvars, $this->configvars);
            return($return);    
        }
        
        
        /**
         * Returns the packet to send to read the configuration out of an endpoint
         * @param array $Info Infomation about the device to use
         * @note This should only be defined in a driver that inherits this class if the packet differs
          */
        public function readConfig($Info) 
        {
            return array(
                array(
                    "To" => $Info["DeviceID"],
                    "Command" => PACKET_COMMAND_GETSETUP,
                ),
                array(
                    "To" => $Info["DeviceID"],
                    "Command" => PACKET_COMMAND_GETCALIBRATION,
                ),
            );
        }
                
        /**
         * Does something with an unsolicited packet.
         *
         * This method MUST be implemented by each driver that inherits this class
         *
         * @param array $Info Infomation about the device to use including the unsolicited packet.
         *
         * @return always true
         */
        public function unsolicited($Info) 
        {
            //add_debug_output("Unsolicited default failing silently.<br>\n");
            print "Unsolicited default failing silently.\n";
            return(true);    
        }
        
        /**
         * Interprets a config packet
         *
         * @param array &$Info devInfo array
         *
         * @return none
         */
        public function interpConfig(&$Info) 
        {
            eDEFAULT::_interpBaseConfig($Info);
            eDEFAULT::_interpCalibration($Info);
        }

        /**
         * This is the basic configuration that all endpoints have
         *
         * @param array $Info devInfo array
         *
         * @return none
         */
        protected function _interpBaseConfig(&$Info) {
            if (strlen($Info['RawData'][PACKET_COMMAND_GETSETUP]) > PACKET_CONFIG_MINSIZE) {
                $pkt = &$Info['RawData'][PACKET_COMMAND_GETSETUP];
                
                $Info["SerialNum"]        = hexdec(substr($pkt, 0, 10));
                $Info["HWPartNum"]        = devInfo::dehexifyPartNum(substr($pkt, ENDPOINT_HW_START, 10));
                $Info["FWPartNum"]        = devInfo::dehexifyPartNum(substr($pkt, ENDPOINT_FW_START, 10));
                $Info["FWVersion"]        = devInfo::dehexifyVersion(substr($pkt, ENDPOINT_FWV_START, 6));
                $Info["DeviceGroup"]      = trim(strtoupper(substr($pkt, ENDPOINT_GROUP, 6)));
                $Info["BoredomThreshold"] = hexdec(trim(strtoupper(substr($pkt, ENDPOINT_BOREDOM, 2))));
                $Info["RawSetup"]         = $pkt;
                devInfo::setDate($Info, "LastConfig");
                self::_interpConfigDriverInfo($Info);
            }
        
        }
        /**
         * Adds the DriverInfo to the devInfo array
         *
         * @param array $Info devInfo array
         *
         * @return none
         */
        protected function _interpConfigDriverInfo(&$Info) 
        {
            if (empty($Info["DriverInfo"]) && !empty($Info["RawSetup"])) {
                $Info["DriverInfo"] = substr($Info["RawSetup"], ENDPOINT_BOREDOM+2);
            }
        
        }
        /**
         * Adds the params to the devInfo array
         *
         * @param array $Info devInfo array
         *
         * @return none
         */
        protected function _interpConfigParams(&$Info) 
        {
            device::decodeParams($Info['params']);
        }
        /**
         * Adds the hardware information to the devInfo array
         *
         * @param array $Info devInfo array
         *
         * @return none
         */
        protected function _interpConfigHW(&$Info) 
        {
            $Info['HWName'] = $this->HWName;
        }
        /**
         * Adds the firmware information to the devInfo array
         *
         * @param array $Info devInfo array
         *
         * @return none
         */
        protected function _interpConfigFW(&$Info) 
        {
            if (isset($this->config[$Info["FWPartNum"]])) {
                $Info["NumSensors"] = $this->config[$Info["FWPartNum"]]["Sensors"];    
                $Info["Function"]   = $this->config[$Info["FWPartNum"]]["Function"];
            } else {
                $Info["NumSensors"] = $this->config["DEFAULT"]["Sensors"];    
                $Info["Function"]   = $this->config["DEFAULT"]["Function"];
            }        
        }
        /**
         * Adds the calibration information to the devInfo array
         *
         * @param array $Info devInfo array
         *
         * @return none
         */
        protected function _interpCalibration(&$Info) 
        {
            if (isset($Info['RawData'][PACKET_COMMAND_GETCALIBRATION])) {
                $pkt = &$Info['RawData'][PACKET_COMMAND_GETCALIBRATION];

                $Info['RawCalibration'] = $pkt;
            }        
        }

        /**
         * Adds the Types array to the devInfo array
         *
         * @param array $Info devInfo array
         *
         * @return none
         */
        protected function _interpTypes(&$Info) 
        {
            for ($i = 0; $i < $Info["NumSensors"]; $i++) {
                
                $key = $this->_getOrder($Info, $i);
                
                if (!isset($Info['Types'][$i])) {
                    $Info["Types"][$i] = hexdec(substr($Info["DriverInfo"], (($key*2)+2), 2));
                }
            }
        }
        /**
         * This sets up all of the data on the sensors.
         *
         * @param array &$Info The devInfo array of the device we are working with.
         *
         * @return none
         */
        protected function _interpConfigSensorSetup(&$Info) 
        {
            $Info["unitType"] = array();
            $Info["Labels"]   = array();
            $Info["Units"]    = array();
            $Info["dType"]    = array();
            $Info["doTotal"]  = array();
            for ($i = 0; $i < $Info["NumSensors"]; $i++) {
                $Info["unitType"][$i] = $this->sensors->getUnitType($Info["Types"][$i], $Info['params']['sensorType'][$i]);
                $Info["Labels"][$i]   = $Info['unitType'][$i]; //$this->driver->sensors->getUnitType($Info["Types"][$i], $Info['params']['sensorType'][$i]);
                $Info["Units"][$i]    = $this->sensors->getUnits($Info["Types"][$i], $Info['params']['sensorType'][$i]);    
                $Info["dType"][$i]    = $this->sensors->getUnitDefMode($Info["Types"][$i], $Info['params']['sensorType'][$i], $Info["Units"][$i]);    
                $Info["doTotal"][$i]  = $this->sensors->doTotal($Info["Types"][$i], $Info['params']['sensorType'][$i]);
            }
        
        }
        /**
         * This gets the time constant
         *
         * @param array &$Info The devInfo array of the device we are working with.
         *
         * @return none
         */
        protected function _interpConfigTC(&$Info) 
        {
            if ($Info["NumSensors"] > 0) {
                $Info["TimeConstant"] = hexdec(substr($Info["DriverInfo"], 0, 2));
                if ($Info["TimeConstant"] == 0) $Info["TimeConstant"] = hexdec(substr($Info["RawSetup"], E00391102B_TC, 4));
            } else {
                $Info["TimeConstant"] = 0;
            }
        
        }


        /**
         * Finds the correct error code for why it was called
         *
         * @param array $Info Infomation about the device to use
         * @param string $fct The function that the code tried to run
         *
         * @return bool Always false
         */
        final public function BadDriver($Info, $fct) 
        {
            return false;
        }    
        
        /**
         * The routine that interprets returned sensor data
         *
         * This is a minimal implementation that only picks out the common things
         * in all packets: DataIndex.  This happens so that if there is a driver that 
         * the polling software doesn't know about, it will still at least try to download
         * sensor readings from the endpoint.
         *
         * This method MUST be implemented by each driver that inherits this class.
         * 
         * @param array $Info The device info array
         * @param array $Packets An array of packets to interpret
         *
         * @return array
         */
        public function interpSensors($Info, $Packets) 
        {
            $Info = $this->interpConfig($Info);
            $ret  = array();
            foreach ($Packets as $key => $data) {
                $data = $this->checkDataArray($data);
                if (isset($data['RawData'])) {
                    $index = 3;
                    $this->_interpSensorsSetData($Info, $data);
                    $this->_interpSensorsGetData($data["Data"], &$index, 3);
    
                    $return = $this->checkRecord($Info, $data);
                    $ret[]  = $data;
                }
            }
        
            return $ret;
        }
    
        /**
         * Sets the initial data to be returned with the sensors
         *
         * @param array &$Info The devInfo array for the device we are looking at
         * @param array &$data The data array we are building.
         *
         * @return none
         */
        protected function _interpSensorsSetData(&$Info, &$data) 
        {
            $data['NumSensors']    = $Info['NumSensors'];
            $data["ActiveSensors"] = $Info["ActiveSensors"];
            $data["Driver"]        = $Info["Driver"];
            $data["DeviceKey"]     = $Info["DeviceKey"];
            $data["Types"]         = $Info["Types"];
            $data["DataIndex"]     = $data["Data"][0];
            $oldtc                 = $data["Data"][1];  // There is nothing here.
            $data["TimeConstant"]  = $data["Data"][2];
            if ($data["TimeConstant"] == 0) $data["TimeConstant"] = $oldtc;

        }
    
        /**
         *  Gets bytes of data out of the raw data string
         *
         * @param array $Data   The raw data with one byte per element
         * @param int   &$index The index in the array
         * @param int   $bytes  How many bytes to take from the array
         * @param int   $width  The field width.  This must be >= $bytes
         *
         * @return int
         */    
        protected function _interpSensorsGetData($Data, &$index, $bytes, $width=null) 
        {
            if ($width < $bytes) $width = $bytes;
            $shift = 0;
            $byte  = 0;
            for ($i = 0; $i < $bytes; $i++) {
                $byte  += $Data[$index++] << $shift;
                $shift += 8;
            }
            $index += ($width - $bytes);
            return $byte;
        }
        /**
         * Get the columns in the database that are for this endpoint
         *
         * This is used to easily display the pertinent columns for any endpoint.
         *
         * Should NOT be implemented in child classes that class needs it to work differently
         *
         * @param array $Info Infomation about the device to use
         *
         * @return array The columns that pertain to this endpoint
         */
        final public function GetCols($Info) 
        {
            $Columns = $this->defcols;
            if (is_array($this->cols)) {
                $Columns = array_merge($Columns, $this->cols);
            }
            return($Columns);
        }
    
        /**
         * Get the columns in the database that are editable by the user
         *
         * This function is here so that it is easy to create pages that allow these
         * columns to be changed.
         *
         * Should NOT be implemented in child classes that class needs it to work differently
         *
         * @param array $Info Infomation about the device to use
         *
         * @return array The columns that can be edited
         */
        final public function getEditCols($Info) 
        {
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
         *
         * @return none
         *
         * @todo Figure out what this function was supposed to do and
         *  either fix it or remove it.
         */
        public function loadAllConfig($Info) 
        {
        }
        /**
         * Gets calibration data for this endpoint
         *
         * @param array $Info Infomation about the device to use
         * @param string $rawcal The raw calibration data to use
         *
         * @return none
         *
         * @todo make this function work?
         */
        public function readCalibration($Info, $rawcal) 
        {
        }
    
        /**
         * Returns a packet that will set the configuration data in an endpoint
         *
         * @param array $Info Infomation about the device to use
         * @param int $start Infomation about the device to use
         * @param mixed $data The data either as an array or in hexified form
         *
         * @return false on failure, The packet in array form on success
         *
         * @todo Document this better.
         */
        public function loadConfig($Info, $start, $data) {
    
            $buffersize = 7;
    
    
            if (is_array($data)) {
                $pktData = '';
                foreach ($data as $val) {
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
                $pkt            = array();
                $pkt['Command'] = '5B';
                $pkt['To']      = $Info['DeviceID'];

                $hstart = dechex($start+$i);
                $hstart = substr($hstart, 0, 2);
                $hstart = str_pad($hstart, 2, "0", STR_PAD_LEFT);
        
                $pkt['Data']  = $hstart;
                $pkt['Data'] .= substr($pktData, ($i*2), ($buffersize*2));
                $packets[] = $pkt;            
            }
            return($packets);
    
        }
        
        /**
         * Runs a function using the correct driver for the endpoint
         *
         * @param string $ver1 The first version to use in the compare
         * @param string $ver2 The second version to use in the compare        
         *
         * @return int -1 if $ver1 < $ver2, 0 if $ver1 == $ver2, 1 if $ver1 > $ver2
         */
        final public function CompareFWVersion($ver1, $ver2) 
        {
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
         *
         * @return mixed The name of the table as a string on success, false on failure
         */
        final public function getHistoryTable() 
        {
            return $this->history_table;
        }
    
        /**
         * Gets the name of the average table for a particular device
         *
         * @param $Info Array Infomation about the device to use
         *
         * @return mixed The name of the table as a string on success, false on failure
         */
        final public function getAverageTable() 
        {
            return $this->average_table;
        }
    
        /**
         * Gets the name of the location table for a particular device
         *
         * @param $Info Array Infomation about the device to use
         *
         * @return mixed The name of the table as a string on success, false on failure
         */
        final public function getLocationTable() 
        {
            return $this->location_table;
        }
            
        
        /**
         * Constructor.
         * 
         * This function sets up $this->history, $this->location, and $this->averages to
         * their default value.
         *
         * @param object $driver An object of class Driver.
         *
         * @return none
         */
        public function __construct(&$driver) 
        {
            $this->driver =& $driver;
            $this->packet =& $driver->packet;
            $this->device =& $driver->device;
            $this->sensors =& $driver->sensors;
        }
    }    
}
?>