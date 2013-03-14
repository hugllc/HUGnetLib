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
namespace HUGnet\devices;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

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
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Role
{
    
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @return null
    */
    private function __construct()
    {
    }
    /**
    *  This builds the class
    *
    * @return The class object
    */
    public static function &factory()
    {
        return new Role();
    }
    /**
    * This creates the sensor drivers
    *
    * @param string $name The name of the role
    * @param int    $iid  The sensor id to get.  They are zero based
    *
    * @return null if not found, array otherwise
    */
    public function input($name, $iid)
    {
        return null;
    }
    /**
    * This creates the sensor drivers
    *
    * @param string $name The name of the role
    * @param int    $oid  The sensor id to get.  They are zero based
    *
    * @return null if not found, array otherwise
    */
    public function output($name, $oid)
    {
        return null;
    }
    /**
    * This creates the sensor drivers
    *
    * @param string $name The name of the role
    * @param int    $pid  The sensor id to get.  They are zero based
    *
    * @return null if not found, array otherwise
    */
    public function process($name, $pid)
    {
        return null;
    }
}


?>
