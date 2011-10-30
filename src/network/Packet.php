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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\network;
/**
 * This is the packet class.
 *
 * This class stores all of the packet information as integers.  They are returned
 * as strings properly formated.  Everything is internal because this class is
 * basically ment to be used in the following scenerio:
 *
 * A packet comes in.  It is given to this class and the class decodes it.  This
 * class is then asked if the packet was valid, and if it was data is retrieved
 * from this class.
 *
 * Packets should stay in class form for as long as they are used.  They shouldn't
 * be converted into any other form.
 *
 * This class will never know how to send a packet.  That belongs other places.
 * This class is part of a refactoring of PacketContainer which bloated to
 * immense proportions.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
final class Packet
{
    /** This is where in the packet this is */
    const COMMAND = 0;
    /** This is where in the packet this is */
    const TO = 2;
    /** This is where in the packet this is */
    const FROM = 8;
    /** This is where in the packet this is */
    const LENGTH = 14;
    /** This is where in the packet this is */
    const DATA = 16;
    /** This is a preamble byte */
    const PREAMBLE = "5A";
    /** This is a preamble byte */
    const PREAMBLE_BYTES = 3;
    /** This is a preamble byte */
    const PREAMBLE_BYTES_MIN = 2;
    /** This is who the packet is to */
    private $_to;
    /** This is who the packet is from */
    private $_from;
    /** This is the packet command */
    private $_command;
    /** This is the packet data */
    private $_data;
    /** This is the packet checksum  */
    private $_checksum;
    /** This is the polynomial for the CRC  */
    private $_poly = 0xA6;
    /** Extra string at the end of the packet  */
    private $_extra;
    /** This has known types in it */
    private $_commands = array(
        "REPLY" => 0x01,
        "PING" => 0x02,
        "FINDPING" => 0x03,
        "CALIBRATION" => 0x4C,
        "CAL_NEXT" => 0x4D,
        "SETCONFIG" => 0x5B,
        "CONFIG" => 0x5C,
        "SENSORREAD" => 0x55,
        "BAD COMMAND" => 0xFF,
        "READ_E2" => 0x0A,
        "READ_SRAM" => 0x0B,
        "READ_FLASH" => 0x0C,
        "WRITE_E2" => 0x1A,
        "WRITE_SRAM" => 0x1B,
        "WRITE_FLASH" => 0x1C,
        "SET_RTC" => 0x50,
        "READ_RTC" => 0x51,
        "POWERUP" => 0x5E,
        "RECONFIG" => 0x5D,
        "BOREDOM" => 0x5F,
    );

    /**
    * This builds and populates the packet
    *
    * @param mixed $data The data to create the packet with
    */
    private function __construct($data)
    {
        // Input a string if we have one
        if (is_string($data)) {
            $this->_fromString($data);
        } else if (is_array($data)) {
            $this->_fromArray($data);
        }
    }
    /**
    * Creates the object
    *
    * @param mixed $data The array of data to use
    *
    * @return null
    */
    public function &factory($data = array())
    {
        return new Packet($data);
    }
    /**
    * Builds the packet from a string
    *
    * @param string $string The packet string
    *
    * @return null
    */
    private function _fromString($string)
    {
        $string = $this->_cleanPktStr(strtoupper($string));
        $this->command(substr($string, self::COMMAND, 2));
        $this->to(substr($string, self::TO, 6));
        $this->from(substr($string, self::FROM, 6));
        $length = hexdec(substr($string, self::LENGTH, 2)) * 2;
        $this->data(substr($string, self::DATA, $length));
        $this->_setField("_checksum", substr($string, (self::DATA + $length), 2));
        $this->extra(substr($string, (self::DATA + $length + 2)));
    }
    /**
    * Builds the packet from a string
    *
    * @param string $array The packet array
    *
    * @return null
    */
    private function _fromArray($array)
    {
        foreach ($array as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }
        $this->_setField("_checksum", $this->_checksum());
    }
    /**
    * Checks for a given key
    *
    * @return null
    */
    public function __toString()
    {
        return $this->Preamble().$this->_packetStr().$this->Checksum();
    }
    /**
    * Checks for a given key
    *
    * @return null
    */
    private function _packetStr()
    {
        // Command (2 chars)
        $string  = $this->Command();
        // To (6 chars)
        $string .= $this->to();
        // From (6 chars)
        $string .= $this->from();
        // Length (2 chars)
        $string .= $this->length();
        // Data ('Length' chars)
        $string .= $this->data();
        return $string;
    }
    /**
    * Computes the checksum of a packet
    *
    * @return int The checksum
    */
    private function _checksum()
    {
        $string = $this->_packetStr();
        $chksum = 0;
        for ($i = 0; $i < strlen($string); $i+=2) {
            $val     = hexdec(substr($string, $i, 2));
            $chksum ^= $val;
        }
        return $chksum;
    }
    /**
    * Sets and/or returns the from
    *
    * @param string $field The field to set
    * @param mixed  $value The value to set this to.
    *
    * @return null
    */
    private function _setField($field, $value)
    {
        if (is_string($value)) {
            $this->$field = hexdec($value);
        } else if (is_int($value)) {
            $this->$field = $value;
        }
        return $this->$field;
    }
    /**
    * Turns an array into a string
    *
    * @param mixed $value The value to set this to.
    *
    * @return null
    */
    private function _toStr($value)
    {
        $return = "";
        foreach ((array)$value as $val) {
            $return .= sprintf("%02X", ($val & 0xFF));
        }
        return $return;
    }
    /**
    * Turns a string into an array
    *
    * @param string $field The field to set
    * @param mixed  $value The value to set this to.
    *
    * @return null
    */
    private function _setArray($field, $value)
    {
        if (is_string($value)) {
            // This reference is necessary, otherwise I get the following error:
            // Cannot use [] for reading
            $array = &$this->$field;
            $array = array();
            foreach (str_split($value, 2) as $val) {
                $array[] = hexdec($val);
            }
        } else if (is_array($value)) {
            $this->$field = $value;
        }
        return $this->$field;
    }
    /**
    * Checks to see if this packet is valid
    *
    * @return bool True if the packet is valid, false otherwise
    */
    public function isValid()
    {
        return ($this->_checksum == $this->_checksum()) && !empty($this->_command);
    }
    /**
    * Sets and/or returns the command
    *
    * @param mixed $value The value to set this to.
    *
    * @return string (2 chars) Returns the command
    */
    public function command($value = null)
    {
        if (isset($this->_commands[$value])) {
            $value = $this->_commands[$value];
        }
        return sprintf("%02X", $this->_setField("_command", $value));
    }
    /**
    * Sets and/or returns who the packet is to
    *
    * @param mixed $value The value to set this to.
    *
    * @return string (6 chars) Returns the to value
    */
    public function to($value = null)
    {
        return sprintf("%06X", $this->_setField("_to", $value));
    }
    /**
    * Sets and/or returns the from
    *
    * @param mixed $value The value to set this to.
    *
    * @return string Returns the value it is set to
    */
    public function from($value = null)
    {
        return sprintf("%06X", $this->_setField("_from", $value));
    }
    /**
    * Sets and/or returns the from
    *
    * @param mixed $value The value to set this to.
    *
    * @return string Returns the value it is set to
    */
    public function extra($value = null)
    {
        if (is_string($value)) {
            $this->_extra = $value;
        }
        return (string)$this->_extra;
    }
    /**
    * Returns the packet length
    *
    * @return string (2 chars) The packet length
    */
    public function length()
    {
        return sprintf("%02X", count($this->_data));
    }
    /**
    * Returns the packet length
    *
    * @return string (2 chars) The packet checksum
    */
    public function checksum()
    {
        return sprintf("%02X", $this->_checksum());
    }
    /**
    * Checks to see if this packet is valid
    *
    * @param mixed $value The value to set this to.
    * @param bool  $raw   Return the raw data as an array if true
    *
    * @return string|array Returns the data in an array if $raw is true, a string
    *                      otherwise
    */
    public function data($value = null, $raw = false)
    {
        $this->_setArray("_data", $value);
        if ($raw) {
            return $this->_data;
        }
        return $this->_toStr($this->_data);
    }
    /**
    * Checks to see if this packet is valid
    *
    * @param bool $min Return the minimum preamble instead of the normal one
    *
    * @return string Returns the value it is set to
    */
    public function preamble($min = false)
    {
        if ($min) {
            return str_repeat(self::PREAMBLE, self::PREAMBLE_BYTES_MIN);
        }
        return str_repeat(self::PREAMBLE, self::PREAMBLE_BYTES);
    }
    /**
    * Checks to see if this packet is valid
    *
    * @return string Returns the value it is set to
    */
    public function type()
    {
        $key = array_search($this->_command, $this->_commands);
        if (!is_bool($key)) {
            return $key;
        }
        return "UNKNOWN";
    }
    /**
    * Removes the preamble and other junk from the start of a packet string
    *
    * @param string $string The preamble will be removed from this packet string
    *
    * @return null
    */
    private function _cleanPktStr($string)
    {
        // This strips off anything before the preamble
        if (($pkt = stristr($string, $this->preamble(true))) === false) {
            // If there is no preamble present send the string directly through
            $this->extra($string);
            return "";
        }
        // This strips off the preamble.
        while (strtoupper(substr($pkt, 0, 2)) == self::PREAMBLE) {
            $pkt = substr($pkt, 2);
        }
        return $pkt;
    }
    /**
    * Returns the CRC8 of the packet
    *
    * @return byte The total CRC
    */
    public function crc8()
    {
        $string = $this->_packetStr();
        $pkt = str_split($string, 2);
        $crc = 0;
        foreach ($pkt as $value) {
            $this->_crc8byte($crc, hexdec($value));
        }
        return $crc;
    }
    /**
    * Checks to see if this packet is valid
    *
    * @param int &$crc The total CRC so far.  SHould be set to 0 to start
    * @param int $byte The byte we are adding to the crc
    *
    * @return byte The total CRC
    */
    private function _crc8byte(&$crc, $byte)
    {
        $crc = ((int)$crc ^ (int)$byte) & 0xFF;
        for ($bit = 8; $bit > 0; $bit--) {
            if (($crc & 0x80) == 0x80) {
                $crc = ($crc << 1) ^ $this->_poly;
            } else {
                $crc = $crc << 1;
            }
            $crc = $crc & 0xFF;
        }
    }

}


?>
