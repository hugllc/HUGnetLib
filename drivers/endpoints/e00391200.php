<?php
/**
 *   Driver for the 0039-12 endpoints with the temperature daughter board
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

// Check to see if this class already exists
if (!class_exists("e00391200")) {

/** The location of the time constant in the setup string */
define("e00391102B_TC", ENDPOINT_CONFIGEND);    
/** The location of the types in the setup string */
define("e00391102B_TYPES", ENDPOINT_CONFIGEND+4);
/** The number of sensors for this device */
define("e00391102B_SENSORS", 9);

    /**
     * Driver for the 0039-12 endpoint board and select firmwares
     */
    class e00391200 extends eDEFAULT{

        /** @var string The name of this hardware */
        var $HWName = "0039-12 Endpoint";
        /** @var string The average table we use */
        var $average_table = "e00391200_average";
        /** @var string The history table we use */
        var $history_table = "e00391200_history";

        /** 
         *  The array of devices and versions that this driver handles.
         *  @var array
         */
        var $devices = array(
        
            "0039-11-02-B" => array(
                "0039-12-00-A" => "DEFAULT",
                "0039-12-01-A" => "DEFAULT",
                "0039-12-02-A" => "DEFAULT",
                "0039-12-01-B" => "DEFAULT",
                "0039-12-02-B" => "DEFAULT",
            ),
            "0039-11-03-B" => array(
                "0039-12-00-A" => "DEFAULT",
                "0039-12-01-A" => "DEFAULT",
                "0039-12-02-A" => "DEFAULT",
                "0039-12-01-B" => "DEFAULT",
                "0039-12-02-B" => "DEFAULT",
            ),

            "0039-20-02-C" => array(
                "0039-12-02-A" => "DEFAULT",
                "0039-12-02-B" => "DEFAULT",
            ),
            "0039-20-03-C" => array(
                "0039-12-02-A" => "DEFAULT",
                "0039-12-02-B" => "DEFAULT",
            ),
            "0039-20-07-C" => array(
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

        /** @var array What the sensors are called if we have no other names for them */
        var $deflocation = array("Sensor 1", "Sensor 2", "Sensor 3", "Sensor 4", "Sensor 5", "Sensor 6", "Sensor 7", "Sensor 8", "Sensor 9");

        /** 
         *  Some configurations for the endpoints based on the firmware they are running
         *  @var array
         */
        var $config = array(
            "0039-11-02-B" => array("Function" => "Temperature/Moisture Sensor", "Sensors" => 9),        
            "0039-11-03-B" => array("Function" => "Temperature Sensor", "Sensors" => 9),        
            "0039-20-02-C" => array("Function" => "Moisture Sensor", "Sensors" => 9, "DisplayOrder" => "0,4,1,5,2,6,3,7,8"),
            "0039-20-03-C" => array("Function" => "Temperature Sensor", "Sensors" => 9,),        
            "0039-20-07-C" => array("Function" => "Capactive Sensor", "Sensors" => 9),        
            "DEFAULT" => array("Function" => "Unknown", "Sensors" => 9),        
        );

    
        /**
         * Extra columns to display for these endpoints
         * @var array
        */
        var $cols = array("TimeConstant" => "Time Constant", 
                                "ActiveSensors" => "Active Sensors",
                                "NumSensors" => "# Sensors",
                                );


        
        function CheckRecord($Info, &$Rec) {
        
            if (isset($Rec['Status'])) {
                $Rec['StatusOld'] = $Rec['Status'];
            }
    
            if (empty($Rec['RawData'])) {
                $Rec["Status"] = 'BAD';
                return;
            }
            $Rec['Status'] = "GOOD";            
            
            $Bad = 0;
    
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
    
        
        function InterpConfig(&$Info) {
            //$Info["Location"] = $this->deflocation;
            $Info['HWName'] = $this->HWName;

            If (isset($this->config[$Info["FWPartNum"]])) {
                $Info["NumSensors"] = $this->config[$Info["FWPartNum"]]["Sensors"];    
                $Info["Function"] = $this->config[$Info["FWPartNum"]]["Function"];
            } else {
                $Info["NumSensors"] = $this->config["DEFAULT"]["Sensors"];    
                $Info["Function"] = $this->config["DEFAULT"]["Function"];
            }
            $Info['params'] = device::decodeParams($Info['params']);
            $Info["Types"] = array();
            $this->InterpConfig00392012C($Info);
            $this->InterpConfigTC($Info);
            $this->InterpConfigSensors($Info);
        }
        private function InterpConfig00392012C(&$Info) {
            if ($Info["FWPartNum"] == "0039-20-12-C") {
                $Info["Types"] = array(0 => 0x70, 1 => 0x70, 2 => 0x71, 3 => 0x72);
            }
        }
        private function InterpConfigTC(&$Info) {
            if ($Info["NumSensors"] > 0) {
                $Info["TimeConstant"] = hexdec(substr($Info["DriverInfo"], 0, 2));
                if ($Info["TimeConstant"] == 0) $Info["TimeConstant"] = hexdec(substr($Info["RawSetup"], e00391102B_TC, 4));
            } else {
                $Info["TimeConstant"] = 0;
            }
        
        }
        private function InterpConfigSensors(&$Info) {
            $Info["Labels"] = array();
            $Info["Units"] = array();

            for ($i = 0; $i < $this->config[$Info["FWPartNum"]]["Sensors"]; $i++) {
                
                $key = $this->getOrder($Info, $i);
                
                if (!isset($Info['Types'][$i])) {
                    $Info["Types"][$i] = hexdec(substr($Info["DriverInfo"], ($key*2)+2, 2));
                }
//                $Info["Labels"][$i] = $this->labels[$Info["Types"][$i]];
                $Info["unitType"][$i] = $this->driver->sensors->getUnitType($Info["Types"][$i], $Info['params']['sensorType'][$i]);
                $Info["Labels"][$i] = $Info['unitType'][$i]; //$this->driver->sensors->getUnitType($Info["Types"][$i], $Info['params']['sensorType'][$i]);
                $Info["Units"][$i] = $this->driver->sensors->getUnits($Info["Types"][$i], $Info['params']['sensorType'][$i]);    
                $Info["dType"][$i] = $this->driver->sensors->getUnitDefMode($Info["Types"][$i], $Info['params']['sensorType'][$i], $Info["Units"][$i]);    
                $Info["doTotal"][$i] = $this->driver->sensors->doTotal($Info["Types"][$i], $Info['params']['sensorType'][$i]);

            }
        }

        private function InterpSensorsSetData(&$Info, &$data) {
            $data['NumSensors'] = $Info['NumSensors'];
            $data["ActiveSensors"] = $Info["ActiveSensors"];
            $data["Driver"] = $Info["Driver"];
            $data["DeviceKey"] = $Info["DeviceKey"];
            $data["Types"] = $Info["Types"];
            $data["DataIndex"] = $data["Data"][0];
            $oldtc = $data["Data"][1];  // There is nothing here.
            $data["TimeConstant"] = $data["Data"][2];
            if ($data["TimeConstant"] == 0) $data["TimeConstant"] = $oldtc;

        }
        private function InterpSensorsGetRaw(&$Info, &$data) {
            if (is_array($data["Data"])) {
                $index = 3;
                for ($i = 0; $i < $data["NumSensors"]; $i++) {
                    $key = $this->getOrder($Info, $i, TRUE);
                    switch ($data["Types"][$key]) {
                        case 1:
                            $data["raw"][$key] = $data["Data"][$index++];
                            $data["raw"][$key] += $data["Data"][$index++] << 8;
                            break;                                            
                        case 0x72:
                            $d = $data["Data"][$index++];
                            $d = $d ^ 0xF0;  // invert the top half of the value.
                            $data["raw"][$key] = $d;
                            break;                                            
                        case 0x2:
                        case 0x0:
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
//        function DecodeData($data) {
        function InterpSensors($Info, $Packets) {
            $this->InterpConfig($Info);
            $ret = array();
            foreach($Packets as $key => $data) {
                $this->checkDataArray($data);
                if(isset($data['RawData'])) {
                    $index = 0;
                    self::InterpSensorsSetData($Info, $data);
                    self::InterpSensorsGetRaw($Info, $data);
                    $this->driver->sensors->decodeData($Info, $data);
                    $this->checkRecord($Info, $data);
                    $ret[] = $data;
                }
            }
        
            return($ret);
        }
    


        /**
         * Constructor
            @param $db String The database to use
            @param $servers Array The servers to use.
            @param $options the database options to use.
        */    
        function e00391200 (&$driver) {
//            $this->eDEFAULT($servers, $db, $options);
            parent::eDEFAULT($driver);
        }



    }

}
// Register this plugin    
if (method_exists($this, 'add_generic')) {
    $this->add_generic(array("Name" => "e00391200", "Type" => "driver", "Class" => "e00391200"));
}

?>
