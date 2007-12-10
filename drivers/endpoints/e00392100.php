<?php
/**
 * Driver for the 0039-21 controller board
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
 */
/** This is for loading our firmware */
require_once HUGNET_INCLUDE_PATH.'/firmware.php';

if (!class_exists("e00392100")) {


    /** Reads the downstream unit serial numbers */
    define('PACKET_READDOWNSTREAMSN_COMMAND', '56');
    /** Reads the downstream unit serial numbers */
    define('PACKET_READPACKETSTATS_COMMAND', '57');
    /** Reads the downstream unit serial numbers */
    define('PACKET_HUGNETPOWER_COMMAND', '60');
    



    /**
     * Driver for the 0039-21 controller board
     *
     * @category   Drivers
     * @package    HUGnetLib
     * @subpackage Endpoints
     * @author     Scott Price <prices@hugllc.com>
     * @copyright  2007 Hunt Utilities Group, LLC
     * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
     * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
    */
    class e00392100 extends eDEFAULT
    {
    
        var $HWName = "Controller Board";
    
        var $average_table = "e00392100_average";
        var $history_table = "e00392100_history";
    
        var $devices = array(
            "DEFAULT" => array(
                "0039-21-01-A" => "DEFAULT",
            ),
        );

        var $Types = array(
            "fake" => array(0x40, 0x50, 0x02, 0x40, 0x50, 0x02),
            "real" => array(0x50, 0x02, 0x40, 0x40, 0x40, 0x40, 0x02, 0x50),
        );
        var $sensorType = array(
            "fake" => array("Controller", "Controller", 'BCTherm2322640', "Controller", "Controller", 'BCTherm2322640'),
            "real" => array("Controller", 'BCTherm2322640', "Controller", "Controller", "Controller", "Controller", 'BCTherm2322640', "Controller"),
        );
        var $labels = array(
            "DEFAULT" => array("HUGnet1 Voltage", "HUGnet1 Current", "FET Temp", "HUGnet2 Voltage", "HUGnet2 Current", "FET Temp"),
        );
    
        var $config = array(
            "DEFAULT" => array("Function" => "HUGnet Controller", "Sensors" => 6),        
        );
            
        var $cols = array(
            "NumSensors" => "# Sensors",
        );
    
        /**
         * This function reads the configuration out of the endpoint.
         *
         * @param array $Info devInfo array of the endpoint to read the configuration of
         *
         * @return mixed
         */
        function readConfig($Info)
        {
             $packet = array(
                0 => array(
                    "To" => $Info["DeviceID"],
                    "Command" => PACKET_COMMAND_GETSETUP,
                ),
                1 => array(
                    "To" => $Info["DeviceID"],
                    "Command" => PACKET_COMMAND_GETCALIBRATION,
                ),
            );
            switch ($Info['FWPartNum']) 
            {
            case '0039-20-06-C':
            case '0039-20-01-C':
                $packet[] = array(
                    "To" => $Info["DeviceID"],
                    "Command" => PACKET_READDOWNSTREAMSN_COMMAND,
                );
                $packet[] = array(
                    "To" => $Info["DeviceID"],
                    "Command" => PACKET_HUGNETPOWER_COMMAND,
                );
                break;
            default:
                break;
            };
            $Packets = $this->packet->SendPacket($Info, $packet);
            return($Packets);
        }
    
        /**
         * This function checks a record to see if it is valid
         *
         * @param array $Info devInfo array of the endpoint to read the configuration of
         * @param array &$Rec The database record to check
         *
         * @return none
         */
        function checkRecord($Info, &$Rec)
        {

            for ($i = 0; $i < $Rec['NumSensors']; $i ++) {
                if (!is_numeric($Rec['Data'.$i])) {
                    $Rec['Data'.$i] = null;
                }
            }
            parent::CheckRecordBase($Info, $Rec);
            if ($Rec["Status"] == "BAD") return;
            if ($Rec["sendCommand"] == PACKET_COMMAND_GETDATA) {
                if ($Rec["TimeConstant"] == 0) {
                    $Rec["Status"]     = "BAD";
                    $Rec["StatusCode"] = "Bad TC";
                    return;
                }
            }
            
        }
    
        /**
         * This function reads the sensors on an endpoint
         *
         * @param array $Info devInfo array of the endpoint to read the configuration of
         *
         * @return mixed
         */
        function readSensors($Info) 
        {
            $packet[0] = array(
                "Command" => EDEFAULT_SENSOR_READ,
                "To" => $Info["DeviceID"],
            );
            switch ($Info['FWPartNum']) 
            {
            case '0039-20-06-C':
            case '0039-20-01-C':
                $packet[1] = array(
                    "To" => $Info["DeviceID"],
                    "Command" => PACKET_READPACKETSTATS_COMMAND,
                );
                break;
            default:
                break;
            };
    
            $Info['sendCommand'] = PACKET_SEND_COMMAND;
    
            $Packets = $this->packet->SendPacket($Info, $packet);
            if (is_array($Packets)) {
                $return = $this->interpSensors($Info, $Packets);
            } else {
                $return = false;
            }
            return($return);
        }
    
    
        /**
         * This function interprets the configuration
         *
         * @param array &$Info devInfo array of the endpoint to read the configuration of
         *
         * @return mixed
         */
        function interpConfig(&$Info) 
        {
            $this->InterpConfigDriverInfo($Info);
            $Info["Location"] = $this->deflocation;
            $this->InterpConfigHW($Info);
            $Info["PacketTimeout"] = 2;
            $this->InterpConfigFW($Info);
            
            $Info['ActiveSensors'] = $Info["NumSensors"];
            $this->InterpConfigParams($Info);

            $this->_interpConfig00392006C($Info);
            $this->_interpConfigSensors($Info);
            $this->_interpConfigDownstream($Info);
            $this->_interpConfigHUGnetPower($Info);
        }

        /**
         * This function interprets the configuration
         *
         * @param array &$Info devInfo array of the endpoint to read the configuration of
         *
         * @return mixed
         */
        private static function _interpConfig00392006C(&$Info) 
        {
            if ($Info['FWPartNum'] == '0039-20-06-C') {
                $Info['mcu'] = array(
                    "SRAM"      => hexdec(substr($Info["DriverInfo"], 0, 4)),
                    "E2"        => hexdec(substr($Info["DriverInfo"], 4, 4)),
                    "FLASH"     => hexdec(substr($Info["DriverInfo"], 8, 6)),
                    "FLASHPAGE" => hexdec(substr($Info["DriverInfo"], 14, 4)),
                );
                if ($Info['mcu']["FLASHPAGE"] == 0) $Info['mcu']["FLASHPAGE"] = 128;
                $Info['mcu']["PAGES"] = $Info['mcu']["FLASH"]/$Info['mcu']["FLASHPAGE"];
                $Info["CRC"]          = strtoupper(substr($Info["DriverInfo"], 18, 4));
                $Info['bootLoader']   = true;
            } else {
                $Info['bootLoader'] = false;
            }
        }
        /**
         * This function interprets the configuration
         *
         * @param array &$Info devInfo array of the endpoint to read the configuration of
         *
         * @return mixed
         */
        private function _interpConfigSensors(&$Info) 
        {
            $Info["Types"]                = $this->Types["fake"];
            $Info['params']['sensorType'] = $this->sensorType["fake"];
            $this->InterpConfigSensorSetup($Info);

            if (isset($this->labels[$Info["FWPartNum"]])) {
                $Info["Location"] = $this->labels[$Info["FWPartNum"]];
            } else {
                $Info["Location"] = $this->labels["DEFAULT"];            
            }

        }
        /**
         * This function interprets the configuration
         *
         * @param array &$Info devInfo array of the endpoint to read the configuration of
         *
         * @return mixed
         */
        private static function _interpConfigDownstream(&$Info) 
        {

            if (!empty($Info["RawData"][PACKET_READDOWNSTREAMSN_COMMAND])) {
                $pkt   = &$Info["RawData"][PACKET_READDOWNSTREAMSN_COMMAND];
                $index = 0;

                $strings[0] = substr($pkt, 0, (strlen($pkt)/2));
                $strings[1] = substr($pkt, (strlen($pkt)/2));

                $Info['subDevices'] = array();
                foreach ($strings as $str) {
                    for ($i = 0; $i < strlen($str); $i += 6) {

                        $id = substr($str, $i, 6);
                        if ((strlen($id) == 6) && ($id != '000000')) {
                            $Info['subDevices'][$index][] = $id;
                        }
                    }
                    $index++;
                }
            }
        }
        /**
         * This function interprets the configuration
         *
         * @param array &$Info devInfo array of the endpoint to read the configuration of
         *
         * @return mixed
         */
        private static function _interpConfigHUGnetPower(&$Info) 
        {
            if (!empty($Info["RawData"][PACKET_HUGNETPOWER_COMMAND])) {
                $pkt = &$Info["RawData"][PACKET_HUGNETPOWER_COMMAND];
                
                $Info['HUGnetPower'][0] = (hexdec(substr($pkt, 0, 2)) == 0) ? 0 : 1;
                $Info['HUGnetPower'][1] = (hexdec(substr($pkt, 2, 2)) == 0) ? 0 : 1;
            }
        }
        
        /**
         * This function interprets the configuration
         *
         * @param array $Info devInfo array of the endpoint to read the configuration of
         *
         * @return mixed
         */
        function updateConfig($Info) 
        {
            $return = true;
            if (is_array($Info['subDevices'])) {
                foreach ($Info['subDevices'] as $index => $devList) {
                    $where  = implode("' OR DeviceID='", $devList);
                    $where  = " DeviceID='".$where."'";
                    $query  = "UPDATE devices SET ControllerKey=".$Info['DeviceKey'].", ControllerIndex=".$index." WHERE ".$where;
                    $return = $this->driver->db->query($query);
                    
                }
                $update = array(
                    'ControllerKey' => 0,
                    'ControllerIndex' => 0,
                );                
                $return = $this->driver->db->AutoExecute($this->driver->device_table, $update, 'UPDATE', 'DeviceKey='.$Info['DeviceKey']);
                
            }
        
            return($return);
        }
        
        
        /**
         * This function interprets the configuration
         *
         * @param array $Info    devInfo array of the endpoint to read the configuration of
         * @param array $Packets Array of packet data to save
         *
         * @return mixed
         */
        function saveSensorData($Info, $Packets) 
        {
            foreach ($Packets as $packet) {            
                if (isset($packet['DataIndex'])) {
                    if (($packet["Status"] == "GOOD")) {
                        if ($packet['sendCommand'] == '55') {                        
                            $return = $this->driver->db->AutoExecute($this->history_table, $packet, 'INSERT');
                        }
                    } else {
                        $return = false;
                    }
                }
            }
            return($return);
        }
    
        
        /**
         * This function interprets the configuration
         *
         * @param array $Info    devInfo array of the endpoint to read the configuration of
         * @param array $Packets Array of packet data to save
         *
         * @return mixed
         */
        function interpSensors($Info, $Packets) 
        {
            $return = array();
    
            foreach ($Packets as $data) {
                if (isset($data['RawData'])) {
                    $data           = $this->checkDataArray($data);
                    $data["Driver"] = get_class($this);
                    if (!isset($data["Date"])) {
                        $data["Date"] = date("Y-m-d H:i:s");
                    } else {
                        $data["Date"] = $data["Date"];
                    }
                    $data["DeviceKey"] = $Info["DeviceKey"];

        
                    switch($data['sendCommand']) {
                    case PACKET_READPACKETSTATS_COMMAND:
                        $loc = 0;
                        for ($index = 0; $index < 3; $index++) {
                            $data['Stats'][$index]['PacketRX']        = $data['Data'][$loc++];
                            $data['Stats'][$index]['PacketRX']       += $data['Data'][$loc++] * 0x100;
                            $data['Stats'][$index]['PacketTX']        = $data['Data'][$loc++];
                            $data['Stats'][$index]['PacketTX']       += $data['Data'][$loc++] * 0x100;
                            $data['Stats'][$index]['PacketTimeout']   = $data['Data'][$loc++];
                            $data['Stats'][$index]['PacketTimeout']  += $data['Data'][$loc++] * 0x100;
                            $data['Stats'][$index]['PacketNoBuffer']  = $data['Data'][$loc++];
                            $data['Stats'][$index]['PacketNoBuffer'] += $data['Data'][$loc++] * 0x100;
                            $data['Stats'][$index]['PacketBadCSum']   = $data['Data'][$loc++];
                            $data['Stats'][$index]['PacketBadCSum']  += $data['Data'][$loc++] * 0x100;
                            $data['Stats'][$index]['PacketSent']      = $data['Data'][$loc++];
                            $data['Stats'][$index]['PacketSent']     += $data['Data'][$loc++] * 0x100;
                            $data['Stats'][$index]['PacketGateway']   = $data['Data'][$loc++];
                            $data['Stats'][$index]['PacketGateway']  += $data['Data'][$loc++] * 0x100;
                            $data['Stats'][$index]['PacketStartTX1']  = $data['Data'][$loc++];
                            $data['Stats'][$index]['PacketStartTX1'] += $data['Data'][$loc++] * 0x100;
                            $data['Stats'][$index]['PacketStartTX2']  = $data['Data'][$loc++];
                            $data['Stats'][$index]['PacketStartTX2'] += $data['Data'][$loc++] * 0x100;
                            $data['Stats'][$index]['PacketBadIface']  = $data['Data'][$loc++];
                            $data['Stats'][$index]['PacketBadIface'] += $data['Data'][$loc++] * 0x100;
                            $data['Stats'][$index]['ByteRX']          = $data['Data'][$loc++];
                            $data['Stats'][$index]['ByteRX']         += $data['Data'][$loc++] * 0x100;
                            $data['Stats'][$index]['ByteRX']         += $data['Data'][$loc++] * 0x10000;
                            $data['Stats'][$index]['ByteRX']         += $data['Data'][$loc++] * 0x1000000;
                            $data['Stats'][$index]['ByteTX']          = $data['Data'][$loc++];
                            $data['Stats'][$index]['ByteTX']         += $data['Data'][$loc++] * 0x100;
                            $data['Stats'][$index]['ByteTX']         += $data['Data'][$loc++] * 0x10000;
                            $data['Stats'][$index]['ByteTX']         += $data['Data'][$loc++] * 0x1000000;
                            $data['Stats'][$index]['ByteTX2']         = $data['Data'][$loc++];
                            $data['Stats'][$index]['ByteTX2']        += $data['Data'][$loc++] * 0x100;
                            $data['Stats'][$index]['ByteTX2']        += $data['Data'][$loc++] * 0x10000;
                            $data['Stats'][$index]['ByteTX2']        += $data['Data'][$loc++] * 0x1000000;
                        }
                        break;        
                    default:
                        $index = 0; 
                        
                        $data["ActiveSensors"] = $Info["ActiveSensors"];
                        $data["NumSensors"]    = $Info["NumSensors"];
                        $data["TimeConstant"]  = 1;
                        $data["Types"]         = $this->Types["fake"];
                        foreach ($data["Types"] as $key => $val) {
                            if (is_null($data['Units'][$key])) {
                                $data['Units'][$key] = $this->driver->sensors->getUnits($data["Types"][$key], $this->sensorType['fake'][$key]);
                            }
                            $data['unitType'][$key] = $this->driver->sensors->getUnitType($data["Types"][$key], $this->sensorType['fake'][$key]);
                        }
                        $data["DataIndex"] = $data["Data"][$index++];
                        for ($key = 0; $index < count($data['Data']); $key++) {
                            $data["raw"][$key]  = $data["Data"][$index++];
                            $data["raw"][$key] += $data["Data"][$index++] << 8;
                        }

                        /*
                            Input 0: HUGnet2 Current
                            Input 1: HUGnet2 Temp
                            Input 2: HUGnet2 Voltage Low
                            Input 3: HUGnet2 Voltage High
                            Input 4: HUGnet1 Voltage High
                            Input 5: HUGnet1 Voltage Low
                            Input 6: HUGnet1 Temp
                            Input 7: HUGnet1 Current
                            
                            Output 0: HUGnet1 Voltage
                            Output 1: HUGnet1 Current
                            Output 2: HUGnet1 Temp
                            Output 3: HUGnet2 Voltage
                            Output 4: HUGnet2 Current
                            Output 5: HUGnet2 Temp
                        */        
                        $reads = array();
                        foreach ($this->Types["real"] as $key => $type) {
                            $sensorType  = $this->sensorType["real"][$key];
                            $reads[$key] = $this->driver->sensors->getReading($data['raw'][$key], $type, $this->sensorType["real"][$key], 1, $Info['params']['Extra'][$key]);
                        }
                        $data["Data0"]  = $reads[4] - $reads[5];
                        $data["Data1"]  = $reads[7];
                        $data["Data2"]  = $reads[6];
                        $data["Data3"]  = $reads[3] - $reads[2];
                        $data["Data4"]  = $reads[0];
                        $data["Data5"]  = $reads[1];
                        $data["Status"] = "GOOD";
                        break;

                    }
                    $this->CheckRecord($Info, $data);
                    $return[] = $data;
                }
            }    
            
            return($return);
        }
    
        /**
         * Programs a page of flash
         *
         * @param array $Info Infomation about the device to use            
         *
         * @return Array of MCU information on success, false on failure 
        */
        function getMCUInfo($Info) 
        {
            $retpkt = $this->ReadConfig($Info);
            $config = $this->driver->InterpConfig($retpkt);

            $mcu = false;
            if (is_array($config['mcu'])) {
                $mcu = $config['mcu'];
            }
            return $mcu;
        }
    
        /**
         * Programs a page of flash
         *
         * Due to the nature of flash, $Val must contain the data for
         * a whole page of flash.
         *
         * @param array  $Info Infomation about the device to use            
         * @param int    $Addr The start address of this block
         * @param string $Val  The data to program into E2 as a hex string
         *
         * @return true on success, false on failure 
         */
        function programFlashPage($Info, $Addr, $Val) 
        {
            $this->packet->Connect($Info);
            $pkt["To"]      = $Info["DeviceID"];
            $pkt["Command"] = "1C";
            $pkt["Data"]    = str_pad(dechex(($Addr>>8) & 0xFF), 2, "0", STR_PAD_LEFT);
            $pkt["Data"]   .= str_pad(dechex($Addr & 0xFF), 2, "0", STR_PAD_LEFT);    
            $pkt["Data"]   .= $Val;
            
            $retpkt = $this->packet->SendPacket($Info, array($pkt));
            $retpkt = $retpkt[0];
            if (strtoupper(trim($retpkt["RawData"])) == strtoupper(trim($Val))) {
                $return = true;
            } else {
                $return = false;
            }
            return($return);
        }
    
        /**
         * Programs a block of E2
         *
         * @param array  $Info Infomation about the device to use            
         * @param int    $Addr The start address of this block
         * @param string $Val  The data to program into E2 as a hex string
         *
         * @return true on success, false on failure 
         */
        function programE2Page($Info, $Addr, $Val) 
        {
            $this->packet->Connect($Info);
            $pkt["To"]      = $Info["DeviceID"];
            $pkt["Command"] = "1A";
    
            // Protect the first 10 bytes of E2
            if ($Addr == 0) {
                $Addr = 0xA;
                $Val  = substr($Val, 20);
            }
    
            $pkt["Data"]  = str_pad(dechex(($Addr>>8) & 0xFF), 2, "0", STR_PAD_LEFT);
            $pkt["Data"] .= str_pad(dechex($Addr & 0xFF), 2, "0", STR_PAD_LEFT);
            $pkt["Data"] .= $Val;
            
            $retpkt = $this->packet->SendPacket($Info, array($pkt));
            $retpkt = $retpkt[0];
            if (strtoupper(trim($retpkt["RawData"])) == strtoupper(trim($Val))) {
                return true;
            } else {
                return false;
            }
        }
    
        /**
         * Gets the CRC of the data
         *
         * @param array $Info Infomation about the device to use            
         *
         * @return The CRC on success, false on failure 
        */
        function getApplicationCRC($Info) 
        {
            $this->packet->Connect($Info);
            $pkt["To"]      = $Info["DeviceID"];
            $pkt["Command"] = "06";
    
            $retpkt = $this->packet->SendPacket($Info, array($pkt));
            $retpkt = $retpkt[0];
            if (is_array($retpkt)) {
                return $retpkt["RawData"];
            } else {
                return false;
            }
        }
    
        /**
         * Gets the CRC of the data
         *
         * @param array $Info Infomation about the device to use            
         *
         * @return The CRC on success, false on failure 
        */
        function setApplicationCRC($Info)
        {
            $this->packet->Connect($Info);
            $pkt["To"]      = $Info["DeviceID"];
            $pkt["Command"] = "07";
    
            $retpkt = $this->packet->SendPacket($Info, array($pkt));
            $retpkt = $retpkt[0];
            if (is_array($retpkt)) {
                return $retpkt["RawData"];
            } else {
                return false;
            }
        }
    
    
        /**
         * Runs the application
         *
         * @param array $Info Infomation about the device to use            
         *
         * @return bool true on success, false on failure
        */
        function runApplication($Info) 
        {
            $this->packet->Connect($Info);
            $pkt["To"]      = $Info["DeviceID"];
            $pkt["Command"] = "08";
    
            $retpkt = $this->packet->SendPacket($Info, array($pkt), false);
            return $retpkt[0];
        }
    
        /**
         * Runs the bootloader
         *
         * @param array $Info devInfo array about the device to use            
         *
         * @return mixed Reply Packet on success, false on failure
         *
        */
        function runBootloader($Info)
        {
            $RetT = $this->packet->ReplyTimeout;
            if ($RetT < 10) $this->packet->ReplyTimeout = 10;
            $this->packet->Connect($Info);

            $pkt["To"]      = $Info["DeviceID"];
            $pkt["Command"] = "09";
            
            $retpkt = $this->packet->SendPacket($Info, array($pkt));
            
            $this->ReplyTimeout = $RetT;

            return $retpkt[0];
        }
    
        /**
         * Runs the application
         *
         * @param array $Info   Infomation about the device to use
         * @param array $dInfo  Not Used
         * @param bool  $update Whether or not to update the device if it needs it.
         *
         * @return bool true on success, false on failure
        */
        function checkProgram($Info, $dInfo, $update=false) 
        {
            $this->InterpConfig($Info);    
            $return = false;
            if ($Info['bootLoader'] || $update) {
                //print "\r\nGetting the latest firmware... ";
                $res = $this->firmware->GetLatestFirmware('0039-20-01-C');
                print " v".$res['FirmwareVersion'];
                if ($Info['bootLoader']) {
                    print "Board is running the bootloader.\r\n";
                } else {
                    print " => ".$Info["FWVersion"];
                    if ($this->CompareFWVersion($Info["FWVersion"], $res['FirmwareVersion']) < 0) {
                        print "Crashing the running program\r\n";
                        $this->RunBootLoader($Info);
                    } else {
                        $update = false;                        
                    }
                }
                if ($Info['bootLoader'] || $update) {
                    $return = $this->loadProgram($Info, $Info, $res['FirmwareKey']);
                } else {
                    $return = true;
                }
            } else {
                $return = true;
            }
            return $return;
        }

   
        /**
         * Runs the application
         *
         * @param array $Info        Infomation about the device to use
         * @param array $gw          The gateway array to use
         * @param int   $FirmwareKey The firmware key of the program to load into memory
         *
         * @return bool true on success, false on failure
        */
        function loadProgram($Info, $gw=null, $FirmwareKey) 
        {

            $fw = $this->firmware->get($FirmwareKey);
            if (!is_array($fw)) {
                $fw = $this->firmware->GetLatestFirmware('0039-20-01-C');
            }
            
            print "\r\nProgramming the device\r\n";

            if (isset($Info['mcu'])) {
                $mcu = $Info['mcu'];
            } else {
                print "Getting MCU Info... ";
                flush();
                $mcu = $this->GetMCUInfo($Info);
                print " Done\r\n";
                flush();
            }



            if (is_array($mcu)) {
                $prog = $this->firmware->InterpSREC($fw["FirmwareCode"], $mcu["FLASH"], $mcu["FLASHPAGE"]);
                print "\r\n";
                print "V = Verified\r\n";
                print "F = Failed\r\n";
                print "\r\n";
                print "Flash Memory: (0x".dechex(count($prog))." pages)\r\n";
                print "Page   0123456789ABCDEF\r\n";
                flush();
                $oldPTimeout = $this->packet->ReplyTimeout;
                foreach ($prog as $pnum => $page) {
                    if (($pnum % 16) == 0) {
                        print '0x'.str_pad(dechex($pnum), 4, "0", STR_PAD_LEFT).' ';    
                    }
                    flush();
                    $addr  = ($pnum * $mcu["FLASHPAGE"]);
                    $tries = 0;
                    do {
                        $return = $this->programFlashPage($Info, $addr, $page);
                    } while (($return === false) && ($tries++ < 5));
                    if ($return) {
                        print "V";
                    } else {
                        print "F";
                    }
                    if ((($pnum+1) % 16) == 0) print "\r\n";        
                    flush();
                    if ($return === false) break;
                }
                print "\r\n\r\n";
                if ($return !== false) {
                    $e2 = $this->firmware->InterpSREC($fw["FirmwareData"], $mcu["E2"], 128);
                    print "E2 Memory: (0x".dechex(count($e2))." pages)\r\n";
                    print "Page   0123456789ABCDEF\r\n";
                    flush();
                    foreach ($e2 as $pnum => $page) {
                        if (($pnum % 16) == 0) {
                            print '0x'.str_pad(dechex($pnum), 4, "0", STR_PAD_LEFT).' ';    
                        }
                        flush();
                        $addr  = ($pnum * 128);
                        $tries = 0;
                        do {
                            $return = $this->programE2Page($Info, $addr, $page);
                        } while (($return === false) && ($tries++ < 5));
                        
                        if ($return) {
                            print "V";
                        } else {
                            print "F";
                            $return = false;
                        }
                        if ((($pnum+1) % 16) == 0) print "\r\n";        
                        if ($return === false) break;
                        flush();
                    }
                }
                $this->packet->ReplyTimeout = $oldPTimeout;
                print "\r\n\r\n";
                if ($return !== false) {
                    print "Getting CRC: ";
                    flush();
                    $AppCRC = $this->setApplicationCRC($Info);
                    if ($AppCRC !== false) {
                        $AppCRC = $this->getApplicationCRC($Info);
                        print $AppCRC."\r\n";
                        print "Running Program\r\n";    
                        flush();
                        $this->runApplication($Info);
                    } else {
                        print " Failed\r\n";
                    }
                } else {
                    print " Failed<br>\r\n ";
                } 
            } else {
                print " Failed<br>\r\n";        
            }
            flush();
            return $return;
        }
        /**
         * Runs the application
         *
         * @param array $Info Infomation about the device to use
         *
         * @return bool true on success, false on failure
        */
        function getPower($Info) 
        {
            $this->packet->Connect($Info);
            $pkt["To"]      = $Info["DeviceID"];
            $pkt["Command"] = "60";
    
            $retpkt = $this->packet->SendPacket($Info, $pkt);
    
            return($retpkt);
        }
    
        /**
         * Runs the application
         *
         * @param array $Info    Infomation about the device to use
         * @param int   $hugnet0 1 to turn the power on, 0 to turn the power off
         * @param int   $hugnet1 1 to turn the power on, 0 to turn the power off
         *
         * @return true on success, false on failure
        */
        function setPower($Info, $hugnet0=1, $hugnet1=1) 
        {
            /*
            $hugnet0 = ($hugnet0 == 0) ? '00' : '01';
            $hugnet1 = ($hugnet1 == 0) ? '00' : '01';
    
            $this->packet->Connect($Info);
            $pkt["To"] = $Info["DeviceID"];
            $pkt["Command"] = "60";
            $pkt["Data"] = $hugnet0.$hugnet1;
    
            $retpkt = $this->packet->SendPacket($Info, $pkt);
    
            return($retpkt);
            */
        }
    
        
        /**
         * Constructor
         *
         * @param object &$driver Driver
        */
        function e00392100 (&$driver)
        {
            parent::__construct($driver);            
            $this->firmware = new firmware($driver->db);
        }
    
    
    
    }
}    
if (method_exists($this, "add_generic")) {
    $this->add_generic(array("Name" => "e00392100", "Type" => "driver", "Class" => "e00392100", "deviceJOIN" => ""));
}
?>
