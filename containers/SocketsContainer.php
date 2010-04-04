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
class SocketsContainer extends HUGnetContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "sockets" => array(),               // The array of server information
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** @var object This is a link to the connected server */
    protected $socket = null;
    /** @var object These are the server groups we know */
    protected $groups = null;
    /** @var object These are the server groups we know */
    protected $driver = null;

    /**
    * Creates the socket class
    *
    * @param array $sockets The sockets to use
    */
    public function __construct($sockets = array())
    {
        if (empty($sockets)) {
            $sockets = array(array());
        }
        foreach ((array)$sockets as $key => $sock) {
            if (isset($sock["GatewayIP"])
                || isset($sock["GatewayPort"])
                || isset($sock["GatewayKey"])
            ) {
                if ($this->findClass("GatewayContainer")) {
                    $this->data["sockets"][$key] =& self::factory(
                        $sock,
                        "GatewayContainer"
                    );
                }
            } else if (isset($sock["dummy"])) {
                if ($this->findClass("DummySocketContainer", "test/stubs")) {
                    $this->data["sockets"][$key] =& self::factory(
                        $sock,
                        "DummySocketContainer"
                    );
                }
            /*
            } else
                if ($this->findClass("PacketLogContainer")) {
                    $this->data["sockets"][$key] =& self::factory(
                        $sock,
                        "PacketLogContainer"
                    );
                }
                */
            }
            if (!isset($this->data["sockets"][$key])) {
                continue;
            }
            // Define this group;
            $this->groups[$this->data["sockets"][$key]->group]
                = $this->data["sockets"][$key]->group;
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
        if (!is_object($this->socket[$group])) {
            return false;
        }
        return $this->socket[$group]->connected();
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
        foreach (array_keys((array)$this->data["sockets"]) as $key) {
            if ($this->data["sockets"][$key]->group !== $group) {
                continue;
            }
            $this->socket[$group] =& $this->data["sockets"][$key];
            if ($this->socket[$group]->connect()) {
                $this->lock(array_keys($this->default));
                return true;
            }

        }
        return false;
    }

    /**
    * Creates a database object
    *
    * @param string $group The group to check
    *
    * @return object HUGnetDBDriver object
    */
    public function &getDriver($group = "default")
    {
        $this->connect($group);
        if ($this->connected($group)) {
            return $this->socket[$group];
        } else {
            return null;
        }
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
        if (!$this->connected($group)) {
            return;
        }
        $this->socket[$group]->disconnect();
        $this->socket[$group] = null;
        $this->unlock(array_keys($this->default));
    }

    /**
    * There should only be a single instance of this class
    *
    * @param array $sockets The sockets to use
    *
    * @return object of type DBServersContainer()
    */
    public function &singleton($sockets = array())
    {
        static $instance;

        if (!is_object($instance)) {
            $class = __CLASS__;
            $instance = new $class($sockets);
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
        foreach (array_keys((array)$this->sockets) as $key) {
            $data[$key] = $this->sockets[$key]->toArray($default);
            if (empty($data[$key])) {
                unset($data[$key]);
            }
        }
        return (array)$data;
    }

}
?>
