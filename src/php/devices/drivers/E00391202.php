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
class E00391202 extends \HUGnet\devices\Driver
    implements \HUGnet\devices\drivers\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "totalSensors" => 13,
        "physicalSensors" => 9,
        "virtualSensors" => 4,
        "historyTable" => "E00391201History",
        "averageTable" => "E00391201Average",
        "type" => "endpoint",
        "job"  => "sense and control",
        "arch" => "0039-12",
        "InputTables" => 9,
        "OutputTables" => 4,
        "ProcessTables" => 1,
        "DataChannels" => 13,
        "DigitalInputs" => array(
                1  => "Port 1",
                2  => "Port 2",
                3  => "Port 3",
                4  => "Port 4",
                5  => "Port 5",
                6  => "Port 6",
                7  => "Port 7",
                8  => "Port 8",
                9  => "Port 9",
                11 => "SV1 Pin3",
                12 => "SV1 Pin4",
        ),
        "DigitalOutputs" => array(
                1  => "Port 1",
                2  => "Port 2",
                3  => "Port 3",
                4  => "Port 4",
                5  => "Port 5",
                6  => "Port 6",
                7  => "Port 7",
                8  => "Port 8",
                9  => "Port 9",
                11 => "SV1 Pin3",
                12 => "SV1 Pin4",
        ),
        "DaughterBoards" => array(
            "0039-15-01-A" => "0039-15-01-A Generic Input",
            "0039-15-01-C" => "0039-15-01-C Light Sensor",
            "0039-16-01-A" => "0039-16-01-A FET Board",
            "0039-23-01-A" => "0039-23-01-A Weather Station",
            "0039-23-01-C" => "0039-23-01-C Relay Driver",
            "0039-23-01-D" => "0039-23-01-D Generic Input",
        ),
        "configImage" => "00391201.svg",
    );

}


?>
