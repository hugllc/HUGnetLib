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
    *
    * @return null
    */
    protected function __construct(&$iopobject)
    {
        $this->_iopobject = &$iopobject;
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
        if (is_string($ret) && (strtolower(substr($ret, 0, 8)) === "getextra")) {
            $key = (int)substr($ret, 8);
            $ret = $this->getExtra($key);
        }
        return $ret;
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
    * @param int $index The extra index to use
    *
    * @return The extra value (or default if empty)
    */
    public function getExtra($index)
    {
        $extra = (array)$this->iopobject()->get("extra");
        if (!isset($extra[$index])) {
            $extra = $this->get("extraDefault");
        }
        return $extra[$index];
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
            /* Calculate the top bit */
            $topBit = pow(2, ($bits - 1));
            /* Check to see if the top bit is set */
            if (($int & $topBit) == $topBit) {
                /* This is a negative number */
                $int = -(pow(2, $bits) - $int);
            }

        }
        return $int;

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
        $bits  = $this->_floats[(int)$bytes]["bits"];
        $esize = $this->_floats[(int)$bytes]["esize"];
        $ebias = $this->_floats[(int)$bytes]["ebias"];
        $fsize = $this->_floats[(int)$bytes]["fsize"];

        $sign = ($val < 0) ? 1 : 0;
        $val = abs($val);
        $exp  = 0;
        // This sections makes it a number between 1 and 2
        while ($val >= 2) {
            $val /= 2;
            $exp++;
        }
        while ($val < 1) {
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
        $int   = $this->decodeInt($val, $bytes, false);
        $bits  = $this->_floats[(int)$bytes]["bits"];
        $esize = $this->_floats[(int)$bytes]["esize"];
        $ebias = $this->_floats[(int)$bytes]["ebias"];
        $fsize = $this->_floats[(int)$bytes]["fsize"];
        $sign  = ($int & pow(2, $bits - 1)) ? -1 : 1;
        $fract = 1 + ((float)($int & (pow(2, $fsize) - 1)) / (float)pow(2, $fsize));
        $exp   = (($int >> $fsize) & 0xFF) - $ebias;
        $float = $fract * pow(2, $exp) * $sign;
        return $float;

    }
}


?>
