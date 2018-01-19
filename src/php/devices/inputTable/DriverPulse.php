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
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our units class */
require_once dirname(__FILE__)."/Driver.php";
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.8
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class DriverPulse extends Driver
{
    /** This is where our port is stored */
    protected $portExtra = 1;
    /** This is the list of our subdrivers */
    private $_subdrivers = array(
        "DEFAULT"                 => 0,
        "Bravo3Motion"            => 1,
        "GenericRevolving"        => 2,
        "LiquidFlow"              => 3,
        "LiquidVolume"            => 4,
        "MaximumAnemometer"       => 5,
        "MaximumRain"             => 6,
        "WattNode"                => 7,
    );
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$iopobject The output in question
    * @param int    $offset     The extra offset to use
    *
    * @return null
    */
    protected function __construct(&$iopobject, $offset = 0)
    {
        parent::__construct($iopobject, $offset);
        $size = $this->input()->device()->get("inputSize");
        if (is_int($size) && ($size > 0)) {
            $this->default["inputSize"] = $size;
        }
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
        $param = parent::get($name);
        switch ($name) {
        case "extraValues":
            $param = (array)$param;
            $param[$this->portExtra] = $this->input()->device()->get(
                "DigitalInputs"
            );
        }
        return $param;
    }
    /**
    * Calculates the difference between this value and the previous one.
    *
    * @param int   &$A    The data value given
    * @param array &$prev The previous reading
    *
    * @return int The difference value
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    protected function calcDiff(&$A, &$prev = null) 
    {
        if (is_null($prev["raw"]) || is_null($A)) {
            $val = null;
        } else if ($prev["raw"] > $A) {
            $val = $A;
        } else {
            $val = $A - $prev["raw"];
        }
        return $val;
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @param string $string The string to decode
    *
    * @return array
    */
    public function decode($string)
    {
        $extra = $this->pDecode($string, 0);
        $this->input()->set("extra", $extra);
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function encode()
    {
        return $this->pEncode(0);
    }
    /**
    * Decodes the driver portion of the setup string
    *
    * @param string &$string The string to decode
    * @param int    $index   The index to start in the extra
    *
    * @return array
    */
    protected function pDecode(&$string, $index)
    {
        $extra = $this->input()->get("extra");
        $this->subdriver($this->decodeInt(substr($string, 0, 2), 1));
        $extra[$index] = $this->decodeInt(substr($string, 2, 2), 1);
        $index++;
        $extra[$index] = $this->decodeInt(substr($string, 4, 2), 1);
        $index++;
        $extra[$index] = $this->decodeInt(substr($string, 6, 2), 1);
        $string = substr($string, 8);
        return $extra;
    }
    /**
    * Encodes this driver as a setup string
    *
    * @param int $index The index to start in the extra
    * 
    * @return array
    */
    protected function pEncode($index = 0)
    {
        $string = sprintf(
            "%02X",
            $driver,
            $this->subdriver[$driver][$drivers[1]]
        );
        $string  = $this->encodeInt((int)$this->subdriver(), 1);
        $string .= $this->encodeInt($this->getExtra($index), 1);
        $index++;
        $string .= $this->encodeInt($this->getExtra($index), 1);
        $index++;
        $string .= $this->encodeInt($this->getExtra($index), 1);
        return $string;
    }
    /**
    * Returns the port this data channel is attached to
    *
    * @param string $driver The driver to use
    *
    * @return array
    */
    protected function subdriver($driver = null)
    {
        if (!is_null($driver)) {
            $subd = array_flip((array)$this->_subdrivers);
            $name = $subd[$driver];
            if (empty($name)) {
                $name = "DEFAULT";
            }
            $this->input()->set("type", $name);
        }
        return $this->_subdrivers[$this->input()->get("type")];
    }
    /**
    * Returns the port this data channel is attached to
    *
    * @return array
    */
    protected function port()
    {
        $port  = $this->getExtra($this->portExtra);
        $ports = (array)$this->input()->device()->get("DigitalInputs");
        return str_replace(" ", "", (string)$ports[$port]);
    }
    /**
    * Returns an array of the pins and stuff this one uses
    *
    * @return null
    */
    public function uses()
    {
        return array($this->getExtra($this->portExtra));
    }
}


?>
