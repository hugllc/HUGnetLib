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
 * @since      0.11.0
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Controller extends \HUGnet\base\Role
    implements RoleInterface
{
    /**
    *  This is the input table data
    */
    protected $input = array(
        0 => array(            // HUGnet1 Voltage High
            "id" => 0xF8,
            "extra" => array(0, 180, 27, 5.0),
            "location" => "HUGnet 1 Voltage High",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "40:DEFAULT",
                "name" => "Controller Board Voltage",
                "MUX" => 4,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        1 => array(            // HUGnet1 Voltage Low
            "id" => 0xF8,
            "extra" => array(0, 180, 27, 5.0),
            "location" => "HUGnet 1 Voltage Low",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "40:DEFAULT",
                "name" => "Controller Board Voltage",
                "MUX" => 5,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        2 => array(            // HUGnet1 Current
            "id" => 0xF8,
            "extra" => array(0, 0.5, 7, 5.0),
            "location" => "HUGnet 1 Current",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "50:DEFAULT",
                "name" => "Controller Board Current",
                "MUX" => 7,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        3 => array(            // HUGnet1 Temperature
            "id" => 0xF8,
            "extra" => array(0, 100, 10),
            "location" => "HUGnet 1 Temperature",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "02:AVRBC2322640",
                "name" => "Controller Board Temperature",
                "MUX" => 6,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        4 => array(            // HUGnet2 Voltage High
            "id" => 0xF8,
            "extra" => array(0, 180, 27, 5.0),
            "location" => "HUGnet 2 Voltage High",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "40:DEFAULT",
                "name" => "Controller Board Voltage",
                "MUX" => 3,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        5 => array(            // HUGnet2 Voltage Low
            "id" => 0xF8,
            "extra" => array(0, 180, 27, 5.0),
            "location" => "HUGnet 2 Voltage Low",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "40:DEFAULT",
                "name" => "Controller Board Voltage",
                "MUX" => 2,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        6 => array(            // HUGnet2 Current
            "id" => 0xF8,
            "extra" => array(0, 0.5, 7, 5.0),
            "location" => "HUGnet 2 Current",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "50:DEFAULT",
                "name" => "Controller Board Current",
                "MUX" => 0,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        7 => array(            // HUGnet2 Temperature
            "id" => 0xF8,
            "extra" => array(0, 100, 10),
            "location" => "HUGnet 2 Temperature",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "02:AVRBC2322640",
                "name" => "Controller Board Temperature",
                "MUX" => 1,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
    );
    /**
    *  This is the output table data
    */
    protected $output = array(
        0 => array(
            // HUGnet 0
            "extra" => array(0, 1),
            "location" => "HUGnet 1 Power",
            "id"     => 0x30,
            "type"   => "HUGnetPower",
            "tableEntry" => array(),
        ),
        1 => array(
            // HUGnet 1
            "extra" => array(1, 1),
            "location" => "HUGnet 2 Power",
            "id"     => 0x30,
            "type"   => "HUGnetPower",
            "tableEntry" => array(),
        ),
    );
    /**
    *  This is the process table data
    */
    protected $process = array(
    );
}


?>
