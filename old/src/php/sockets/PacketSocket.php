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
 * @subpackage Sockets
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetClass.php";
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
require_once dirname(__FILE__)."/../tables/PacketSocketTable.php";
require_once dirname(__FILE__)."/../interfaces/HUGnetSocketInterface.php";
//require_once dirname(__FILE__)."/../devInfo.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sockets
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PacketSocket extends HUGnetContainer implements HUGnetSocketInterface
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "dbGroup" => "default",             // The database group this is in
        "group" => "default",               // The gateway group this is in
        "Timeout" => 10,                    // This is the timeout value
        "readIndex" => 0,                   // The index to start reading at
        "DeviceID" => "FD0020",             // This is our device ID
        "senderID" => 0,                    // Our ID as a sender
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** @var object This is our configuration interface */
    protected $myConfig = null;
    /** @var object This is our table for doing things. */
    protected $myTable = null;
    /** @var int The error number.  0 if no error occurred */
    protected $Errno = 0;
    /** @var string The error string */
    protected $Error = "";

    /**
    * Builds the class
    *
    * @param array $data The data to build the class with
    *
    * @return null
    */
    public function __construct($data = array())
    {
        // This forces it to always be present, even if the data gets cleared
        $this->default["DeviceID"] = PacketContainer::tempDeviceID();
        $this->clearData();
        parent::__construct($data);
        $this->myConfig = &ConfigContainer::singleton();
        $this->senderID = mt_rand(1, 24777216);
    }
    /**
    * Creates a database object
    *
    * @return bool true on success, false on failure
    */
    public function connected()
    {
        return is_object($this->myTable);
    }

    /**
    * Creates a socket connection to the gateway
    *
    * @return bool true on success, false on failure
    */
    public function connect()
    {
        if (!$this->connected()) {
            // Set the verbosity
            $this->verbose($this->myConfig->verbose);
            $config = array("group" => $this->dbGroup);
            $this->myTable = new PacketSocketTable($config, $this->dbGroup);
            $this->myTable->verbose($this->verbose);
            $this->myTable->senderID = $this->senderID;
            $this->myTable->create();
        }
        return $this->connected();
    }

    /**
    * This does nothing here.  There is no reason to disconnect
    *
    * @return object PDO object, null on failure
    */
    public function disconnect()
    {
        return $this->myTable = null;
    }

    /**
    * Read a packet from the database
    *
    * @return int Read bytes on success, false on failure
    */
    private function &_read()
    {
        $ret = &$this->myTable->getNextPacket();
        if (($ret === false) && ($this->Timeout >= 0)) {
            usleep(200000);
        }
        return $ret;
    }

    /**
    * Sends out a packet
    *
    * @param PacketContainer &$pkt The packet to send out
    *
    * @return bool true on success, false on failure
    */
    function sendPkt(PacketContainer &$pkt)
    {
        $this->connect();
        $this->myTable->clearData();
        $this->myTable->fromPacket($pkt);
        return (bool)$this->myTable->insertRow(true);

    }
    /**
    * Waits for a reply packet for the packet given
    *
    * @param PacketContainer &$pkt The packet to send out
    *
    * @return bool true on success, false on failure
    */
    public function recvPkt(PacketContainer &$pkt)
    {
        $this->connect();
        $this->myTable->deleteOld();
        $timeout = time() + $pkt->Timeout;
        $newPkt = new PacketContainer();
        do {
            $packet = &$this->_read();
            $newPkt->clearData();
            $newPkt->fromAny($packet);
            $ret = $pkt->recv($newPkt->toPktString());
        } while (($ret === false) && ($timeout > time()));
        return (bool)$ret;
    }
    /**
    * function to set DeviceID
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setDeviceID($value)
    {
        if (is_int($value)) {
            $value = dechex($value);
        }
        $this->data["DeviceID"] = self::stringSize($value, 6);
    }
}
?>
