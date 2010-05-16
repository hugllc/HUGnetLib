<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetClass.php";
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
require_once dirname(__FILE__)."/../interfaces/ConnectionManager.php";


/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DBServersContainer extends HUGnetContainer implements ConnectionManager
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "servers" => array(),               // The array of server information
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** @var object This is where we store our database connection */
    protected $pdo = null;
    /** @var object This is a link to the connected server */
    protected $server = null;
    /** @var object These are the server groups we know */
    protected $groups = null;
    /** @var object These are the server groups we know */
    protected $driver = null;

    /**
    * Disconnects from the database
    *
    * @param array $servers The servers to use
    */
    public function __construct($servers = array())
    {
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
        }
    }
    /**
    * Disconnects from the database
    *
    */
    public function __destruct()
    {
        foreach ((array)$this->groups as $group) {
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
        return is_object($this->pdo[$group])
            && (get_class($this->pdo[$group]) == "PDO");
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
        foreach (array_keys((array)$this->data["servers"]) as $key) {
            if ($this->data["servers"][$key]->group !== $group) {
                continue;
            }
            $this->server[$group] =& $this->data["servers"][$key];
            if ($this->_connect($group)) {
                $this->lock(array_keys($this->default));
                return true;
            }

        }
        return false;
    }
    /**
    * Tries to connect to a database servers
    *
    * @param string $group The group to check
    *
    * @return bool True on success, false on failure
    */
    private function _connect($group = "default")
    {

        $this->lock(array_keys($this->default));
        $this->vprint("Trying ".$this->server[$group]->getDSN(), 3);
        try {
            $this->pdo[$group] = new PDO(
                $this->server[$group]->getDSN(),
                (string)$this->server[$group]->user,
                (string)$this->server[$group]->password,
                (array)$this->server[$group]->options
            );
        } catch (PDOException $e) {
            self::vprint(
                "Error (".$e->getCode()."): ".$e->getMessage()."\n",
                1
            );
            // Just to be sure
            $this->disconnect($group);
            // Return failure
            return false;
        }
        $this->vprint("Connected to ".$this->server[$group]->getDSN(), 3);
        return true;
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
        return $this->pdo[$group];
    }
    /**
    * Creates a database object
    *
    * @param string &$table Table object to attach to it
    * @param string $group  The group to check
    *
    * @return object HUGnetDBDriver object
    */
    public function &getDriver(&$table, $group = "default")
    {
        if ($this->connect($group)) {
            $driverName = ucfirst($this->server[$group]->driver."Driver");
            if (self::findClass($driverName, "/plugins/database/")) {
                return new $driverName($table, $this->pdo[$group]);
            }
            // @codeCoverageIgnoreStart
            // It thinks this line won't run.  I don't know why.
        }
        // @codeCoverageIgnoreEnd
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
        $this->driver[$group] = null;
        $this->server[$group] = null;
        $this->pdo[$group] = null;
        $this->unlock(array_keys($this->default));
    }

    /**
    * There should only be a single instance of this class
    *
    * @param array $servers The servers to use
    *
    * @return object of type DBServersContainer()
    */
    public function &singleton($servers = array())
    {
        static $instance;

        if (!is_object($instance)) {
            $class = __CLASS__;
            $instance = new $class($servers);
        }
        return $instance;
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toArray($default = false)
    {
        foreach (array_keys($this->servers) as $key) {
            $data[$key] = $this->servers[$key]->toArray($default);
            if (empty($data[$key])) {
                unset($data[$key]);
            }
        }
        return (array)$data;
    }
    /**
    * Return an array of the groups currently registered
    *
    * @return null
    */
    public function groups()
    {
        return (array)$this->groups;
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
