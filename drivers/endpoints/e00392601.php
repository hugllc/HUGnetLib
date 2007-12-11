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
 * @version    SVN: $Id: e00392100.php 528 2007-12-10 23:07:27Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

if (!class_exists("e00392601")) {


    /**
     * Driver for the polling script (0039-26-01-P)
     */
    class e00392601 extends eDEFAULT{
        var $HWName = "0039-26 Gateway";

        var $average_table = "e00392601_average";
        var $history_table = "e00392601_history";

        var $devices = array(
            "DEFAULT" => array(
                "0039-26-01-P" => "DEFAULT",
            ),
        );

        var $config = array(
            "DEFAULT" => array("Function" => "Gateway", "Sensors" => 0),        
        );


        /**
         * Returns the packet to send to read the configuration out of an endpoint
         * @param array $Info Infomation about the device to use
         * @note This should only be defined in a driver that inherits this class if the packet differs
         */
        function readConfig($Info) {
            return array(
                array(
                    "To" => $Info["DeviceID"],
                    "Command" => PACKET_COMMAND_GETSETUP,
                ),
            );
        }

        /**
         *
          */
        function checkRecord($Info, &$Rec) {
        
            $Rec["Status"] = "BAD";
        }

        function getConfigStr($Info) {
            $string = devInfo::hexify($Info["SerialNum"], 10);
    
            $string .= devInfo::hexifyPartNum($Info["HWPartNum"]);
            $string .= devInfo::hexifyPartNum($Info["FWPartNum"]);
            $string .= devInfo::hexifyVersion($Info["FWVersion"]);
            $string .= "FFFFFF";
    
            $string .= devInfo::hexify($Info["Priority"], 2);
            $Jobs = 0;
            if ($Info["doPoll"]) $Jobs |= 0x01;
            if ($Info["doConfig"]) $Jobs |= 0x02;
            if ($Info["doCCheck"]) $Jobs |= 0x04;
            if ($Info["doUnsolicited"]) $Jobs |= 0x08;
            $string .= devInfo::hexify($Jobs, 2);
            $string .= devInfo::hexify($Info['GatewayKey'], 4);
    
            $string .= devInfo::hexifyStr($Info["Name"], 60);
     
            $myIP = explode(".", $Info["IP"]);
    
            for ($i = 0; $i < 4; $i++) {
                $string .= devInfo::hexify($myIP[$i], 2);
            }
            return $string;

        }    
        
        /**
         *
          */
        function interpConfig(&$Info) {

            $Info['HWName'] = $this->HWName;

            $Info["NumSensors"] = $this->config["DEFAULT"]["Sensors"];    
            $Info["Function"] = $this->config["DEFAULT"]["Function"];
            $Info["Timeconstant"] = 0;

            $Info['DriverInfo'] = substr($Info["RawSetup"], E00391102B_TC);

            $start = 46;
            $Info["Types"] = array();
            $Info["Labels"] = array();
            $Info["Units"] = array();
            $Info["unitType"] = array();
            $Info["dType"] = array();
            $Info["doTotal"] = array();
            $Info["params"] = array();
            
            $Info["isGateway"] = true;
    
            $index = 0;
            $Info["Priority"] = $Info['BoredomThreshold'];
            $Jobs = hexdec(substr($Info["DriverInfo"], $index, 2));
            $Info['doPoll'] = (bool) ($Jobs & 0x01);
            $Info['doConfig'] = (bool) ($Jobs & 0x02);
            $Info['doCCheck'] = (bool) ($Jobs & 0x04);
            $Info['doUnsolicited'] = (bool) ($Jobs & 0x08);
            $index += 2;
            $Info["GatewayKey"] = hexdec(substr($Info["DriverInfo"], $index, 4));
            $index += 4;
            $Info["Name"] = devInfo::deHexify(trim(strtoupper(substr($Info["DriverInfo"], $index, 60))));
            $index += 60;
            $IP = str_split(substr($Info["DriverInfo"], $index, 8), 2);
            foreach ($IP as $k => $v) {
                $IP[$k] = hexdec($v); 
            }
            $Info['IP'] = implode(".", $IP);
            
            return($Info);
        }

        /**
         *
          */
        function interpSensors($Info, $Packets) {
            return array();
        }

        /**
         * Constructor
         *
         * @param object $driver Object of class driver.
        */    
        function e00392601 (&$driver) {
            parent::__construct($driver);
        }



    }

}

// Protect us in case this is included differently
if (method_exists($this, 'add_generic')) {
    $this->add_generic(array("Name" => "e00392601", "Type" => "driver", "Class" => "e00392601"));
}

?>
