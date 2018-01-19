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
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class E10460301 extends \HUGnet\devices\Driver
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
        "historyTable" => "E10460300History",
        "averageTable" => "E10460300Average",
        "loadable" => true,
        "packetTimeout" => 5,
        "type" => "endpoint",
        "job"  => "sense",
        "arch" => "1046-03",
        "InputTables" => 18,
        "OutputTables" => 2,
        "DataChannels" => 19,
        "ProcessTables" => 4,
        "PowerTables" => 2,
        "inputSize" => 4,
        "AddressSize" => 2,
        "PowerPortDataSize" => 6,
        "PowerPortDataStart" => 0,
        "fixed" => array(
            "InputTables" => true, 
            "OutputTables" => true, 
            "ProcessTables" => true,
            "PowerTables" => false,
        ),
        "Role" => "Coach03",
        "useCRC" => true,
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
    /**
    * Decodes the sensor data
    *
    * @param string $string  The string of sensor data
    * @param string $command The command that was used to get the data
    * @param float  $deltaT  The time difference between this packet and the next
    * @param float  $prev    The previous record
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function decodeData($string, $command="", $deltaT = 0, $prev = null)
    {
        $ret = array();
        $size = $this->params["inputSize"];
        for ($i = 0; $i < $this->params["InputTables"]; $i++) {
            $str = substr($string, $i*8, $size * 2);
            $value = \HUGnet\Util::decodeInt($str, $size, true);
            switch ($i) {
                case 5:
                case 11:
                    break;
                default:
                    $value /= 1000;
                    break;
            }
            $ret[$i] = array(
                "value" => $value
            );
        }
        return $ret;
    }
}


?>
