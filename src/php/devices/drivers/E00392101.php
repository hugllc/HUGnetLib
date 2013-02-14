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
class E00392101 extends \HUGnet\devices\Driver
{
    /** The placeholder for the reading the downstream units from a controller */
    const COMMAND_READDOWNSTREAM = "56";
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "totalSensors" => 10,
        "physicalSensors" => 8,
        "virtualSensors" => 2,
        "historyTable" => "E00392100History",
        "averageTable" => "E00392100Average",
        "loadable" => true,
        "packetTimeout" => 2,
        'ConfigInterval' => 600,
        "type" => "controller",
        "job"  => "control",
        "arch" => "AVR",
        "InputTables" => 10,
        "OutputTables" => 0,
        "ProcessTables" => 0,
    );

    /**
    * This function returns the configuration packet arrays
    *
    * @return array
    */
    public function config()
    {
        return array(
            array(
                "Command" => "CONFIG",
            ),
            array(
                "Command" => self::COMMAND_READDOWNSTREAM,
                "Data" => "00",
            ),
            array(
                "Command" => self::COMMAND_READDOWNSTREAM,
                "Data" => "01",
            ),
        );
    }
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
            "timeConstant" => 1,
            "String" => substr($string, 2),
        );
        return $ret;
    }
    /**
    * This creates the sensor drivers
    *
    * Here is the actual sensor array (actual view):
    *    ADC 0: HUGnet2 Current
    *    ADC 1: HUGnet2 Temp
    *    ADC 2: HUGnet2 Voltage Low
    *    ADC 3: HUGnet2 Voltage High
    *    ADC 4: HUGnet1 Voltage High
    *    ADC 5: HUGnet1 Voltage Low
    *    ADC 6: HUGnet1 Temp
    *    ADC 7: HUGnet1 Current
    *
    * Here is the actual sensor array (world view):
    *    Input 0: HUGnet1 Voltage High
    *    Input 1: HUGnet1 Voltage Low
    *    Input 2: HUGnet1 Current
    *    Input 3: HUGnet1 Temp
    *    Input 4: HUGnet2 Voltage High
    *    Input 5: HUGnet2 Voltage Low
    *    Input 6: HUGnet2 Current
    *    Input 7: HUGnet2 Temp
    *
    * @param int $sid The sensor id to get.  They are labaled 0 to sensors
    *
    * @return null
    */
    public function &input($sid)
    {
        $sid = (int)$sid;
        include_once dirname(__FILE__)."/../Input.php";
        $data = array(
            "input" => $sid,
            "dev" => $this->device()->id(),
        );
        $table = array(
            "id" => 0,
            "ADLAR" => 1,
            "REFS" => 1,
        );
        $type = array(
            "id" => 0xF8,
            "type" => "AVRAnalogTable",
        );
        switch($sid) {
        case 0:
            // HUGnet1 Voltage High
            $table["driver"] = "40:FETBoardVoltage";
            $table["name"] = "Controller Board Voltage";
            $table["MUX"] = 4;
            $data["extra"] = array(180, 27, 5.0);
            $data["location"] = "HUGnet 1 Voltage High";
            break;
        case 1:
            // HUGnet1 Voltage Low
            $table["driver"] = "40:FETBoardVoltage";
            $table["name"] = "Controller Board Voltage";
            $table["MUX"] = 5;
            $data["extra"] = array(180, 27, 5.0);
            $data["location"] = "HUGnet 1 Voltage Low";
            break;
        case 2:
            // HUGnet1 Current
            $table["driver"] = "50:FETBoardCurrent";
            $table["name"] = "Controller Board Current";
            $table["MUX"] = 7;
            $data["extra"] = array(0.5, 7, 5.0);
            $data["location"] = "HUGnet 1 Current";
            break;
        case 3:
            // HUGnet1 Temperature
            $table["driver"] = "02:AVRBC2322640";
            $table["name"] = "Controller Board Temperature";
            $table["MUX"] = 6;
            $data["extra"] = array(100, 10);
            $data["location"] = "HUGnet 1 Temperature";
            break;
        case 4:
            // HUGnet2 Voltage High
            $table["driver"] = "40:FETBoardVoltage";
            $table["name"] = "Controller Board Voltage";
            $table["MUX"] = 3;
            $data["extra"] = array(180, 27, 5.0);
            $data["location"] = "HUGnet 2 Voltage High";
            break;
        case 5:
            // HUGnet2 Voltage Low
            $table["driver"] = "40:FETBoardVoltage";
            $table["name"] = "Controller Board Voltage";
            $table["MUX"] = 2;
            $data["extra"] = array(180, 27, 5.0);
            $data["location"] = "HUGnet 2 Voltage Low";
            break;
        case 6:
            // HUGnet2 Current
            $table["driver"] = "50:FETBoardCurrent";
            $table["name"] = "Controller Board Current";
            $table["MUX"] = 0;
            $data["extra"] = array(0.5, 7, 5.0);
            $data["location"] = "HUGnet 2 Current";
            break;
        case 7:
            // HUGnet2 Temperature
            $table["driver"] = "02:AVRBC2322640";
            $table["name"] = "Controller Board Temperature";
            $table["MUX"] = 1;
            $data["extra"] = array(100, 10);
            $data["location"] = "HUGnet 2 Temperature";
            break;
        default:
            $type = array(
                "id" => 0xFE,
            );
        }
        $system = $this->device()->system();
        $device = $this->device();
        $obj = \HUGnet\devices\Input::factory(
            $system, $data, null, $device, $table
        );
        if (is_null($obj->get("id"))
            || ((int)$obj->get("id") !== $type["id"])
        ) {
            $obj->load(array_merge((array)$data, (array)$type));
            //$obj->store();
        }
        return $obj;
    }


}


?>
