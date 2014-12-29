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
class E00392100 extends \HUGnet\devices\Driver
    implements \HUGnet\devices\drivers\DriverInterface
{
    /** The placeholder for the reading the downstream units from a controller */
    const COMMAND_READDOWNSTREAM = "56";
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "totalSensors" => 10,
        "physicalSensors" => 6,
        "virtualSensors" => 4,
        "historyTable" => "E00392100History",
        "averageTable" => "E00392100Average",
        "loadable" => true,
        "packetTimeout" => 2,
        'ConfigInterval' => 600,
        "type" => "controller",
        "job"  => "control",
        "arch" => "old",
        "DataChannels" => 10,
        "fixed" => true,
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
    * @param int   $sid  The sensor id to get.  They are labeled 0 to sensors
    * @param array $data The data to use for a role
    *
    * @return null
    */
    public function &input($sid, $data = null)
    {
        $sid = (int)$sid;
        include_once dirname(__FILE__)."/../Input.php";
        $data = array(
            "input" => $sid,
            "dev" => $this->device()->id(),
        );
        if (($sid == 0) || ($sid === 3)) {
            $type = array(
                "id" => 0x40,
                "type" => "ControllerVoltage",
            );
        } else if (($sid == 1) || ($sid === 4)) {
            $type = array(
                "id" => 0x50,
                "type" => "ControllerCurrent",
            );
        } else if (($sid == 2) || ($sid === 5)) {
            $type = array(
                "id" => 0x02,
                "type" => "ControllerTemp",
            );
        } else {
            $type = array(
                "id" => 0xFE,
            );
        }
        $system = $this->device()->system();
        $device = $this->device();
        $obj = \HUGnet\devices\Input::factory($system, $data, null, $device);
        if (is_null($obj->get("id"))
            || ((int)$obj->get("id") !== $type["id"])
            || ((int)$obj->get("type") !== $type["type"])
        ) {
            $obj->load(array_merge((array)$data, (array)$type));
            //$obj->store();
        }
        return $obj;
    }


}


?>
