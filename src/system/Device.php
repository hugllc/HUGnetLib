<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/SystemTableBase.php";

/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Device extends SystemTableBase
{
    /**
    * This is the cache for the drivers.
    */
    private $_driverCache = array();
    /**
    * This is the cache for the drivers.
    */
    private $_network = null;
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        foreach (array_keys($this->_driverCache) as $key) {
            unset($this->_driverCache[$key]);
        }
        unset($this->_network);
        parent::__destruct();
    }
    /**
    * This function creates the system.
    *
    * @param mixed  $system (object)The system object to use
    * @param mixed  $data   (int)The id of the item, (array) data info array
    * @param string $table  The table to use
    *
    * @return null
    */
    public static function &factory($system, $data=null, $table="DevicesTable")
    {
        $object = &parent::factory($system, $data, $table);
        return $object;
    }

    /**
    * This function creates the system.
    *
    * @return Reference to the network object
    */
    public function &network()
    {
        if (!is_object($this->_network)) {
            include_once dirname(__FILE__)."/../devices/Network.php";
            $this->_network = \HUGnet\devices\Network::factory(
                $this->system()->network(),
                $this->table()
            );
        }
        return $this->_network;
    }
    /**
    * This function creates the system.
    *
    * @return Reference to the network object
    */
    public function &config()
    {
        if (!is_object($this->_config)) {
            include_once dirname(__FILE__)."/../devices/Config.php";
            $this->_config = \HUGnet\devices\Config::factory(
                $this->table()
            );
        }
        return $this->_config;
    }
    /**
    * This creates the driver
    *
    * @param string $driver The driver to use.  Leave blank for automatic.
    *
    * @return null
    */
    public function &driver($driver = null)
    {
        if (empty($driver)) {
            $driver = $this->get("Driver");
        }
        if (empty($driver)) {
            $driver == "EDEFAULT";
        }
        if (!is_object($this->_driverCache[$driver])) {
            include_once dirname(__FILE__)."/../devices/Driver.php";
            $this->_driverCache[$driver] = &devices\Driver::factory($driver);
        }
        return $this->_driverCache[$driver];
    }

}


?>
