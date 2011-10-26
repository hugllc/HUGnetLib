<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/**
 * Base class for all other classes
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Packet extends \ArrayObject
{
    /** This is a preamble byte */
    const PREAMBLE = "5A";
    /** This is a preamble byte */
    const PREAMBLE_BYTES = 3;

    /**
    * Creates the object
    *
    * @param mixed $data The array of data to use
    *
    * @return null
    */
    public function &factory($data = array())
    {
        // Make sure our input is clean
        if (is_string($data) || (!is_object($data) && !is_array($data))) {
            $later = $data;
            $data = array();
        }
        // Create the new packet
        $pkt = new Packet(
            $data, \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS
        );
        // Input a string if we have one
        if (is_string($later)) {
            $pkt->fromString($later);
        }
        // Return the packet
        return $pkt;

    }
    /**
    * Checks for a given key
    *
    * @return null
    */
    public function __tostring()
    {
        $string .= $this->_packetStr();
        // Add the checksum (2 chars) to the return
        $string .= $this->_checksum($string);
        return str_repeat(self::PREAMBLE, self::PREAMBLE_BYTES).$string;
    }
    /**
    * Checks for a given key
    *
    * @return null
    */
    private function _packetStr()
    {
        // Command (2 chars)
        $string  = sprintf("%02X", hexdec($this->Command));
        // To (6 chars)
        $string .= sprintf("%06X", hexdec($this->To));
        // From (6 chars)
        $string .= sprintf("%06X", hexdec($this->From));
        // Length (2 chars)
        $string .= sprintf("%02X", (strlen($this->Data)/2));
        // Data ('Length' chars)
        $string .= $this->Data;
        return $string;
    }
    /**
    * Checks for a given key
    *
    * @param string $string The packet string
    *
    * @return null
    */
    public function fromString($string)
    {
        while (strtoupper(substr($string, 0, 2)) == self::PREAMBLE) {
            $string = substr($string, 2);
        }
        $string         = strtoupper($string);
        $this->Command  = substr($string, 0, 2);
        $this->To       = substr($string, 2, 6);
        $this->From     = substr($string, 8, 6);
        $this->Length   = (int) hexdec(substr($string, 14, 2));
        $this->Data     = substr($string, 16, ($this->Length*2));
        $this->Checksum = substr($string, (16 + ($this->Length*2)), 2);
    }
    /**
    * Computes the checksum of a packet
    *
    * @param string $string The raw packet string
    *
    * @return string The checksum
    */
    private function _checksum($string)
    {
        $chksum = 0;
        for ($i = 0; $i < strlen($string); $i+=2) {
            $val     = hexdec(substr($string, $i, 2));
            $chksum ^= $val;
        }
        return sprintf("%02X", $chksum);
    }
    /**
    * Computes the checksum of a packet
    *
    * @param string $string The raw packet string
    *
    * @return string The checksum
    */
    public function valid()
    {
        return $this->Checksum == $this->_checksum($this->_packetStr());
    }
}


?>
