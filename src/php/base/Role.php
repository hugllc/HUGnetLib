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
namespace HUGnet\base;
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
abstract class Role
{
    /**
    *  This is the input table data
    */
    protected $input = array(
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
            "controller" => "Controller",
        ),
        "0039-21-02" => array(
            "controller" => "Controller",
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
        $class = get_called_class();
        $object = new $class();
        return $object;
    }
    /**
    * This returns the data for the give id if there is any.
    *
    * @param int $iid The sensor id to get.  They are zero based
    *
    * @return null if not found, array otherwise
    */
    public function input($iid)
    {
        if (isset($this->input[(int)$iid])) {
            return $this->input[(int)$iid];
        }
        return null;
    }
    /**
    * This returns the data for the give id if there is any.
    *
    * @param int $oid The sensor id to get.  They are zero based
    *
    * @return null if not found, array otherwise
    */
    public function output($oid)
    {
        if (isset($this->output[(int)$oid])) {
            return $this->output[(int)$oid];
        }
        return null;
    }
    /**
    * This returns the data for the give id if there is any.
    *
    * @param int $pid The sensor id to get.  They are zero based
    *
    * @return null if not found, array otherwise
    */
    public function process($pid)
    {
        if (isset($this->process[(int)$pid])) {
            return $this->process[(int)$pid];
        }
        return null;
    }
}


?>
