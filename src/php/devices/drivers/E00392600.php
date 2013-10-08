<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our interface */
require_once dirname(__FILE__)."/DriverInterface.php";


/**
 * Networking for devices.
 *
 * This class will do all of the networking for devices.  It will poll, get configs,
 * update software, and anything else related to talking to devices.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class E00392600 extends \HUGnet\devices\Driver
    implements \HUGnet\devices\drivers\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "physicalSensors" => 0,
        "virtualSensors" => 0,
        "totalSensors" => 0,
        "ConfigInterval" => 600,
        "type" => "script",
        "packetTimeout" => 2,
        "arch" => "PC",
        "InputTables" => 0,
        "OutputTables" => 0,
        "ProcessTables" => 0,
        "DataChannels" => 0,
    );
    /**
    * Checks a record to see if it needs fixing
    *
    * @return array
    */
    public function checkRecord()
    {
        $lastContact = $this->device()->getParam("LastContact");
        $fails       = $this->device()->getParam("ContactFail");

        if (($fails > 20) && ((time() - $lastContact) > 3600)) {
            $this->device()->system()->out(
                "Old script device ".sprintf("%06X", $this->device()->get("id"))
                ." deleted from the database",
                1
            );
            $this->device()->delete();
            $this->device()->load(null);
        } else {
            $this->_updateJob();
        }
    }
    /**
    * Encodes this driver as a setup string
    *
    * @param bool $showFixed Show the fixed portion of the data
    *
    * @return array
    */
    public function encode($showFixed = true)
    {
        $return = "";
        $string = strtoupper(
            str_replace("-", "", (string)$this->device()->system()->get("uuid"))
        );
        $return .= str_pad($string, 32, "F");

        $IPA = explode(".", (string)$this->device()->get("DeviceLocation"));
        $return .= sprintf(
            "%02X%02X%02X%02X",
            (int)$IPA[0] & 0xFF,
            (int)$IPA[1] & 0xFF,
            (int)$IPA[2] & 0xFF,
            (int)$IPA[3] & 0xFF
        );
        $return .= sprintf("%04X", $this->device()->get("GatewayKey"));
        $return .= sprintf("%02X", $this->device()->getParam("Enable"));
        return $return;
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string The string to decode
    *
    * @return array
    */
    public function decode($string)
    {
        $index = 0;
        $uuid = strtolower(substr((string)$string, $index, 32));
        $this->device()->set(
            "DeviceName",
            substr($uuid, 0, 8)."-".substr($uuid, 8, 4)."-".substr($uuid, 12, 4)
            ."-".substr($uuid, 16, 4)."-".substr($uuid, 20)
        );
        $index += 32;
        $IPA = str_split(substr((string)$string, $index, 8), 2);
        $this->device()->set(
            "DeviceLocation",
            sprintf(
                "%d.%d.%d.%d",
                hexdec($IPA[0]) & 0xFF,
                hexdec($IPA[1]) & 0xFF,
                hexdec($IPA[2]) & 0xFF,
                hexdec($IPA[3]) & 0xFF
            )
        );
        $index += 8;
        $this->device()->set(
            "GatewayKey", hexdec(substr((string)$string, $index, 4))
        );
        $index += 4;
        $this->device()->setParam(
            "Enable", hexdec(substr((string)$string, $index, 2))
        );
        $index += 2;
        $this->_updateJob();
    }
    /**
    * Sets the Job
    *
    * @return array
    */
    private function _updateJob()
    {
        switch ($this->device()->get("HWPartNum")) {
        case "0039-26-02-P":
            $this->device()->set("DeviceJob", "Updater");
            break;
        case "0039-26-03-P":
            $this->device()->set("DeviceJob", "Analysis");
            break;
        case "0039-26-04-P":
            $this->device()->set("DeviceJob", "Router");
            break;
        case "0039-26-06-P":
            $this->device()->set("DeviceJob", "Gatherer");
            break;
        default:
            $this->device()->set("DeviceJob", "Unknown");
            break;
        }
    }

}


?>
