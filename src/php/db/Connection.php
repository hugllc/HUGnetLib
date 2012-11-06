<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Connection  implements \ConnectionManager
{
    private $_default = array(
        "group"  => "default",       // This is the name of the database group
        "driver" => "sqlite",        // The driver to use
        "host"   => "localhost",     // The server to contact
        "port"   => 3306,            // The port to use
        "db"     => "HUGnet",        // The database to use
        "socket" => "",              // Unix socket to use
        "user"   => "",              // Username to log in as
        "password" => "",            // Password to use
        "options" => array(),        // Options to use
        "file"    => ":memory:",     // The file for sqlite
        "filePerm" => 0644,          // Permissions on the sqlite file
    );
    /** @var array This is where our system object is kept */
    private $_system = null;
    /** @var array This is where the server information is stored */
    private $_servers = array();

    /** @var object This is where we store our database connection */
    private $_pdo = null;
    /** @var object This is a link to the connected server */
    private $_server = null;
    /** @var object These are the server groups we know */
    private $_groups = null;

    /**
    * Creates the object
    *
    * @param object &$system The system object to use
    */
    private function __construct(&$system)
    {
        $this->_system = &$system;
        $servers = (array)$system->get("servers");
        //$this->_servers["default"] = $this->_default;
        foreach ($servers as $key => $val) {
            $this->_servers[$key] = array_merge($this->_default, $val);
            $group = $this->_servers[$key]["group"];
            $this->_groups[$group] = $group;
        }
        if (empty($this->_servers)) {
            $this->_servers["default"] = $this->_default;
        }
        /*
        if ($this->findClass("DBServerContainer")) {
            if (empty($servers)) {
                $servers = array(array());
            }
            foreach ((array)$servers as $key => $serv) {
                $this->data["servers"][$key] =& self::factory(
                    $serv,
                    "DBServerContainer"
                );
                // Define this group;
                $this->groups[$this->data["servers"][$key]->group]
                    = $this->data["servers"][$key]->group;
            }
        }*/
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
        $obj = new Connection($system);
        return $obj;
    }

    /**
    * Destroys the object
    *
    */
    public function __destruct()
    {
        foreach (array_keys((array)$this->_pdo) as $group) {
            $this->disconnect($group);
        }
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
        return is_object($this->_pdo[$group])
            && (is_a($this->_pdo[$group], "PDO"));
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
        $this->connect();
        $ret = strtolower($this->_servers[$this->_server[$group]]["driver"]);
        if (empty($ret)) {
            $ret = "sqlite";
        }
        return $ret;
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
        return $this->_servers[$this->_server[$group]];
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
        foreach (array_keys($this->_servers) as $key) {
            if ($this->_servers[$key]["group"] !== $group) {
                continue;
            }
            if ($this->_connect($key)) {
                return true;
            }

        }
        return false;
    }
    /**
    * Tries to connect to a database servers
    *
    * @param string $server The server to check
    *
    * @return bool True on success, false on failure
    */
    private function _connect($server = "default")
    {
        $dsn = $this->_getDSN($server);
        $group = $this->_servers[$server]["group"];
        $this->_system->out(
            "Trying ".$dsn,
            3
        );
        try {
            $this->_pdo[$group] = new \PDO(
                $dsn,
                (string)$this->_servers[$server]["user"],
                (string)$this->_servers[$server]["password"],
                (array)$this->_servers[$server]["options"]
            );
            //$this->_server[$group]->postConnect();
        } catch (\PDOException $e) {
            $this->_system->out(
                "Error (".$e->getCode()."): ".$e->getMessage()."\n",
                2
            );
            // Just to be sure
            $this->disconnect($group);
            // Return failure
            return false;
        }
        $this->_server[$group] = $server;
        $this->_system->out(
            "Connected to ".$dsn,
            3
        );
        return true;
    }
    /**
    * creates a dsn for the PDO stuff.  The DSNs apper in the $servers array
    *
    * @param string $server The server to check
    *
    * @return null
    */
    private function _getDSN($server = "default")
    {
        $driver = strtolower($this->_servers[$server]["driver"]);

        if ($driver == "mysql") {
            $dsn  = $driver.":";
            if (!empty($this->_servers[$server]["socket"])) {
                $dsn .= "unix_socket=".$this->_servers[$server]["socket"].";";
            } else {
                $dsn .= "host=".$this->_servers[$server]["host"].";";
                $dsn .= "port=".$this->_servers[$server]["port"].";";
            }
            $dsn .= "dbname=".$this->_servers[$server]["db"];
        } else {
            $this->_servers[$server]["driver"] = "sqlite";
            if (empty($this->_servers[$server]["file"])) {
                $this->_servers[$server]["file"] = $this->_default["file"];
            }
            $dsn = "sqlite:".$this->_servers[$server]["file"];
        }
        return (string)$dsn;
    }

    /**
    * Returns a PDO object
    *
    * @param string $group The group to check
    *
    * @return object PDO object, null on failure
    */
    public function &getPDO($group = "default")
    {
        $this->connect($group);
        return $this->_pdo[$group];
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
        unset($this->_pdo[$group]);
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
