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
namespace HUGnet\devices\roles;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is the base of our base class */
require_once dirname(__FILE__)."/../../base/Role.php";
/** This is the base of our Interface */
require_once dirname(__FILE__)."/RoleInterface.php";

/**
 * Base driver class for devices.
 *
 * This class deals with loading the drivers and figuring out what driver needs
 * to be loaded.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.14.4
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Type4 extends \HUGnet\base\Role
    implements RoleInterface
{
    /**
    *  This is the input table data
    */
    protected $input = array(
        0 => array(
            "id" => 0x50,
            "extra" => array(0.001, 64.0, 1.0),
            "location" => "Port 1 Current",
            "type" => "XMegaCurrent",
        ),
        1 => array(
            "id" => 0x40,
            "extra" => array(200.0, 10.0, 1.0),
            "location" => "Port 1 Voltage",
            "type" => "XMegaVoltage",
        ),
        2 => array(
            "id" => 0x02,
            "extra" => array(160),
            "location" => "Port 1 Temperature",
            "type" => "MurataNCPP18XH",
        ),
        3 => array(
            "id" => 0x50,
            "extra" => array(0.001, 64.0, 1.0),
            "location" => "Port 2 Current",
            "type" => "XMegaCurrent",
        ),
        4 => array(
            "id" => 0x40,
            "extra" => array(200.0, 10.0, 1.0),
            "location" => "Port 2 Voltage",
            "type" => "XMegaVoltage",
        ),
        5 => array(
            "id" => 0x02,
            "extra" => array(160),
            "location" => "Port 2 Temperature",
            "type" => "MurataNCPP18XH",
        ),
        
        6 => array(
            "id" => 0x40,
            "extra" => array(200, 10.0, 1.0),
            "location" => "Bus Voltage",
            "type" => "XMegaVoltage",
        ),
        7 => array(
            "id" => 0x02,
            "extra" => array(160),
            "location" => "Bus Temperature",
            "type" => "MurataNCPP18XH",
        ),
        8 => array(
            "id" => 0x40,
            "extra" => array(1.0),
            "location" => "Internal VCC",
            "type" => "XMegaVCC",
        ),
        9 => array(
            "id" => 0x02,
            "extra" => array(160),
            "location" => "External Thermistor 1",
            "type" => "MurataNCPP18XH",
        ),
        10 => array(
            "id" => 0x02,
            "extra" => array(160),
            "location" => "External Thermistor 2",
            "type" => "MurataNCPP18XH",
        ),
    );
    /**
    *  This is the output table data
    */
    protected $output = array(
        0 => array(
            "id" => 0x40,
            "extra" => array(),
            "location" => "Power Port 1",
            "type" => "FETCtrl104603",
        ),
        1 => array(
            "id" => 0x40,
            "extra" => array(),
            "location" => "Power Port 2",
            "type" => "FETCtrl104603",
        ),
    );
    /**
    *  This is the process table data
    */
    protected $process = array(
    );
    /**
    *  This is the process table data
    */
    protected $power = array(
    );
}


?>
