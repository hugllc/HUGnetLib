<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\network\packets;
/** This is our interface */
require_once dirname(__FILE__)."/PacketInterface.php";

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
 * This class has a lot of methods, but they are very simple.  I could make them
 * more complex and have less methods.  However, I think I will leave this class
 * as it is and just supress the warnings.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Network
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 * @SuppressWarnings(PHPMD.ShortMethodName)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
final class PacketADuC implements PacketInterface
{
    /** This is where in the packet this is */
    const LENGTH = 0;
    /** This is where in the packet this is */
    const COMMAND = 2;
    /** This is where in the packet this is */
    const ADDRESS = 4;
    /** This is where in the packet this is */
    const DATA = 12;
    /** This is a preamble byte */
    const PREAMBLE = "070E";
    /** This is the packet command */
    private $_command;
    /** This is the address of the data operation */
    private $_address;
    /** This is the packet data */
    private $_data;
    /** This is the packet checksum  */
    private $_checksum;
    /** Extra string at the end of the packet  */
    private $_extra;
    /** The interface associated with this packet  */
    private $_iface;
    /** This is where we keep our reply  */
    private $_reply = null;
    /** This is says if we got a whole packet. Default to true */
    private $_whole = true;
    /**
    * This has known types in it
    *
    * These should not change.  There are other places in the code that these
    * are used.  If they change then things could break.
    *
    * These will be added to by the code.  That is why they are static.
    */
    private static $_commands = array(
        "ERASEPAGE" => 0x45,
        "WRITE" => 0x57,
        "VERIFY" => 0x56,
        "PROTECT" => 0x50,
        "RUN" => 0x52,
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
    static public function &factory($data = array())
    {
        if (is_a($data, "\\HUGnet\\network\\packets\\PacketADuC")) {
            return $data;
        }
        return new PacketADuC($data);
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
        $length = (hexdec(substr($string, self::LENGTH, 2)) * 2) - 10;
        $this->command(substr($string, self::COMMAND, 2));
        $this->address(substr($string, self::ADDRESS, 8));
        $this->data(substr($string, self::DATA, $length));
        $this->_setField("_checksum", substr($string, (self::DATA + $length), 2));
        $this->_whole = strlen($string) >= (self::DATA + $length + 2);
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
        // Length (2 chars)
        $string = $this->length();
        // Command (2 chars)
        $string .= $this->command();
        // To (8 chars)
        $string .= $this->address();
        // Data ('Length - 5' chars)
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
            $chksum += $val;
            $chksum &= 0xFF;
        }
        return (0 - $chksum) & 0xFF;
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
        if (is_string($value) && (strlen($value) > 0)) {
            // This reference is necessary, otherwise I get the following error:
            // Cannot use [] for reading
            $this->$field = array();
            $array = &$this->$field;
            foreach (str_split($value, 2) as $val) {
                $array[] = hexdec($val);
            }
        } else if (is_array($value)) {
            $this->$field = $value;
        }
        return $this->$field;
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
        while (strtoupper(substr($pkt, 0, 4)) == self::PREAMBLE) {
            $pkt = substr($pkt, 4);
        }
        return $pkt;
    }
    /**
    * Checks to see if this packet is valid
    *
    * @return bool True if the packet is valid, false otherwise
    */
    public function isValid()
    {
        if (!$this->_whole) {
            return null;
        }
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
        if (isset(self::$_commands[$value])) {
            $value = self::$_commands[$value];
        }
        return sprintf("%02X", $this->_setField("_command", $value));
    }
    /**
    * Sets and/or returns who the packet is to
    *
    * @param mixed $value The value to set this to.
    *
    * @return string (8 chars) Returns the to value
    */
    public function address($value = null)
    {
        return sprintf("%08X", $this->_setField("_to", $value));
    }
    /**
    * Sets and/or returns the interface associated with this pacekt
    *
    * @param mixed $value The value to set this to.
    *
    * @return string (8 chars) Returns the to value
    */
    public function iface($value = null)
    {
        if (is_string($value)) {
            $this->_iface = trim($value);
        }
        return $this->_iface;
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
        return sprintf("%02X", count($this->_data) + 5);
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
    * Returns the packet reply data if there is any
    *
    * @param mixed $value The value to set this to.
    * @param bool  $raw   Return the raw data as an array if true
    *
    * @return null, true or false.  null if no return yet, true if positive ack,
    *                       false if negative ack
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function reply($value = null, $raw = false)
    {
        if (is_string($value)) {
            $value = hexdec($value);
        }
        if ($value == 0x06) {
            $this->_reply = true;
        } else if ($value == 0x07) {
            $this->_reply = false;
        }
        return $this->_reply;
    }
    /**
    * Returns the packet data
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
    * Returns the packet preamble
    *
    * @param bool $min Return the minimum preamble instead of the normal one
    *
    * @return string Returns the value it is set to
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function preamble($min = false)
    {
        return self::PREAMBLE;
    }
    /**
    * Return the packet type
    *
    * @return string Returns the value it is set to
    */
    public function type()
    {
        $key = array_search($this->_command, self::$_commands);
        if (!is_bool($key)) {
            return $key;
        }
        return "UNKNOWN";
    }
    /**
    * Return a modified configuration array
    *
    * @param array $config The configuration array to start with
    *
    * @return array The modified confiruation array
    */
    public function config($config = array())
    {
        $config["block"] = true;
        $config["baud"]  = 38400;
        $config["tries"] = 1;
        $config["find"] = false;
        return $config;
    }
}


?>
