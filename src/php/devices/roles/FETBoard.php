<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.11.0
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.11.0
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class FETBoard extends \HUGnet\base\Role
    implements RoleInterface
{
    /**
    *  This is the input table data
    */
    protected $input = array(
        0 => array(            // Current
            "table" => array(
                "driver" => "50:fetBoard",
                "name" => "FET Board Current",
                "MUX" => 0,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
            "data" => array(
                "id" => 0xF8,
                "extra" => array(0.5, 1, 5.0),
                "location" => "Channel 1 Current",
                "type" => "AVRAnalogTable",
            ),
        ),
        1 => array(            // Voltage
            "table" => array(
                "driver" => "40:fetBoard",
                "name" => "FET Board Voltage",
                "MUX" => 1,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
            "data" => array(
                "id" => 0xF8,
                "extra" => array(150, 10, 5.0),
                "location" => "Channel 1 Voltage",
                "type" => "AVRAnalogTable",
            ),
        ),
        2 => array(            // Current
            "table" => array(
                "driver" => "50:fetBoard",
                "name" => "FET Board Current",
                "MUX" => 2,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
            "data" => array(
                "id" => 0xF8,
                "extra" => array(0.5, 1, 5.0),
                "location" => "Channel 2 Current",
                "type" => "AVRAnalogTable",
            ),
        ),
        3 => array(            // Voltage
            "table" => array(
                "driver" => "40:fetBoard",
                "name" => "FET Board Voltage",
                "MUX" => 3,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
            "data" => array(
                "id" => 0xF8,
                "extra" => array(150, 10, 5.0),
                "location" => "Channel 2 Voltage",
                "type" => "AVRAnalogTable",
            ),
        ),
        4 => array(            // Current
            "table" => array(
                "driver" => "50:fetBoard",
                "name" => "FET Board Current",
                "MUX" => 4,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
            "data" => array(
                "id" => 0xF8,
                "extra" => array(0.5, 1, 5.0),
                "location" => "Channel 3 Current",
                "type" => "AVRAnalogTable",
            ),
        ),
        5 => array(            // Voltage
            "table" => array(
                "driver" => "40:fetBoard",
                "name" => "FET Board Voltage",
                "MUX" => 5,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
            "data" => array(
                "id" => 0xF8,
                "extra" => array(150, 10, 5.0),
                "location" => "Channel 3 Voltage",
                "type" => "AVRAnalogTable",
            ),
        ),
        6 => array(            // Current
            "table" => array(
                "driver" => "50:fetBoard",
                "name" => "FET Board Current",
                "MUX" => 6,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
            "data" => array(
                "id" => 0xF8,
                "extra" => array(0.5, 1, 5.0),
                "location" => "Channel 4 Current",
                "type" => "AVRAnalogTable",
            ),
        ),
        7 => array(            // Voltage
            "table" => array(
                "driver" => "40:fetBoard",
                "name" => "FET Board Voltage",
                "MUX" => 7,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
            "data" => array(
                "id" => 0xF8,
                "extra" => array(150, 10, 5.0),
                "location" => "Channel 4 Voltage",
                "type" => "AVRAnalogTable",
            ),
        ),
        8 => array(            // Supply Voltage
            "table" => array(
                "driver" => "40:fetBoard",
                "name" => "FET Board Voltage",
                "MUX" => 8,
                "id" => 0,
                "ADLAR" => 1,
                "REFS" => 1,
            ),
            "data" => array(
                "id" => 0xF8,
                "extra" => array(150, 10, 5.0),
                "location" => "Supply Voltage",
                "type" => "AVRAnalogTable",
            ),
        ),
    );
    /**
    *  This is the output table data
    */
    protected $output = array(
        0 => array(
            "table" => array(
            ),
            "data" => array(
                "location" => "Channel 1",
                "extra"    => array(1),
                "id"       => 0x31,
                "type"     => "FET003912",
            ),
        ),
        1 => array(
            "table" => array(
            ),
            "data" => array(
                "location" => "Channel 2",
                "extra"    => array(2),
                "id"       => 0x31,
                "type"     => "FET003912",
            ),
        ),
        2 => array(
            "table" => array(
            ),
            "data" => array(
                "location" => "Channel 3",
                "extra"    => array(3),
                "id"       => 0x31,
                "type"     => "FET003912",
            ),
        ),
        3 => array(
            "table" => array(
            ),
            "data" => array(
                "location" => "Channel 4",
                "extra"    => array(4),
                "id"       => 0x31,
                "type"     => "FET003912",
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
