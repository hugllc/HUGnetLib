<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetClass.php";
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
require_once dirname(__FILE__)."/../interfaces/ConnectionManager.php";
require_once dirname(__FILE__)."/PacketContainer.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class SocketsContainer extends HUGnetContainer implements ConnectionManager
{
    /** This is the maximum our SN can be */
    const MAX_SN = 0xFEFFFF;
    /** This is the minimum our SN can be */
    const MIN_SN = 0xFE0000;

    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "sockets" => array(),               // The array of server information
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** @var object This is a link to the connected server */
    public $socket = null;
    /** @var object These are the server groups we know */
    protected $groups = null;
    /** @var object These are the server groups we know */
    protected $driver = null;
    /** @var string This is the last deviceID that we found */
    protected $lastDeviceID = array();
    /** @var int How long to wait for the Packets.  0 means the packet default */
    public $PacketTimeout = 0;

    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($array)
    {
        $this->clearData();
        foreach ((array)$array as $key => $sock) {
            if (isset($sock["GatewayIP"])
                || isset($sock["GatewayPort"])
                || isset($sock["GatewayKey"])
            ) {
                if ($this->findClass("GatewaySocket", "sockets")) {
                    $this->sockets[$key] =& self::factory(
                        $sock,
                        "GatewaySocket"
                    );
                }
            } else if (isset($sock["dummy"])) {
                if ($this->findClass("DummySocketContainer", "test/stubs")) {
                    $this->sockets[$key] =& self::factory(
                        $sock,
                        "DummySocketContainer"
                    );
                }
            } else {
                if ($this->findClass("PacketSocket", "sockets")) {
                    $this->sockets[$key] =& self::factory(
                        $sock,
                        "PacketSocket"
                    );
                }
            }
            if (isset($this->sockets[$key])) {
                // Define this group;
                $this->groups[$this->sockets[$key]->group]
                    = $this->sockets[$key]->group;
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
        foreach (array_keys((array)$this->sockets) as $key) {
            if ($this->sockets[$key]->group !== $group) {
                continue;
            }
            $this->socket[$group] =& $this->sockets[$key];
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
    * @return object SocketInterface object
    */
    public function &getSocket($group = "default")
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
    * Finds a deviceID that we can use
    *
    * @param array $groups array of groups to check
    *
    * @return null
    */
    public function deviceID($groups = array())
    {
        if (empty($groups)) {
            // If we get no groups, do all
            $groups = $this->groups();
        }
        // Find an ID to use
        do {
            $index = mt_rand(self::MIN_SN, self::MAX_SN);
            $devId = $this->_checkID($index, $groups);
        } while ($devId === false);
        $this->forceDeviceID($devId, $groups);
        return $devId;
    }
    /**
    * Forcably sets the DeviceID
    *
    * @param string $devId  The DeviceID to use
    * @param array  $groups The array of groups to set
    *
    * @return null
    */
    public function forceDeviceID($devId, $groups = array())
    {
        if (empty($groups) || !is_array($groups)) {
            $groups = (array)$this->groups;
        }
        // Set all of the IDs
        foreach ($groups as $group) {
            $this->lastDeviceID[$group] = $devId;
            if ($this->connect($group)) {
                $this->socket[$group]->DeviceID = $devId;
            }
        }
    }
    /**
    * Finds a deviceID that we can use
    *
    * @param int   $devId  The ID to check
    * @param array $groups The array of groups to check
    *
    * @return null
    */
    private function _checkID($devId, $groups)
    {
        $pkt = new PacketContainer(
            array(
                "Command" => PacketContainer::COMMAND_FINDECHOREQUEST,
                "To" => $devId,
                "GetReply" => true,
                "Retries" => 2,
                "Timeout" => $this->PacketTimeout,
            )
        );
        self::vprint("Checking ".$pkt->To, HUGnetClass::VPRINT_NORMAL);
        foreach ($groups as $group) {
            if (!$this->connect($group)) {
                continue;
            }
            $pkt->group = $group;
            $pkt->send();
            if (is_object($pkt->Reply)) {
                // We got a reply so this id exists.  return false.
                // @codeCoverageIgnoreStart
                // Can't get here, as we can't predict where the start is
                return false;
                // @codeCoverageIgnoreEnd
            }
        }
        // Return the good ID
        return $pkt->To;
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
