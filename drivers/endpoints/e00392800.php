<?php
/**
 * This is the driver code for the 0039-28 endpoints.
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
        private $calParts = 2;
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
        function readConfig($Info) {
            $packet = array(
                array(
                    "To" => $Info["DeviceID"],
                    "Command" => PACKET_COMMAND_GETSETUP,
                ),
            );

            for ($i = 0; $i < $this->calParts; $i++) {
                $packet[] = array(
                    "To" => $Info["DeviceID"],
                    "Command" => PACKET_COMMAND_GETCALIBRATION,
                    "Data" => devInfo::hexify($i),
                );
            }
            return $packet;
        }
        /**
         *
          */
        function checkRecord($Info, &$Rec) {
            parent::checkRecordBase($Info, $Rec);    
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
        function interpConfig(&$Info) {
            $this->interpConfigDriverInfo($Info);
            $this->interpConfigHW($Info);
            $this->interpConfigFW($Info);
            $this->interpConfigTC($Info);
            $this->interpConfigParams($Info);
            $this->_interpConfig00392012C($Info);
            $this->InterpTypes($Info);
            $this->interpConfigSensorSetup($Info);
        }
        /**
         *
          */
        private function _interpConfig00392012C(&$Info) {
            if ($Info["FWPartNum"] == "0039-20-12-C") {
                $Info["Types"] = array(0 => 0x70, 1 => 0x70, 2 => 0x71, 3 => 0x72);
            }
        }

        /**
         *
          */
        function interpSensors($Info, $Packets) {
            $this->interpConfig($Info);
            $ret = array();

            unset($lastPacket);
            foreach ($Packets as $key => $data) {
                $data = $this->checkDataArray($data);
                if (isset($data['RawData'])) {
                    self::interpSensorsSetData($Info, $data);
                    $index = 3; 
                    self::_interpSensorsGetRaw($Info, $data);
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
        private function _interpSensorsGetRaw(&$Info, &$data) {
            if (is_array($data["Data"])) {
                $index = 3;
                for ($i = 0; $i < $Info["NumSensors"]; $i++) {
                    $key = $this->getOrder($Info, $i, true);
                    if ($Info["Types"][$key] == 0x6F) {
                        $data["raw"][$key] = $this->interpSensorsGetData($data["Data"], &$index, 1, 3);
                    } else {
                        $data["raw"][$key] = $this->interpSensorsGetData($data["Data"], &$index, 3);
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
