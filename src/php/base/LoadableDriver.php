<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\base;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * Base driver class for devices.
 *
 * This class deals with loading the drivers and figuring out what driver needs
 * to be loaded.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class LoadableDriver
{
    /**
    * This is where we store the process.
    */
    private $_iopobject = null;
    /**
    * This is our entry object
    */
    private $_entry = null;
    /**
    * This is the class to use for our entry object.
    */
    protected $entryClass = null;
    /**
    * The location of our tables.
    */
    protected $tableLoc = "";
    /**
    * The offset for getExtra
    */
    protected $offset = 0;

    /**
    * This is where we store our float size information
    */
    private $_floats = array(
        4 => array(
            "bits"  => 32,
            "esize" => 8,
            "ebias" => 127,
            "fsize" => 23,
        ),
    );
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$iopobject The output in question
    * @param int    $offset     The offset to use for getExtra
    *
    * @return null
    */
    protected function __construct(&$iopobject, $offset = 0)
    {
        $this->_iopobject = &$iopobject;
        $this->offset     = (int)$offset;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_iopobject);
    }
    /**
    * This is the destructor
    *
    * @return object
    */
    protected function iopobject()
    {
        return $this->_iopobject;
    }
    /**
    * Checks to see if a piece of data exists
    *
    * @param string $name The name of the property to check
    *
    * @return true if the property exists, false otherwise
    */
    public function present($name)
    {
        return !is_null($this->get($name));
    }
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    public function get($name)
    {
        $ret = null;
        if (isset($this->params[$name])) {
            $ret = $this->params[$name];
        } else if (isset($this->default[$name])) {
            $ret = $this->default[$name];
        }
        if (is_string($ret) 
            && ($name != "extraDefault")   // This would cause an infinite loop
            && (strtolower(substr($ret, 0, 8)) === "getextra")
        ) {
            $key = (int)substr($ret, 8);
            $ret = $this->getExtra($key);
        }
        return $ret;
    }
    /**
    * Returns the name of the class to use for the table entry
    *
    * @return string The name of the class to use
    */
    protected function entryClass()
    {
        $file  = dirname(__FILE__)."/../devices/".$this->tableLoc;
        $file .= "/tables/".$this->entryClass.".php";
        if (file_exists($file)) {
            include_once $file;
        }
        $ret = "\\HUGnet\\devices\\".$this->tableLoc."\\tables\\".$this->entryClass;
        return $ret;
    }
    /**
    * Returns the converted table entry
    *
    * @return bool The table to use
    */
    protected function convertOldEntry()
    {
        return null;
    }
    /**
    * Returns the driver object
    * 
    * @return object The driver requested
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function &entry()
    {
        $entryClass = $this->entryClass();
        if (!is_object($this->_entry)  && class_exists($entryClass)) {
            $table = json_decode(
                (string)$this->iopobject()->table()->get("tableEntry"), true
            );
            if (empty($table) || !is_array($table)) {
                $newTable = $this->convertOldEntry();
                if (is_array($newTable)) {
                    $table = $newTable;
                    $found = false;
                }
            }
            $entry = $entryClass::factory(
                $this, $table
            );
            if (!$found || empty($table)) {
                $this->iopobject()->table()->set("tableEntry", $entry->toArray());
                $this->iopobject()->table()->updateRow();
            }
            $this->_entry = &$entry;
        }
        return $this->_entry;
    }
    /**
    * Returns all of the parameters and defaults in an array
    *
    * @return array of data from the sensor
    */
    public function toArray()
    {
        $return = array();
        $keys = array_merge(array_keys($this->default), array_keys($this->params));
        foreach ($keys as $key) {
            $return[$key] = $this->get($key);
        }
        return $return;
    }
    /**
    * Gets the extra values
    *
    * @param mixed $index The extra index to use
    *
    * @return The extra value (or default if empty)
    */
    public function getExtra($index)
    {
        $index = $this->_extraIndex($index);
        $extra = (array)$this->iopobject()->get("extra");
        $return = $extra[$index + $this->offset];
        if (is_null($return)) {
            $extra = $this->get("extraDefault");
            $return = $extra[$index];
        }
        return $return;
    }
    /**
    * Gets the extra values
    *
    * @param mixed $index The extra index to use
    * @param mixed $value The value to set it to
    *
    * @return The extra value (or default if empty)
    */
    public function setExtra($index, $value)
    {
        $index = $this->_extraIndex($index) + $this->offset;
        $extra = (array)$this->iopobject()->get("extra");
        if (($value === "") || is_null($value)) {
            unset($extra[$index]);
        } else {
            $extraValues = $this->get("extraValues");
            // This checks to see if this is a valid value
            if (!is_array($extraValues[$index]) 
                || isset($extraValues[$index][$value])
            ) {
                $extra[$index] = $value;
            } else if (is_array($extraValues[$index])) {
                // This is a case where we are given the value of the array element,
                // instead of the key.
                $key = array_search($value, $extraValues[$index], true);
                if ($key !== false) {
                    $extra[$index] = $key;
                }
            }
        }
        $this->iopobject()->set("extra", $extra);
        return $extra[$index];
    }
    /**
    * Gets the extra Index
    *
    * @param mixed $index The extra index to use
    *
    * @return The extra value (or default if empty)
    */
    private function _extraIndex($index)
    {
        if (!is_numeric($index)) {
            $extraNames = $this->get("extraNames");
            if (isset($extraNames[$index])) {
                $index = $extraNames[$index];
            }
        }
        return (int)$index;
    }
    /**
    * Returns the driver that should be used for a particular device
    *
    * @return array The array of drivers that will work
    */
    public function getDrivers()
    {
        $ret = (array)$this->arch[$this->iopobject()->device()->get("arch")]
            + (array)$this->arch["all"];
        ksort($ret);
        return $ret;
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string The string to decode
    *
    * @return array
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function decode($string)
    {
        /* Do nothing by default */
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function encode()
    {
        $string  = "";
        return $string;
    }

    /**
    * This builds the string for the levelholder.
    *
    * @param int $val   The value to use
    * @param int $bytes The number of bytes to set
    *
    * @return string The string
    */
    protected function encodeInt($val, $bytes = 4)
    {
        $val = (int)$val;
        for ($i = 0; $i < $bytes; $i++) {
            $str .= sprintf(
                "%02X",
                ($val >> ($i * 8)) & 0xFF
            );
        }
        return $str;

    }
    /**
    * This builds the string for the levelholder.
    *
    * @param string $val    The value to use
    * @param int    $bytes  The number of bytes to set
    * @param bool   $signed If the number is signed or not
    *
    * @return string The string
    */
    protected function decodeInt($val, $bytes = 4, $signed = false)
    {
        $int = 0;
        for ($i = 0; $i < $bytes; $i++) {
            $int += hexdec(substr($val, ($i * 2), 2))<<($i * 8);
        }
        $bits = $bytes * 8;
        $int = (int)($int & (pow(2, $bits) - 1));
        if ($signed) {
            $int = $this->signedInt($int, $bytes);
        }
        return $int;

    }
    /**
    * This builds the string for the levelholder.
    *
    * @param int $val   The value to use
    * @param int $bytes The number of bytes to set
    *
    * @return string The string
    */
    protected function signedInt($val, $bytes = 4)
    {
        $bits = $bytes * 8;
        /* Calculate the top bit */
        $topBit = pow(2, ($bits - 1));
        /* Check to see if the top bit is set */
        if (($val & $topBit) == $topBit) {
            /* This is a negative number */
            $val = -(pow(2, $bits) - $val);
        }
        return $val;

    }
    /**
    * This builds the string for the levelholder.
    *
    * @param float $val   The value to use
    * @param int   $bytes The number of bytes to use
    *
    * @return string The string
    */
    protected function encodeFloat($val, $bytes = 4)
    {
        if (!isset($this->_floats[(int)$bytes])) {
            return $this->encodeInt(null, $bytes);
        }
        if ($val == 0) {
            return $this->encodeInt(0);
        }
        $bits  = $this->_floats[(int)$bytes]["bits"];
        //$esize = $this->_floats[(int)$bytes]["esize"];
        $ebias = $this->_floats[(int)$bytes]["ebias"];
        $fsize = $this->_floats[(int)$bytes]["fsize"];

        $sign = ($val < 0) ? 1 : 0;
        $val = abs($val);
        $exp  = 0;
        // This sections makes it a number between 1 and 2
        for ($i = 0; ($val >= 2) && ($i < 64); $i++) {
            $val /= 2;
            $exp++;
        }
        for ($i = 0; ($val < 1) && ($i < 64); $i++) {
            $val *= 2;
            $exp--;
        }
        $exp += $ebias;
        $int  = round((($val - 1) * pow(2, $fsize)));
        $int  = $int | ($exp << $fsize) | ($sign << ($bits - 1));
        return $this->encodeInt($int);

    }
    /**
    * This builds the string for the levelholder.
    *
    * @param string $val   The value to use
    * @param int    $bytes The number of bytes to use
    *
    * @return string The string
    */
    protected function decodeFloat($val, $bytes = 4)
    {
        if (!isset($this->_floats[(int)$bytes])) {
            return null;
        }
        // First, we need to get the int
        if (is_string($val)) {
            $int = $this->decodeInt($val, $bytes, false);
        } else {
            $int = (int)$val;
        }
        if ($int == 0) {
            return 0.0;
        }
        $bits  = $this->_floats[(int)$bytes]["bits"];
        //$esize = $this->_floats[(int)$bytes]["esize"];
        $ebias = $this->_floats[(int)$bytes]["ebias"];
        $fsize = $this->_floats[(int)$bytes]["fsize"];
        $sign  = ($int & pow(2, $bits - 1)) ? -1 : 1;
        $fract = 1 + ((float)($int & (pow(2, $fsize) - 1)) / (float)pow(2, $fsize));
        $exp   = (($int >> $fsize) & 0xFF) - $ebias;
        $float = (float)$fract * pow(2, $exp) * $sign;
        return $float;

    }
    /**
    * This takes the runs/second and turns it into a priority
    *
    * @param int $value The value to encode
    *
    * @return string The priority, encoded for the device
    */
    protected function encodePriority($value)
    {
        if (($value > 0.5) && ($value < 129)) {
            $value = round(128 / $value);
        } else if (round($value, 2) == 0.50) {
            $value = 255;
        } else {
            $value = 0;
        }
        $string = $this->encodeInt($value, 1);
        return $string;
    }
    /**
    * This decodes the priority from the endoint to runs/second
    *
    * @param string $string The setup string to decode
    *
    * @return Reference to the network object
    */
    protected function decodePriority($string)
    {
        $value = $this->decodeInt($string, 1);
        if (($value > 0) && ($value <= 255)) {
            $value = round(128 / $value, 2);
        } else {
            $value = 129;
        }
        return $value;
    }
}


?>
