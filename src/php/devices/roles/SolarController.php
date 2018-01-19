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
class SolarController extends \HUGnet\base\Role
    implements RoleInterface
{
    /**
    *  This is the input table data
    */
    protected $input = array(
        0 => array(            // HUGnet1 Voltage High
            "id" => 0xF8,
            "extra" => array(0, 10, 10),
            "location" => "Channel 1 Low",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "02:AVRBC2322640",
                "name" => "Solar Controller Temp",
                "MUX" => 0,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        1 => array(            // HUGnet1 Voltage Low
            "id" => 0xF8,
            "extra" => array(0, 10, 10),
            "location" => "Channel 1 High",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "02:AVRBC2322640",
                "name" => "Solar Controller Temp",
                "MUX" => 1,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        2 => array(            // HUGnet1 Current
            "id" => 0xF8,
            "extra" => array(0, 10, 10),
            "location" => "Channel 2 Low",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "02:AVRBC2322640",
                "name" => "Solar Controller Temp",
                "MUX" => 2,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        3 => array(            // HUGnet1 Temperature
            "id" => 0xF8,
            "extra" => array(0, 10, 10),
            "location" => "Channel 2 High",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "02:AVRBC2322640",
                "name" => "Solar Controller Temp",
                "MUX" => 3,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        4 => array(            // HUGnet2 Voltage High
            "id" => 0xF8,
            "extra" => array(0, 10, 10),
            "location" => "Channel 3 Low",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "02:AVRBC2322640",
                "name" => "Solar Controller Temp",
                "MUX" => 4,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        5 => array(            // HUGnet2 Voltage Low
            "id" => 0xF8,
            "extra" => array(0, 10, 10),
            "location" => "Channel 3 High",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "02:AVRBC2322640",
                "name" => "Solar Controller Temp",
                "MUX" => 5,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        6 => array(            // HUGnet2 Current
            "id" => 0xF8,
            "extra" => array(0, 10, 10),
            "location" => "Channel 4 Low",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "02:AVRBC2322640",
                "name" => "Solar Controller Temp",
                "MUX" => 6,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
        ),
        7 => array(            // HUGnet2 Temperature
            "id" => 0xF8,
            "extra" => array(0, 10, 10),
            "location" => "Channel 4 High",
            "type" => "AVRAnalogTable",
            "tableEntry" => array(
                "driver" => "02:AVRBC2322640",
                "name" => "Solar Controller Temp",
                "MUX" => 7,
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
            "extra" => array(0, 9),
            "location" => "Channel 1 Output",
            "id"     => 0x32,
            "type"   => "GPIO003928",
            "tableEntry" => array(
            ),
        ),
        1 => array(
            // HUGnet 0
            "extra" => array(0, 10),
            "location" => "Channel 2 Output",
            "id"     => 0x32,
            "type"   => "GPIO003928",
            "tableEntry" => array(
            ),
        ),
        2 => array(
            // HUGnet 0
            "extra" => array(0, 11),
            "location" => "Channel 3 Output",
            "id"     => 0x32,
            "type"   => "GPIO003928",
            "tableEntry" => array(
            ),
        ),
        3 => array(
            // HUGnet 0
            "extra" => array(0, 12),
            "location" => "Channel 4 Output",
            "id"     => 0x32,
            "type"   => "GPIO003928",
            "tableEntry" => array(
            ),
        ),
        4 => array(
            // HUGnet 0
            "extra" => array(0, 13),
            "location" => "Channel 1 Alarm",
            "id"     => 0x32,
            "type"   => "GPIO003928",
            "tableEntry" => array(
            ),
        ),
        5 => array(
            // HUGnet 0
            "extra" => array(0, 14),
            "location" => "Channel 2 Alarm",
            "id"     => 0x32,
            "type"   => "GPIO003928",
            "tableEntry" => array(
            ),
        ),
        6 => array(
            // HUGnet 0
            "extra" => array(0, 15),
            "location" => "Channel 3 Alarm",
            "id"     => 0x32,
            "type"   => "GPIO003928",
            "tableEntry" => array(
            ),
        ),
        7 => array(
            // HUGnet 0
            "extra" => array(0, 16),
            "location" => "Channel 4 Alarm",
            "id"     => 0x32,
            "type"   => "GPIO003928",
            "tableEntry" => array(
            ),
        ),
    );
    /**
    *  This is the process table data
    */
    protected $process = array(
    );
}


?>
