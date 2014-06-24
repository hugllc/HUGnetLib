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
class TesterKnownGood extends \HUGnet\base\Role
    implements RoleInterface
{
    /**
    *  This is the input table data
    */
    protected $input = array(
        0 => array(            // HUGnetLab Input 1 Voltage
            "id" => 0xF9,
            "extra" => array(1, 10, 1.2),
            "location" => "HUGnetLab Known Good Voltage",
            "type" => "ADuCInputTable",
            "tableEntry" => array(
                "driver0" => 0x41,
                "name" => "ADuC Input Voltage",
                "priority" => 0x01,
                "ADC0EN" => 1,
                "ADC0CH" => 7,
                "ADC1EN" => 0,
                "AF" => 0,
                "SF" => 9,
            ),
        ),
        1 => array(            // HUGnetLab Input 2 Voltage
            "id" => 0xF9,
            "extra" => array(1, 10, 1.2),
            "location" => "HUGnetLab Known Good Voltage",
            "type" => "ADuCInputTable",
            "tableEntry" => array(
                "driver0" => 0x41,
                "name" => "ADuC Input Voltage",
                "priority" => 0x01,
                "ADC0EN" => 1,
                "ADC0CH" => 6,
                "ADC1EN" => 0,
                "AF" => 0,
                "SF" => 9,
            ),
        ),
        2 => array(            // HUGnetLab Input 3 Voltage
            "id" => 0xF9,
            "extra" => array(1, 10, 1.2),
            "location" => "HUGnetLab Known Good Voltage",
            "type" => "ADuCInputTable",
            "tableEntry" => array(
                "driver0" => 0x41,
                "name" => "ADuC Input Voltage",
                "priority" => 0x01,
                "ADC0EN" => 1,
                "ADC0CH" => 2,
                "ADC1EN" => 0,
                "AF" => 0,
                "SF" => 9,
            ),
        ),
        3 => array(            // HUGnetLab Input 4 Voltage
            "id" => 0xF9,
            "extra" => array(1, 10, 1.2),
            "location" => "HUGnetLab Known Good Voltage",
            "type" => "ADuCInputTable",
            "tableEntry" => array(
                "driver0" => 0x41,
                "name" => "ADuC Input Voltage",
                "priority" => 0x01,
                "ADC0EN" => 1,
                "ADC0CH" => 1,
                "ADC1EN" => 0,
                "AF" => 0,
                "SF" => 9,
            ),
        ),
        4 => array(            // HUGnetLab Input 5 Voltage
            "id" => 0xF9,
            "extra" => array(100, 1, 1.2),
            "location" => "HUGnetLab Known Good Voltage",
            "type" => "ADuCInputTable",
            "tableEntry" => array(
                "driver0" => 0x41,
                "name" => "ADuC Input Voltage",
                "priority" => 0x01,
                "ADC0EN" => 0,
                "ADC1CH" => 7,
                "ADC1EN" => 1,
                "AF" => 0,
                "SF" => 9,
            ),
        ),
        5 => array(            // HUGnetLab Input 6 Voltage
            "id" => 0xF9,
            "extra" => array(100, 1, 1.2),
            "location" => "HUGnetLab Known Good Voltage",
            "type" => "ADuCInputTable",
            "tableEntry" => array(
                "driver0" => 0x41,
                "name" => "ADuC Input Voltage",
                "priority" => 0x01,
                "ADC0EN" => 0,
                "ADC1CH" => 8,
                "ADC1EN" => 1,
                "AF" => 0,
                "SF" => 9,
            ),
        ),
        6 => array(            // HUGnetLab Input 7 Voltage
            "id" => 0xF9,
            "extra" => array(100, 1, 1.2),
            "location" => "HUGnetLab Known Good Voltage",
            "type" => "ADuCInputTable",
            "tableEntry" => array(
                "driver0" => 0x41,
                "name" => "ADuC Input Voltage",
                "priority" => 0x01,
                "ADC0EN" => 0,
                "ADC1CH" => 9,
                "ADC1EN" => 1,
                "AF" => 0,
                "SF" => 9,
            ),
        ),
        7 => array(            // HUGnetLab Input 8 Voltage
            "id" => 0xF9,
            "extra" => array(100, 1, 1.2),
            "location" => "HUGnetLab Known Good Voltage",
            "type" => "ADuCInputTable",
            "tableEntry" => array(
                "driver0" => 0x41,
                "name" => "ADuC Input Voltage",
                "priority" => 0x01,
                "ADC0EN" => 0,
                "ADC1CH" => 10,
                "ADC1EN" => 1,
                "AF" => 0,
                "SF" => 9,
            ),
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
}


?>
