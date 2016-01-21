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
class BatSocComm extends \HUGnet\base\Role
    implements RoleInterface
{
    /**
    *  This is the input table data
    */
    protected $input = array(
        0 => array(
            "id" => 0x80,
            "location" => "System Current",
            "type" => "Current",
        ),
        1 => array(
            "id" => 0x80,
            "location" => "Bus Voltage",
            "type" => "Voltage",
        ),
        2 => array(
            "id" => 0x80,
            "location" => "Bus Temperature",
            "type" => "Temperature",
        ),
        3 => array(
            "id" => 0x80,
            "location" => "System Charge",
            "type" => "BatCapacity",
        ),
        4 => array(
            "id" => 0x80,
            "location" => "System Capacity",
            "type" => "BatCapacity",
        ),
        5 => array(
            "id" => 0x80,
            "location" => "System Status",
            "type" => "BatSysStatus",
        ),
    );
    /**
    *  This is the output table data
    */
    protected $output = array(
    /*
        0 => array(
            "id" => 0x40,
            "extra" => array(),
            "location" => "Power Port 1",
            "type" => "FETCtrl104603",
        ),
        */
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
