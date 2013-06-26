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
namespace HUGnet\db\connections;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is for the base class */
require_once dirname(__FILE__)."/../../interfaces/DBConnection.php";


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
class PDO  implements \HUGnet\interfaces\DBConnection
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

    /**
    * Creates the object
    *
    * @param object &$system The system object to use
    * @param array  $config  The config to use
    */
    private function __construct(&$system, $config)
    {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $this->_system = &$system;
        foreach ((array)$config as $key => $val) {
            $this->_servers[$key] = array_merge($this->_default, $val);
        }
        if (empty($this->_servers)) {
            $this->_servers["default"] = $this->_default;
        }
    }
    /**
    * This function creates the system.
    *
    * @param object &$system The system object to use
    *
    * @return null
    */
    public static function &factory(&$system, $config)
    {
        $obj = new PDO($system, $config);
        return $obj;
    }

    /**
    * Destroys the object
    *
    */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
    * Checks to see if we are connected to a database
    *
    * @return object PDO object, null on failure
    */
    public function connected()
    {
        return is_object($this->_pdo)
            && (is_a($this->_pdo, "PDO"));
    }
    /**
    * Checks to see if we are connected to a database
    *
    * @return object PDO object, null on failure
    */
    public function driver()
    {
        $this->connect();
        $ret = strtolower($this->_servers[$this->_server]["driver"]);
        if (empty($ret)) {
            $ret = "sqlite";
        }
        return $ret;
    }
    /**
    * Tries to connect to a database servers
    *
    * @return bool True on success, false on failure
    */
    public function config()
    {
        return $this->_servers[$this->_server];
    }
    /**
    * Connects to a database group
    *
    * @return bool True on success, false on failure
    */
    public function connect()
    {
        if ($this->connected()) {
            return true;
        }
        foreach (array_keys($this->_servers) as $key) {
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
        $this->_system->out(
            "Trying ".$dsn,
            3
        );
        try {
            $this->_pdo = new \PDO(
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
            $this->disconnect();
            // Return failure
            return false;
        }
        $this->_server = $server;
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
    * Returns a database object
    *
    * @return object The database object
    */
    public function &getDBO()
    {
        $this->connect();
        return $this->_pdo;
    }
    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function disconnect()
    {
        unset($this->_server);
        unset($this->_pdo);
        $this->_server = null;
        $this->_pdo = null;
    }

    /**
    * Group Exists
    *
    * @return bool True if group exists and connection is made, false otherwise
    */
    public function available()
    {
        return $this->connect();
    }
}
?>
