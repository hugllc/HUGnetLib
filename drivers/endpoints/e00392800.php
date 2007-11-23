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
            parent::CheckRecordBase($Info, $Rec);    
            if ($Rec["Status"] == "BAD") return;
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
            $this->InterpConfigDriverInfo($Info);
            $this->InterpConfigHW($Info);
            $this->InterpConfigFW($Info);
            $this->InterpConfigTC($Info);
            $this->InterpConfigParams($Info);
            $this->InterpConfig00392012C($Info);
            $this->InterpTypes($Info);
            $this->InterpConfigSensorSetup($Info);
        }
        /**
         *
         */
        private function InterpConfig00392012C(&$Info) {
            if ($Info["FWPartNum"] == "0039-20-12-C") {
                $Info["Types"] = array(0 => 0x70, 1 => 0x70, 2 => 0x71, 3 => 0x72);
            }
        }

        /**
         *
         */
        function InterpSensors($Info, $Packets) {
            $this->InterpConfig($Info);
            $ret = array();

            unset($lastPacket);
            foreach($Packets as $key => $data) {
                $data = $this->checkDataArray($data);
                if(isset($data['RawData'])) {
                    self::InterpSensorsSetData($Info, $data);
                    $index = 3; 
                    self::InterpSensorsGetRaw($Info, $data);
                    $this->driver->sensors->decodeData($Info, $data);
                    $this->checkRecord($Info, $data);
                    $ret[] = $data;
                }
            }
            return($ret);
        }

        /**
         *
         */
        private function InterpSensorsGetRaw(&$Info, &$data) {
            if (is_array($data["Data"])) {
                $index = 3;
                for ($i = 0; $i < $Info["NumSensors"]; $i++) {
                    $key = $this->getOrder($Info, $i, TRUE);
                    if ($Info["Types"][$key] == 0x6F) {
                        $data["raw"][$key] = $this->InterpSensorsGetData($data["Data"], &$index, 1, 3);
                    } else {
                        $data["raw"][$key] = $this->InterpSensorsGetData($data["Data"], &$index, 3);
                    }
                }
                
            }
        }

        /**
         * Constructor
         *
         * @param object $driver Object of class driver.
        */    
        function e00392800 (&$driver) {
            parent::__construct($driver);
        }



    }

}

// Protect us in case this is included differently
if (method_exists($this, 'add_generic')) {
    $this->add_generic(array("Name" => "e00392800", "Type" => "driver", "Class" => "e00392800", "deviceJOIN" => "e00391200_location"));
}

?>
