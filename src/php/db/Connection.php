<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
namespace HUGnet\db;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is for the base class */
require_once dirname(__FILE__)."/../interfaces/ConnectionManager.php";


/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Connection implements \ConnectionManager
{
    /** @var array This is where our system object is kept */
    private $_system = null;
    /** @var array This is where the server information is stored */
    private $_servers = array();
    /** @var array This is where the server objects are stored */
    private $_server = array();
    /** @var array This is where the server drivers are stored */
    private $_drivers = array();
    /** @var array This is where the groups are stored */
    private $_groups = array();
    /**
    * Creates the object
    *
    * @param object &$system The system object to use
    */
    private function __construct(&$system)
    {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $this->_system = &$system;
        $servers = (array)$system->get("servers");
        foreach ($servers as $key => $val) {
            $group = $val["group"];
            if (empty($group)) {
                $group = "default";
            }
            $this->_servers[$group][$key] = $val;
            $this->_drivers[$group] = $val["driver"];
            $this->_groups[$group] = $group;
        }
        if (empty($this->_servers)) {
            $this->_servers = array(
                "default" => array(
                    array(
                        "group" => "default",
                        "driver" => "sqlite",
                    )
                )
            );
        }
    }
    /**
    * This function creates the system.
    *
    * @param object &$system The system object to use
    *
    * @return null
    */
    public static function &factory(&$system)
    {
        /** This is for the base class */
        $obj = new Connection($system);
        return $obj;
    }
    /**
    * This function creates the system.
    *
    * @param string $group The group to use
    *
    * @return null
    */
    private function &_driverFactory($group)
    {
        $ret = false;
        if (is_array($this->_servers[$group])) {
            if ($this->_drivers[$group] === "mongodb") {
                include_once dirname(__FILE__)."/connections/Mongodb.php";
                $this->_server[$group] = \HUGnet\db\connections\Mongodb::factory(
                    $this->_system, $this->_servers[$group]
                );
                $ret = true;
            } else {
                include_once dirname(__FILE__)."/connections/PDO.php";
                $this->_server[$group] = \HUGnet\db\connections\PDO::factory(
                    $this->_system, $this->_servers[$group]
                );
                $ret = true;
            }
        }
        return $ret;
    }
    /**
    * Destroys the object
    *
    */
    public function __destruct()
    {
        foreach (array_keys((array)$this->_server) as $group) {
            unset($this->_server[$group]);
        }
        unset($this->_drivers);
        unset($this->_servers);
        unset($this->_groups);
    }

    /**
    * Checks to see if we are connected to a database
    *
    * @param string $group The group to check
    *
    * @return object PDO object, null on failure
    */
    public function connected($group = "default")
    {
        return is_object($this->_server[$group])
            && (is_a($this->_server[$group], "\HUGnet\interfaces\DBConnection"))
            && $this->_server[$group]->connected();
    }
    /**
    * Checks to see if we are connected to a database
    *
    * @param string $group The group to check
    *
    * @return object PDO object, null on failure
    */
    public function driver($group = "default")
    {
        $this->connect($group);
        if ($this->connected($group)) {
            return $this->_server[$group]->driver();
        }
        return "sqlite";
    }
    /**
    * Tries to connect to a database servers
    *
    * @param string $group The group to get the config of
    *
    * @return bool True on success, false on failure
    */
    public function config($group = "default")
    {
        $this->connect($group);
        if ($this->connected($group)) {
            return $this->_server[$group]->config();
        }
        return array();
    }
    /**
    * Connects to a database group
    *
    * @param string $group The group to check
    *
    * @return bool True on success, false on failure
    */
    public function connect($group = "default")
    {
        if ($this->connected($group)) {
            return true;
        }
        if (!is_object($this->_server[$group])
            || (!is_a($this->_server[$group], "\HUGnet\interfaces\DBConnection"))
        ) {
            if (!$this->_driverFactory($group)) {
                return false;
            }
        }
        $this->_server[$group]->connect();
        return $this->connected($group);
    }

    /**
    * Returns a database object
    *
    * @param string $group The group to check
    *
    * @return object The database object
    */
    public function &getDBO($group = "default")
    {
        $this->connect($group);
        if ($this->connected($group)) {
            return $this->_server[$group]->getDBO();
        }
        return null;
    }
    /**
    * Disconnects from the database
    *
    * @param string $group The group to check
    *
    * @return null
    */
    public function disconnect($group = "default")
    {
        unset($this->_server[$group]);
    }

    /**
    * Return an array of the groups currently registered
    *
    * @return null
    */
    public function groups()
    {
        return (array)$this->_groups;
    }
    /**
    * Group Exists
    *
    * @param string $group The group to check
    *
    * @return bool True if group exists and connection is made, false otherwise
    */
    public function available($group = "default")
    {
        return $this->connect($group);
    }

}
?>
