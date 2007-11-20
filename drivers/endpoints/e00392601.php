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
 ** @version $Id: e00392800.php 445 2007-11-13 16:53:06Z prices $    
 *
 */

if (!class_exists("e00392601")) {


    /**
     * Driver for the 0039-12 endpoint board and select firmwares
    */
    class e00392601 extends eDEFAULT{
        static $PacketSN = 0;
        static $PacketHWPartNum = 20;
        static $PacketFWPartNum = 30;
        static $PacketFWVersion = 40;
        static $PacketGroup = 46;
        static $PacketPriority = 52;
        static $PacketJobs = 54;
        static $PacketGatewayKey = 56;
        static $PacketName = 60;
        static $PacketIP = 120;
        
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
        function ReadConfig($Info) {
            $packet = array(
                array(
                    "To" => $Info["DeviceID"],
                    "Command" => PACKET_COMMAND_GETSETUP,
                ),
            );
            $return = $this->packet->SendPacket($Info, $packet);

            return($return);
        }

        /**
         *
         */
        function CheckRecord($Info, &$Rec) {
        
            $Rec["Status"] = "BAD";
        }

        function getConfigStr($Info) {
            $string = EPacket::hexify($Info["SerialNum"], 10);
    
            $string .= str_replace("-", "", $Info["HWPartNum"]);
            $string .= str_replace("-", "", $Info["FWPartNum"]);
            $ver = explode(".", POLL_VERSION);
            for($i = 0; $i < 3; $i++) $string .= EPacket::hexify($ver[$i], 2);
            $string .= "FFFFFF";
    
            $string .= EPacket::hexify($Info["Priority"], 2);
            $Jobs = 0;
            if ($Info["doPoll"]) $Jobs |= 0x01;
            if ($Info["doConfig"]) $Jobs |= 0x02;
            if ($Info["doCCheck"]) $Jobs |= 0x04;
            if ($Info["doUnsolicited"]) $Jobs |= 0x08;
            $string .= EPacket::hexify($Jobs, 2);
            $string .= EPacket::hexify($Info['GatewayKey'], 4);
    
            $string .= EPacket::hexifyStr($Info["Name"], 60);
     
            $myIP = explode(".", $Info["IP"]);
    
            for($i = 0; $i < 4; $i++) {
                $string .= EPacket::hexify($myIP[$i], 2);
            }
print $string;
            return $string;

        }    
        
        /**
         *
         */
        function InterpConfig(&$Info) {
            //$Info["Location"] = $this->deflocation;

            $Info['HWName'] = $this->HWName;

            $Info["NumSensors"] = $this->config["DEFAULT"]["Sensors"];    
            $Info["Function"] = $this->config["DEFAULT"]["Function"];
            $Info["Timeconstant"] = 0;

            $Info['DriverInfo'] = substr($Info["RawSetup"], e00391102B_TC);

            $start = 46;
            $Info["Types"] = array();
            $Info["Labels"] = array();
            $Info["Units"] = array();
            $Info["unitType"] = array();
            $Info["dType"] = array();
            $Info["doTotal"] = array();
            $Info["params"] = array();
            
            $Info["isGateway"] = TRUE;
    
            $index = 0;
            $Info["Priority"] = $Info['BoredomThreshold'];
            $Jobs = hexdec(substr($Info["DriverInfo"], $index, 2));
            $Info['doPoll'] = (bool) ($Jobs & 0x01);
            $Info['doConfig'] = (bool) ($Jobs & 0x02);
            $Info['doCCheck'] = (bool) ($Jobs & 0x04);
            $Info['doUnsolicited'] = (bool) ($Jobs & 0x08);
            $index += 2;
            $Info["GatewayKey"] = 	hexdec(substr($Info["DriverInfo"], $index, 4));
            $index += 4;
            $Info["Name"] = 	$this->packet->deHexify(trim(strtoupper(substr($Info["DriverInfo"], $index, 60))));
            $index += 60;
			$IP = str_split(substr($Info["DriverInfo"], $index, 8), 2);
            foreach($IP as $k => $v) {
                $IP[$k] = hexdec($v); 
            }
            $Info['IP'] = implode(".", $IP);
            
            return($Info);
        }

        /**
         *
         */
        function InterpSensors($Info, $Packets) {
            return array();
        }

        /**
         * Constructor
         *
         * @param object $driver Object of class driver.
        */    
        function e00392601 (&$driver) {
            parent::eDEFAULT($driver);
        }



    }

}

// Protect us in case this is included differently
if (method_exists($this, 'add_generic')) {
    $this->add_generic(array("Name" => "e00392601", "Type" => "driver", "Class" => "e00392601"));
}

?>
