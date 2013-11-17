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
class E00393700 extends \HUGnet\devices\Driver
    implements \HUGnet\devices\drivers\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "totalSensors" => 20,
        "physicalSensors" => 9,
        "virtualSensors" => 11,
        "historyTable" => "E00393700History",
        "averageTable" => "E00393700Average",
        "loadable" => true,
        "packetTimeout" => 2,
        "type" => "endpoint",
        "job"  => "sense",
        "arch" => "0039-37",
        "InputTables" => 9,
        "OutputTables" => 5,
        "DataChannels" => 20,
        "DigitalInputs" => array(
                2 => "P0.0",
                3 => "P0.1",
                4 => "P0.2",
                5 => "P0.3",
                6 => "P0.4",
                7 => "P1.4",
                8 => "P1.5",
                9 => "P1.6",
                10 => "P2.0",
                11 => "P2.1",
        ),
        "DigitalOutputs" => array(
                0 => "P1.2",
                1 => "P1.3",
                2 => "P0.0",
                3 => "P0.1",
                4 => "P0.2",
                5 => "P0.3",
                6 => "P0.4",
                7 => "P1.4",
                8 => "P1.5",
                9 => "P1.6",
                10 => "P2.0",
                11 => "P2.1",
        ),
        "ProcessTables" => 4,
    );

}


?>
