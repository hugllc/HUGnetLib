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
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.11.0
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Type3 extends \HUGnet\base\Role
    implements RoleInterface
{
    /**
    *  This is the input table data
    */
    protected $input = array(
        0 => array(
            "id" => 0x7D,
            "extra" => array(),
            "location" => "Status 1",
            "type" => "XMegaStatus",
        ),
        1 => array(
            "id" => 0x50,
            "extra" => array(0.001, 64.0, 1.0),
            "location" => "PC Current",
            "type" => "XMegaCurrent",
        ),
        2 => array(
            "id" => 0x40,
            "extra" => array(40.2, 2.0, 1.0),
            "location" => "Voltage 1",
            "type" => "XMegaVoltage",
        ),
        3 => array(
            "id" => 0x40,
            "extra" => array(40.2, 2.0, 1.0),
            "location" => "Voltage 2",
            "type" => "XMegaVoltage",
        ),
        4 => array(
            "id" => 0x40,
            "extra" => array(40.2, 2.0, 1.0),
            "location" => "Voltage 3",
            "type" => "XMegaVoltage",
        ),
        5 => array(
            "id" => 0x40,
            "extra" => array(40.2, 2.0, 1.0),
            "location" => "Voltage 4",
            "type" => "XMegaVoltage",
        ),
        6 => array(
            "id" => 0x40,
            "extra" => array(40.2, 2.0, 1.0),
            "location" => "Voltage 5",
            "type" => "XMegaVoltage",
        ),
        7 => array(
            "id" => 0x40,
            "extra" => array(40.2, 2.0, 1.0),
            "location" => "Voltage 6",
            "type" => "XMegaVoltage",
        ),
        8 => array(
            "id" => 0x40,
            "extra" => array(40.2, 2.0, 1.0),
            "location" => "Voltage 7",
            "type" => "XMegaVoltage",
        ),
        9 => array(
            "id" => 0x40,
            "extra" => array(40.2, 2.0, 1.0),
            "location" => "Voltage 8",
            "type" => "XMegaVoltage",
        ),
        10 => array(
            "id" => 0x40,
            "extra" => array(40.2, 2.0, 1.0),
            "location" => "BB Voltage",
            "type" => "XMegaVoltage",
        ),
        11 => array(
            "id" => 0x02,
            "location" => "Lower Internal Temperature",
            "type" => "XMegaTemp",
        ),
        12 => array(
            "id" => 0x40,
            "extra" => array(1.0),
            "location" => "Lower Internal VCC",
            "type" => "XMegaVCC",
        ),
        13 => array( 
            "id" => 0x40,
            "extra" => array(0, 0, 1.0),
            "location" => "Lower Internal DAC",
            "type" => "XMegaVoltage",
        ),
        14 => array(
            "id" => 0x7D,
            "extra" => array(),
            "location" => "Status 2",
            "type" => "XMegaStatus",
        ),
        15 => array(
            "id" => 0x50,
            "extra" => array(0.001, 64.0, 1.0),
            "location" => "Current 1",
            "type" => "XMegaCurrent",
        ),
        16 => array(
            "id" => 0x50,
            "extra" => array(0.001, 64.0, 1.0),
            "location" => "Current 2",
            "type" => "XMegaCurrent",
        ),
        17 => array(
            "id" => 0x50,
            "extra" => array(0.001, 64.0, 1.0),
            "location" => "Current 3",
            "type" => "XMegaCurrent",
        ),
        18 => array(
            "id" => 0x50,
            "extra" => array(0.001, 64.0, 1.0),
            "location" => "Current 4",
            "type" => "XMegaCurrent",
        ),
        19 => array(
            "id" => 0x50,
            "extra" => array(0.001, 64.0, 1.0),
            "location" => "Current 5",
            "type" => "XMegaCurrent",
        ),
        20 => array(
            "id" => 0x50,
            "extra" => array(0.001, 64.0, 1.0),
            "location" => "Current 6",
            "type" => "XMegaCurrent",
        ),
        21 => array(
            "id" => 0x50,
            "extra" => array(0.001, 64.0, 1.0),
            "location" => "Current 7",
            "type" => "XMegaCurrent",
        ),
        22 => array(
            "id" => 0x50,
            "extra" => array(0.001, 64.0, 1.0),
            "location" => "Current 8",
            "type" => "XMegaCurrent",
        ),
        23 => array(
            "id" => 0x40,
            "extra" => array(40.2, 2.0, 1.0),
            "location" => "V++",
            "type" => "XMegaVoltage",
        ),
        24 => array(
            "id" => 0x40,
            "extra" => array(33, 3.3, 1.0),
            "location" => "U5V",
            "type" => "XMegaVoltage",
        ),
        25 => array(
            "id" => 0x02,
            "extra" => array(10),
            "location" => "Thermistor 1",
            "type" => "MurataNCPP18XH",
        ),
        26 => array(
            "id" => 0x02,
            "extra" => array(10),
            "location" => "Thermistor 2",
            "type" => "MurataNCPP18XH",
        ),
        27 => array(
            "id" => 0x02,
            "extra" => array(10),
            "location" => "UpAnalog 1",
            "type" => "MurataNCPP18XH",
        ),
        28 => array(
            "id" => 0x02,
            "extra" => array(10),
            "location" => "UpAnalog 2",
            "type" => "MurataNCPP18XH",
        ),
        29 => array(
            "id" => 0x02,
            "location" => "Upper Internal Temperature",
            "type" => "XMegaTemp",
        ),
        30 => array(
            "id" => 0x40,
            "extra" => array(1.0),
            "location" => "Upper Internal VCC",
            "type" => "XMegaVCC",
        ),
        31 => array( 
            "id" => 0x40,
            "extra" => array(0, 0, 1.0),
            "location" => "Upper Internal DAC",
            "type" => "XMegaVoltage",
        ),
    );
    /**
    *  This is the output table data
    */
    protected $output = array(
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
        0 => array( 
            "id" => 0xFE,
            "location" => "Power Converter",
        ),
    );
}


?>
