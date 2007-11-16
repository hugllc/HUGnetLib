<?php
/**
 *   This is the driver code for the 0039-28 endpoints.
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
 ** @license http://opensource.org/licenses/gpl-license.php GNU Public License
 ** @package HUGnetLib
 ** @subpackage Endpoints
 ** @copyright 2007 Hunt Utilities Group, LLC
 ** @author Scott Price <prices@hugllc.com>
 ** @version $Id$    
 *
 */

if (!class_exists("e00392800")) {


    /**
     * Driver for the 0039-12 endpoint board and select firmwares
    */
    class e00392800 extends eDEFAULT{

        var $HWName = "0039-28 Endpoint";

        var $average_table = "e00392800_average";
        var $history_table = "e00392800_history";

        var $devices = array(
            "0039-20-12-C" => array(
                "0039-28-01-A" => "DEFAULT",
                "0039-28-01-B" => "DEFAULT",
                "0039-28-01-C" => "DEFAULT",
            ),        
            "0039-20-13-C" => array(
                "0039-28-01-A" => "DEFAULT",
                "0039-28-01-B" => "DEFAULT",
                "0039-28-01-C" => "DEFAULT",
            ),        
            "DEFAULT" => array(
                "0039-28-01-A" => "DEFAULT",
                "0039-28-01-B" => "DEFAULT",
                "0039-28-01-C" => "DEFAULT",
            ),

        );

        var $deflocation = array("Sensor 1", "Sensor 2", "Sensor 3", "Sensor 4", "Sensor 5", "Sensor 6", "Sensor 7", "Sensor 8", "Sensor 9");

        var $config = array(
            "0039-20-12-C" => array("Function" => "Pulse Counter", "Sensors" => 4),        
            "0039-20-13-C" => array("Function" => "Sensor Board", "Sensors" => 16),        
            "DEFAULT" => array("Function" => "Unknown", "Sensors" => 16),        
        );


        /**
         * Calibration data
        */
    
        /**
         * Extra columns to display for these endpoints
        */
        var $cols = array("TimeConstant" => "Time Constant", 
                                "ActiveSensors" => "Active Sensors",
                                "NumSensors" => "# Sensors",
                                );

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
            );
            $return = $this->packet->SendPacket($Info, $packet);

            if (is_array($return) && (count($return) > 0)) {
                $packet = array();
                for($i = 0; $i < $this->calParts; $i++) {
                    $packet[] = array(
                        "To" => $Info["DeviceID"],
                        "Command" => PACKET_COMMAND_GETCALIBRATION,
                        "Data" => $this->packet->hexify($i),
                    );
                }
                $Packets = $this->packet->SendPacket($Info, $packet);
                if (count($Packets) == $this->calParts) {
                    $return['cal'] = $Packets[0];
                    for ($i = 1; $i < $this->calParts; $i++) {
                        $return['cal']['RawData'] .= $Packets[$i]['RawData'];
                    }
                }
            }
            return($return);
        }

        /**
         *
         */
        function CheckRecord($Info, &$Rec) {
        
            if (isset($Rec['Status'])) {
                $Rec['StatusOld'] = $Rec['Status'];
            }
            if (empty($Rec['RawData'])) {
                $Rec["Status"] = 'BAD';
                return ;
            }
            // Start with the assumption that the record is good.
            $Rec['Status'] = "GOOD";            

            $zero = TRUE;
            for($i = 0; $i < $Rec['NumSensors']; $i ++) {
                if (!is_null($Rec['Data'.$i])) {
                    $zero = FALSE;
                    break;
                }
            }
            if ($zero && ($i > 3)) {
                $Rec["Status"] = "BAD";
                $Rec["StatusCode"] = "All Bad";
                return;
            }

            if ($Rec["TimeConstant"] == 0) {
                $Rec["Status"] = "BAD";
                $Rec["StatusCode"] = "Bad TC";
                return;
            }

        }
    
        
        /**
         *
         */
        function InterpConfig(&$Info) {
            //$Info["Location"] = $this->deflocation;

            $Info['HWName'] = $this->HWName;

            If (isset($this->config[$Info["FWPartNum"]])) {
                $Info["NumSensors"] = $this->config[$Info["FWPartNum"]]["Sensors"];    
                $Info["Function"] = $this->config[$Info["FWPartNum"]]["Function"];
/*
                if (isset($this->config[$Info["FWPartNum"]]["DisplayOrder"])) {
                    $Info["DisplayOrder"] = explode(",", $this->config[$Info["FWPartNum"]]["DisplayOrder"]);
                }
*/
            } else {
                $Info["NumSensors"] = $this->config["DEFAULT"]["Sensors"];    
                $Info["Function"] = $this->config["DEFAULT"]["Function"];
            }
            if ($Info["NumSensors"] > 0) {
                $Info["TimeConstant"] = hexdec(substr($Info["RawSetup"], e00391102B_TC, 2));
                if ($Info["TimeConstant"] == 0) $Info["TimeConstant"] = hexdec(substr($Info["RawSetup"], e00391102B_TC, 4));
            } else {
                $Info["TimeConstant"] = 0;
            }

            $Info['DriverInfo'] = substr($Info["RawSetup"], e00391102B_TC);
//            $Info["Function"] = "Temperature/Moisture Sensor";    
//            $start = strlen($Info["RawSetup"]) - (2*$this->config[$Info["FWPartNum"]]["Sensors"]);
            $start = 46;
            $Info["Types"] = array();
            $Info["Labels"] = array();
            $Info["Units"] = array();
            device::decodeParams($Info['params']);

            switch(trim(strtoupper($Info["FWPartNum"]))) {
            case "0039-20-12-C":
                $Info["Types"] = array(0 => 0x70, 1 => 0x70, 2 => 0x71, 3 => 0x72);
            default:
                break;
            }

            for ($i = 0; $i < $this->config[$Info["FWPartNum"]]["Sensors"]; $i++) {
                
                $key = $this->getOrder($Info, $i);
                
                if (!isset($Info['Types'][$i])) {
                    if ($start > e00391102B_TC) {
                        $Info["Types"][$i] = hexdec(substr($Info["RawSetup"], ($start + ($key*2)), 2));
                    } else {
                        $Info["Types"][$i] = 0;
                    }
                }
                $Info["unitType"][$i] = $this->driver->sensors->getUnitType($Info["Types"][$i], $Info['params']['sensorType'][$i]);
                $Info["Labels"][$i] = $Info['unitType'][$i]; //$this->driver->sensors->getUnitType($Info["Types"][$i], $Info['params']['sensorType'][$i]);
                $Info["Units"][$i] = $this->driver->sensors->getUnits($Info["Types"][$i], $Info['params']['sensorType'][$i]);    
                $Info["dType"][$i] = $this->driver->sensors->getUnitDefMode($Info["Types"][$i], $Info['params']['sensorType'][$i], $Info["Units"][$i]);    
                $Info["doTotal"][$i] = $this->driver->sensors->doTotal($Info["Types"][$i], $Info['params']['sensorType'][$i]);
            }

            return($Info);
        }

        /**
         *
         */
        function InterpSensors($Info, $Packets) {
            $Info = $this->InterpConfig($Info);
            $ret = array();

            unset($lastPacket);
            foreach($Packets as $key => $data) {
                $data = $this->checkDataArray($data);
                $index = 0; 
                if(isset($data['RawData'])) {
                    $data['NumSensors'] = $Info['NumSensors'];
                    $data["ActiveSensors"] = $Info["ActiveSensors"];
                    $data["Driver"] = $Info["Driver"];
                    $data["DeviceKey"] = $Info["DeviceKey"];
                    $data["Types"] = $Info["Types"];
                    $data['params'] = $Info['params'];
                    $data["DataIndex"] = $data["Data"][$index++];
                    $index++;  // There is nothing here.
                    $data["TimeConstant"] = $data["Data"][$index++];

                    if (is_array($data["Data"])) {
                        for ($i = 0; $i < $Info["NumSensors"]; $i++) {
                            $key = $this->getOrder($Info, $i, TRUE);

                            if (isset($data["Data"][$index])) {
                                switch ($Info["Types"][$key]) {
                                    case 0x6F:
                                        $d = $data["Data"][$index];
            //                                    $d = $d ^ 0xF0;  // invert the top half of the value.
                                        $data["raw"][$key] = $d;
                                        $index += 3;
                                        break;                                            
                                    case 2:
                                    case 0:
                                    case 0x30:
                                    case 0x70:
                                    case 0x71:
                                    case 0x10:
                                    default:
                                        $data["raw"][$key] = $data["Data"][$index++];
                                        $data["raw"][$key] += $data["Data"][$index++] << 8;
                                        $data["raw"][$key] += $data["Data"][$index++] << 16;
                                        break;                                            
            
                                }                    
                            }        
                        }
                        
                    }
                    $this->driver->sensors->decodeData($Info, $data);
                    $this->checkRecord($Info, $data);
                    $ret[] = $data;
                }
            }
            return($ret);
        }

        /**
         * Constructor
         *
         * @param object $driver Object of class driver.
        */    
        function e00392800 (&$driver) {
            parent::eDEFAULT($driver);
        }



    }

}

// Protect us in case this is included differently
if (method_exists($this, 'add_generic')) {
    $this->add_generic(array("Name" => "e00392800", "Type" => "driver", "Class" => "e00392800", "deviceJOIN" => "e00391200_location"));
}

?>
