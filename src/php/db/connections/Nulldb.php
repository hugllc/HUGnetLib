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
class Nulldb  implements \HUGnet\interfaces\DBConnection
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
    * @param array  $config  The config to use
    *
    * @return null
    */
    public static function &factory(&$system, $config)
    {
        $obj = new Nulldb($system, $config);
        return $obj;
    }

    /**
    * Destroys the object
    *
    */
    public function __destruct()
    {
        $this->disconnect();
        unset($this->_system);
        unset($this->_servers);
    }

    /**
    * Checks to see if we are connected to a database
    *
    * @return object PDO object, null on failure
    */
    public function connected()
    {
        return true;
    }
    /**
    * Checks to see if we are connected to a database
    *
    * @return object PDO object, null on failure
    */
    public function driver()
    {
        $this->connect();
        return "nulldb";
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
    * @return bool True on success, false on failure
    */
    public function connect()
    {
        return true;
    }
    /**
    * Returns a database object
    *
    * @return object The database object
    */
    public function &getDBO()
    {
        return null;
    }
    /**
    * Disconnects from the database
    *
    * @return null
    */
    public function disconnect()
    {
    }

    /**
    * Group Exists
    *
    * @return bool True if group exists and connection is made, false otherwise
    */
    public function available()
    {
        return true;
    }
}
?>
