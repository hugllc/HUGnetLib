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
interface HUGnetPacketInterface
{
    /**
    * Sets the socket to use
    *
    * @param string $group The socket group to use
    *
    * @return null
    */
    public function socket($group="default");
    /**
    * Looks for a packet in a string.
    *
    * This is meant to be call with every byte received.  The incoming byte should
    * be appended onto the string.  This routine will take care of removing
    * the portion of string that it turns into packets.
    *
    * @param string &$string The raw packet string to check
    *
    * @return bool true on success, false on failure
    */
    public function recv(&$string);
    /**
    * Sends a packet out
    *
    * This function will wait for a reply if "GetReply" is true.  It will also
    * try to send the packet out the number of times in "Retries" in the case
    * of failure.
    *
    * @param array $data The data to build the class with if called statically
    *
    * @return PacketContainer object on success, null
    */
    public function send($data = array());
    /**
    * Checks to see if the contained packet is an unsolicited
    *
    * @return bool true if it is unsolicited, false otherwise
    */
    public function unsolicited();

}
?>
