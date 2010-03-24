<?php
/**
 * This is the driver code for the 0039-28 endpoints.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
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
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/eDEFAULT.php';
// This is the interface we are implementing
require_once HUGNET_INCLUDE_PATH."/interfaces/endpointdriver.php";

/**
* Driver for the polling script (0039-26-01-P)
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Endpoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2010 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class e00392601 extends eDEFAULT implements endpointDriverInterface
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "e00392601",
        "Type" => "driver",
        "Class" => "e00392601"
    );
    /** Hardware name */
    protected $HWName = "0039-26 Gateway";

    /** Average Table */
    protected $average_table = "e00392601_average";
    /** History Table */
    protected $history_table = "e00392601_history";

    /** Devices */
    public $devices = array(
        "DEFAULT" => array(
            "0039-26-01-P" => "DEFAULT",
            "0039-26-02-P" => "DEFAULT",
            "0039-26-03-P" => "DEFAULT",
            "0039-26-04-P" => "DEFAULT",
            "0039-26-05-P" => "DEFAULT",
            "0039-26-06-P" => "DEFAULT",
            "0039-26-07-P" => "DEFAULT",
        ),
    );

    /** Config array */
    public $config = array(
        "DEFAULT" => array("Function" => "Gateway", "Sensors" => 0),
    );


    /**
        * Returns the packet to send to read the configuration out of an endpoint
        *
        * This should only be defined in a driver that inherits this class if the
        * packet differs
        *
        * @param array $Info Infomation about the device to use
        *
        * @return array
        */
    public function readConfig($Info)
    {
        return array(
            array(
                "To" => $Info["DeviceID"],
                "Command" => PACKET_COMMAND_GETSETUP,
            ),
        );
    }

    /**
        * Gets the job number for this device
        *
        * @param array $Info Infomation about the device to use
        *
        * @return null
        */
    function getJob($Info)
    {
        $stuff = explode("-", $Info["HWPartNum"]);
        return (int) $stuff[2];
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
        * Create the config string
        *
        * @param array $Info The devInfo array
        *
        * @return string
        */
    function getConfigStr($Info)
    {
        $string  = devInfo::hexify($Info["SerialNum"], 10);
        $string .= devInfo::hexifyPartNum($Info["HWPartNum"]);
        $string .= devInfo::hexifyPartNum($Info["FWPartNum"]);
        $string .= devInfo::hexifyVersion($Info["FWVersion"]);
        $string .= "FFFFFF";
        $string .= "00";

        $string .= devInfo::hexify($Info['Job'], 2);
        $string .= devInfo::hexify($Info['GatewayKey'], 4);
        $string .= devInfo::hexifyStr($Info["Name"], 60);

        $myIP = explode(".", $Info["IP"]);

        for ($i = 0; $i < 4; $i++) {
            $string .= devInfo::hexify($myIP[$i], 2);
        }

        for ($i = 1; $i < 7; $i++) {
            $string .= devInfo::hexify($Info["Priorities"][$i], 2);
        }
        return $string;

    }
    /**
        * This takes the numeric job and replaces it with a name
        *
        * @param int $job The job
        *
        * @return string
        */
    function getFunction($job)
    {
        $jobs = array(
            1 => "Poll",
            2 => "Updatedb",
            3 => "Analysis",
            4 => "Endpoint",
            5 => "Control",
            6 => "Config",
        );

        if (!empty($jobs[$job])) return $jobs[$job];
        return $job;
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

        $Info['HWName']       = "0039-26 Gateway";
        $Info["NumSensors"]   = 0;
        $Info["Function"]     = "Gateway ";
        $Info["Timeconstant"] = 0;
        $Info['DriverInfo']   = substr($Info["RawSetup"], E00391102B_TC);

        $Info["TotalSensors"] = (int)$Info["NumSensors"];

        $start = 46;
        $Info["Types"]    = array();
        $Info["Labels"]   = array();
        $Info["Units"]    = array();
        $Info["unitType"] = array();
        $Info["dType"]    = array();
        $Info["doTotal"]  = array();
        $Info["params"]   = array();

        $Info["isGateway"] = true;

        $index = 0;
        // This byte is currently not used
        $Info["Job"] = hexdec(substr($Info["DriverInfo"], $index, 2));
        if (empty($Info["Job"])) $Info["Job"] = self::getJob($Info);
        $Info["Function"] .= self::getFunction($Info["Job"]);

        $index             += 2;
        $Info["CurrentGatewayKey"] = hexdec(substr($Info["DriverInfo"], $index, 4));

        $index       += 4;
        $Info["Name"] = devInfo::deHexify(trim(strtoupper(substr($Info["DriverInfo"], $index, 60))));
        $Info["Name"] = trim($Info["Name"]);
        $index += 60;
        $IP     = str_split(substr($Info["DriverInfo"], $index, 8), 2);
        $index += 8;

        foreach ($IP as $k => $v) {
            $IP[$k] = hexdec($v);
        }
        $Info['IP'] = implode(".", $IP);

        $Info["Priorities"] = array();
        for ($i = 1; $i < 7; $i++) {
            $Info["Priorities"][$i] = hexdec(substr($Info["DriverInfo"], $index, 2));
            $index += 2;
        }

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

?>
