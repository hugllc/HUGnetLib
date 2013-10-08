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
class E00392802 extends \HUGnet\devices\Driver
    implements \HUGnet\devices\drivers\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "totalSensors" => 20,
        "physicalSensors" => 16,
        "virtualSensors" => 4,
        "historyTable" => "E00392800History",
        "averageTable" => "E00392800Average",
        "type" => "endpoint",
        "job"  => "sense",
        "arch" => "0039-28",
        "InputTables" => 16,
        "OutputTables" => 8,
        "ProcessTables" => 2,
        "DataChannels" => 20,
        "DigitalInputs" => array(
                1  => "Port 1",
                2  => "Port 2",
                3  => "Port 3",
                4  => "Port 4",
                5  => "Port 5",
                6  => "Port 6",
                9  => "Port 9",
                10 => "Port 10",
                11 => "Port 11",
                12 => "Port 12",
                13 => "Port 13",
                14 => "Port 14",
                15 => "Port 15",
                16 => "Port 16",
        ),
        "DigitalOutputs" => array(
                1  => "Port 1",
                2  => "Port 2",
                3  => "Port 3",
                4  => "Port 4",
                5  => "Port 5",
                6  => "Port 6",
                9  => "Port 9",
                10 => "Port 10",
                11 => "Port 11",
                12 => "Port 12",
                13 => "Port 13",
                14 => "Port 14",
                15 => "Port 15",
                16 => "Port 16",
        ),
    );

}


?>
