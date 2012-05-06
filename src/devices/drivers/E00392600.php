<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class E00392600 extends \HUGnet\devices\Driver
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
    );
    /**
    * This function creates the system.
    *
    * @return null
    */
    public static function &factory()
    {
        return parent::intFactory();
    }
    /**
    * Checks a record to see if it needs fixing
    *
    * @param object &$device The device object
    *
    * @return array
    */
    public function checkRecord(&$device)
    {
        $lastContact = $device->getParam("LastContact");
        $fails       = $device->getParam("ContactFail");
        if (($fails > 20) && ((time() - $lastContact) > 3600)) {
            \HUGnet\VPrint::out(
                "Old script device ".sprintf("%06X", $device->get("id"))
                ." deleted from the database",
                1
            );
            $device->delete();
            $device->load(null);
        }
    }
    /**
    * Encodes this driver as a setup string
    *
    * @param object &$device   The device object
    * @param bool   $showFixed Show the fixed portion of the data
    *
    * @return array
    */
    public function encode(&$device, $showFixed = true)
    {
        $string  = strtoupper(
            str_replace("-", "", (string)$device->system()->get("uuid"))
        );
        $string = str_pad($string, 32, "F");
        $IP = explode(".", (string)$device->get("DeviceLocation"));
        $string .= sprintf(
            "%02X%02X%02X%02X",
            (int)$IP[0] & 0xFF,
            (int)$IP[1] & 0xFF,
            (int)$IP[2] & 0xFF,
            (int)$IP[3] & 0xFF
        );
        return $string;
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string  The string to decode
    * @param object &$device The device object
    *
    * @return array
    */
    public function decode($string, &$device)
    {
        $uuid = strtolower(substr((string)$string, 0, 32));
        $device->set(
            "DeviceName",
            substr($uuid, 0, 8)."-".substr($uuid, 8, 4)."-".substr($uuid, 12, 4)
            ."-".substr($uuid, 16, 4)."-".substr($uuid, 20)
        );
        $IP = str_split(substr((string)$string, 32), 2);
        $device->set(
            "DeviceLocation",
            sprintf(
                "%d.%d.%d.%d",
                hexdec($IP[0]) & 0xFF,
                hexdec($IP[1]) & 0xFF,
                hexdec($IP[2]) & 0xFF,
                hexdec($IP[3]) & 0xFF
            )
        );
    }

}


?>
