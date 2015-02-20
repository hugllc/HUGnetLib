<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class E10460200 extends \HUGnet\devices\Driver
    implements \HUGnet\devices\drivers\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "totalSensors" => 0,
        "physicalSensors" => 0,
        "virtualSensors" => 0,
        "historyTable" => "E10460200History",
        "averageTable" => "E10460200Average",
        "loadable" => true,
        "packetTimeout" => 2,
        "type" => "endpoint",
        "job"  => "sense",
        "arch" => "1046-02",
        "InputTables" => 40,
        "OutputTables" => 0,
        "DataChannels" => 2,
        "ProcessTables" => 0,
        "PowerTables" => 7,
        "inputSize" => 2,
        "AddressSize" => 3,
        "fixed" => true,
        "Role" => "Type3",
    );
    /**
    * Decodes the sensor string
    *
    * @param string $string The string of sensor data
    *
    * @return null
    */
    public function decodeSensorString($string)
    {
        $ret = array(
            "DataIndex" => hexdec(substr($string, 0, 2)),
            "String" => substr($string, 2),
        );
        return $ret;
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
    
        if (strlen($string) < 6) {
            return;
        }
        // This is the temperature constants for the internal temperature.
        $temp[0] = array(
            "temp" => hexdec(substr($string, 0, 2)),
            "set"  => hexdec(substr($string, 2, 2)) + (hexdec(substr($string, 4, 2)) << 8)
        );
        if (strlen($string) >= 12) {
            $temp[1] = array(
                "temp" => hexdec(substr($string, 6, 2)),
                "set"  => hexdec(substr($string, 8, 2)) + (hexdec(substr($string, 10, 2)) << 8)
            );
        }

        $index = 0;
        for ($i = 0; $i < $this->get("InputTables"); $i++) {
            if (!isset($temp[$index])) {
                break;
            }
            $input = $this->device()->input($i);
            if ($input->get("type") == "XMegaTemp") {
                $input->setExtra(0, $temp[$index]["temp"]);
                $input->setExtra(1, $temp[$index]["set"]);
                $input->store();
                $index++;
            }
            // Done all that we have.
            if ($index >= count($temp)) {
                break;
            }
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
        return "";
    }
}


?>
