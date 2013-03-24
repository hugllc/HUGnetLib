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
 * @version    Release: 0.11.0
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.11.0
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Role
{
    /** This is my role */
    private $_roles = array();
    /**
    * This is where the correlation between the drivers and the arch is stored.
    *
    * If a driver is not registered here, it will not be in the list of drivers
    * that can be chosen.
    *
    */
    private $_arch = array(
        "0039-12" => array(
        ),
        "0039-21-01" => array(
            "Controller" => "Controller",
        ),
        "0039-21-02" => array(
            "Controller" => "Controller",
        ),
        "0039-28" => array(
        ),
        "0039-37" => array(
        ),
        "Linux" => array(
        ),
        "all" => array(
        ),
    );

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
    *  This builds the class
    *
    * @param string $role The role to use
    *
    * @return The class object
    */
    private function _getRole($role)
    {
        if (is_string($role)) {
            if (!is_object($this->_roles[$role])) {
                $file = dirname(__FILE__)."/roles/".$role.".php";
                $class = "\\HUGnet\\devices\\roles\\".$role;
                if (file_exists($file)) {
                    include_once $file;
                }
                $interface = "\\HUGnet\\devices\\roles\\RoleInterface";
                if (is_subclass_of($class, $interface)) {
                    $this->_roles[$role] = $class::factory();
                }
            }
            if (is_object($this->_roles[$role])) {
                return $this->_roles[$role];
            }
        }
        return null;
    }
    /**
    * This creates the sensor drivers
    *
    * @param string $role The name of the role
    * @param int    $iid  The sensor id to get.  They are zero based
    *
    * @return null if not found, array otherwise
    */
    public function input($role, $iid)
    {
        $uRole = $this->_getRole($role);
        if (is_object($uRole)) {
            return $uRole->input($iid);
        }
        return null;
    }
    /**
    * This creates the sensor drivers
    *
    * @param string $role The name of the role
    * @param int    $oid  The sensor id to get.  They are zero based
    *
    * @return null if not found, array otherwise
    */
    public function output($role, $oid)
    {
        $uRole = $this->_getRole($role);
        if (is_object($uRole)) {
            return $uRole->output($oid);
        }
        return null;
    }
    /**
    * This creates the sensor drivers
    *
    * @param string $role The name of the role
    * @param int    $pid  The sensor id to get.  They are zero based
    *
    * @return null if not found, array otherwise
    */
    public function process($role, $pid)
    {
        $uRole = $this->_getRole($role);
        if (is_object($uRole)) {
            return $uRole->process($pid);
        }
        return null;
    }
    /**
    * This creates the sensor drivers
    *
    * @param string $arch The architecture to get the roles for
    *
    * @return null if not found, array otherwise
    */
    public function getAll($arch)
    {
        if (is_array($this->_arch[$arch])) {
            return $this->_arch[$arch];
        }
        return null;
    }
}


?>
