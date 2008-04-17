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

if (!class_exists("eVIRTUAL")) {


    /**
     * Driver for the polling script (0039-26-01-P)
     *
     * @category   Drivers
     * @package    HUGnetLib
     * @subpackage Endpoints
     * @author     Scott Price <prices@hugllc.com>
     * @copyright  2007 Hunt Utilities Group, LLC
     * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
     * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
     */
    class eVIRTUAL extends eDEFAULT
    {
        /** Hardware name */
        protected $HWName = "Virtual Endpoint";

        /** Devices */
        public $devices = array(
            "DEFAULT" => array(
                "VIRTUAL" => "DEFAULT",
           ),
        );


        /**
         * Returns the packet to send to read the configuration out of an endpoint
         *
         * @param array $Info Infomation about the device to use
         *
         * @return null
         */
        function readConfig($Info)
        {
            return array(
                array(
                    "To" => $Info["DeviceID"],
                    "Command" => PACKET_COMMAND_GETSETUP,
               ),
           );
        }

        /**
         * Checks a data record to determine what its status is.  It changes
         * Rec['Status'] to reflect the status and adds Rec['Statusold'] which
         * is the status that the record had originally.
         *
         * @param array $Info The information array on the device
         * @param array &$Rec The data record to check
         *
         * @return null
         */
        function checkRecord($Info, &$Rec)
        {
            $Rec["StatusOld"] = $Rec["Status"];        
            $Rec["Status"] = "BAD";
        }
        
        /**
         * Interpret the configuration
         *
         * @param array &$Info The devInfo array
         *
         * @return array
         */
        function interpConfig(&$Info) 
        {
            return $Info;
        }

        /**
         * This is just to kill the default behaviour
         *
         * @param array $Info    The devInfo array
         * @param array $Packets The packets to interpret
         *
         * @return array
         */
        function interpSensors($Info, $Packets) 
        {
            return array();
        }


    }

}

// Protect us in case this is included differently
if (method_exists($this, 'addGeneric')) {
    $this->addGeneric(array("Name" => "e00392601", "Type" => "driver", "Class" => "e00392601"));
}

?>
