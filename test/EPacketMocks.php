<?php
/**
 * Tests the EPacket class
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   PacketStructure
 * @package    HUGnetLibTest
 * @subpackage PacketStructure
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
// Need to make sure this file is not added to the code coverage
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once dirname(__FILE__).'/../EPacket.php';

/**
 * This class is for testing callback
 *
 * @category   PacketStructure
 * @package    HUGnetLibTest
 * @subpackage PacketStructure
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class EPacketTest_CallBack_Class
{
    /**
    * This function is for testing callback
    *
    * @param array $pkt The packet
    *
    * @return null
    */
    public function test($pkt)
    {
        $this->TestVar = $pkt;
    }
}

/**
 * This class overrides epsocket so that we can test EPacket without
 * actually using a socket connection.
 *
 * @category   PacketStructure
 * @package    HUGnetLibTest
 * @subpackage PacketStructure
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class EPacketTXRXMock extends EPacket
{
    /**
    * constructor
    */
    function __construct()
    {

    }
    /**
    * Some Function
    *
    * @param array &$Info      The array with the device information in it
    * @param array $PacketList Array with packet information in it.
    * @param bool  $GetReply   Whether or not to wait for a reply.
    * @param int   $pktTimeout The timeout value to use
    *
    * @return null
    */
    function sendPacket(&$Info, $PacketList, $GetReply=true, $pktTimeout = null)
    {
        return array(
            "Info" => $Info,
            "PacketList" => $PacketList,
            "GetReply" => $GetReply,
            "pktTimeout" => $pktTimeout,
        );
    }
    /**
    * Some Function
    *
    * @param int $socket  The socket to send it out of.  0 is the default.
    * @param int $timeout Timeout for waiting.  Default is used if timeout == 0
    *
    * @return null
    */
    public function recvPacket($socket, $timeout = 0)
    {
        return array(
            "socket" => $socket,
            "timeout" => $timeout,
        );
    }
}
/**
 * This class overrides epsocket so that we can test EPacket without
 * actually using a socket connection.
 *
 * @category   PacketStructure
 * @package    HUGnetLibTest
 * @subpackage PacketStructure
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class EpSocketMock extends EpSocket
{
    /** Socket to use */
    var $socket = false;
    /** Current socket index */
    var $index = 0;

    /**
    * Sets replies for packets received
    *
    * @param string $data  The data we will receive
    * @param string $reply The data to return
    *
    * @return null
    */
    public function setReply($data, $reply)
    {
        if (is_array($data)) {
            $data = array_change_key_case($data, CASE_LOWER);
        }
        $this->reply[serialize($data)] = $reply;
    }

    /**
    * Connects to the server
    *
    * @param array $config Infomation about the connection
    *
    * @return bool true if the connection is good, false otherwise
    *
    * @see epsocket::Connect()
    */
    public function connect($config = array())
    {
        if (!empty($config["GatewayIP"])) {
            $this->Server = $config["GatewayIP"];
        }
        if (!empty($config["GatewayPort"])) {
            $this->Port = $config["GatewayPort"];
        }
        if (!empty($this->Server) && !empty($this->Port)) {
            $this->socket = true;
            return true;
        } else {
            $this->Errno = -1;
            $this->Error = "No server specified";
            return false;
        }

    }
    /**
    * Checks to make sure that all we are connected to the server
    *
    * This routine only checks the connection.  It does nothing else.  If you want to
    * have the script automatically connect if it is not connected already then use
    * epsocket::Connect().
    *
    * @return bool true if the connection is good, false otherwise
    */
    function checkConnect()
    {
        return true;
    }

    /**
    * Closes the socket connection
    *
    * @return null
    */
    function close()
    {
        $this->socket = false;
    }

    /**
    * Sends out a packet
    *
    * @param array $packet the packet to send out
    *
    * @return bool false on failure, true on success
    */
    function sendPacket($packet)
    {
        $this->lastPacket = serialize($packet["packet"]);
        return true;

    }
    /**
    * Receives a packet from the socket interface
    *
    * @param int $timeout Timeout for waiting.  Default is used if timeout == 0
    *
    * @return bool false on failure, the Packet array on success
    */
    function recvPacket($timeout=0)
    {
        if (isset($this->reply[$this->lastPacket])) {
            return $this->reply[$this->lastPacket];
        }
        return false;
    }

    /**
    * Constructor
    *
    * @param array $config Infomation about the connection
    *
    * @return null
    */
    function __construct($config = array())
    {
        $config["verbose"] = false;
        $this->Connect();
    }
}

/**
 * This function is for testing callback
 *
 * @param array $pkt The packet array
 *
 * @return null
 *
 */
function EPacketTest_CallBack_function($pkt)
{
    $_SESSION['EPacketTest_CallBack_Function'] = $pkt;
}

?>
