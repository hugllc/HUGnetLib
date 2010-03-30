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
require_once dirname(__FILE__)."/../base/HUGnetContainerLinkedList.php";

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
class DBServersContainer extends HUGnetContainerLinkedList
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
            }
        }
    }
    /**
    * Disconnects from the database
    *
    */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
    * Creates a database object
    *
    * @return object PDO object, null on failure
    */
    public function connected()
    {
        return is_object($this->pdo) && (get_class($this->pdo) == "PDO");
    }

    /**
    * Creates a database object
    *
    * @return object PDO object, null on failure
    */
    public function connect()
    {
        if (!$this->connected()) {
            foreach (array_keys((array)$this->data["servers"]) as $key) {
                $this->server =& $this->data["servers"][$key];
                if ($this->_connect()) {
                    $this->lock(array_keys($this->default));
                    return true;
                }
            }
        }
        return false;
    }
    /**
    * Creates a database object
    *
    * @return object PDO object, null on failure
    */
    private function _connect()
    {

        $this->lock(array_keys($this->default));
        $this->vprint("Trying ".$this->server->getDSN(), 3);
        try {
            $this->pdo = new PDO(
                $this->server->getDSN(),
                (string)$this->server->user,
                (string)$this->server->password,
                (array)$this->server->options
            );
        } catch (PDOException $e) {
            self::vprint(
                "Error (".$e->getCode()."): ".$e->getMessage()."\n",
                1,
                $verbose
            );
            // Just to be sure
            $this->disconnect();
            // Return failure
            return false;
        }
        return true;
    }

    /**
    * Creates a database object
    *
    * @return object PDO object, null on failure
    */
    public function &getPDO()
    {
        if (!$this->connected()) {
            $this->connect();
        }
        return $this->pdo;
    }

    /**
    * Disconnects from the database
    *
    * @return object PDO object, null on failure
    */
    public function disconnect()
    {
        $this->server = null;
        $this->pdo = null;
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

}
?>
