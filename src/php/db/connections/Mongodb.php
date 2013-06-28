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
class Mongodb  implements \HUGnet\interfaces\DBConnection
{
    private $_default = array(
        "group"  => "default",       // This is the name of the database group
        "driver" => "mongodb",        // The driver to use
        "host"   => "localhost",     // The server to contact
        "port"   => 27017,            // The port to use
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
    private $_client = null;
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
        $obj = new Mongodb($system, $config);
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
        return is_object($this->_client)
            && (is_a($this->_client, "MongoClient"));
    }
    /**
    * Checks to see if we are connected to a database
    *
    * @return object PDO object, null on failure
    */
    public function driver()
    {
        $this->connect();
        return "mongodb";
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
    * Tries to connect to a database servers
    *
    * @param string $server The server to check
    *
    * @return bool True on success, false on failure
    */
    public function connect()
    {
        $this->_system->fatalError(
            "Mongo Extension Not Found", 
            !class_exists("\MongoClient")
        );
        
        if ($this->connected()) {
            return true;
        }
        $dsn = $this->_getDSN();
        $this->_system->out(
            "Trying ".$dsn,
            3
        );
        try {
            $this->_client = new \MongoClient(
                $dsn,
                array(
                )
            );
            //$this->_server[$group]->postConnect();
        } catch (\MongoConnectionException $e) {
            $this->_system->out(
                "Error (".$e->getCode()."): ".$e->getMessage()."\n",
                2
            );
            // Just to be sure
            $this->disconnect();
            // Return failure
            return false;
        }
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
    private function _getDSN()
    {
        $driver = strtolower($this->_servers[$server]["driver"]);

        $dsn = "mongodb://";
        $sep = "";
        $servers = "";
        $this->_server = null;
        foreach ($this->_servers as $key => $serv) {
            if (is_null($this->_server)) {
                $this->_server = $key;
            }
            $servers .= $sep.$serv["host"].":".$serv["port"];
            $sep = ",";
        }
        if (!empty($this->_servers[$this->_server]["user"])) {
            $dsn .= $this->_servers[$this->_server]["user"].":";
            $dsn .= $this->_servers[$this->_server]["password"]."@";
        }
        $dsn .= $servers;
        $dsn .= "/".$this->_servers[$this->_server]["db"];
        return (string)$dsn;
    }

    /**
    * Returns a database object
    *
    * @return object The database object
    */
    public function &getDBO()
    {
        if ($this->connect()) {
            return $this->_client->selectDB($this->_servers[$this->_server]["db"]);
        }
        return null;
    }
    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function disconnect()
    {
        if (is_object($this->_client)) {
            $this->_client->close();
        }
        unset($this->_server);
        unset($this->_client);
        $this->_server = null;
        $this->_client = null;
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
