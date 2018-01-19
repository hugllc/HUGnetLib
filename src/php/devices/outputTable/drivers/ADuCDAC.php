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
namespace HUGnet\devices\outputTable\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../DriverADuC.php";
/** This is our interface */
require_once dirname(__FILE__)."/../DriverInterface.php";

/**
 * Driver for reading voltage based pressure sensors
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.10.0
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCDAC extends \HUGnet\devices\outputTable\DriverADuC
    implements \HUGnet\devices\outputTable\DriverInterface
{
    /** This is the class for our table entry */
    protected $entryClass = "ADuCDAC";
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Digital to Analog Converter",
        "shortName" => "DAC",
        "extraText" => array(
            6 => "Initial Value"
        ),
        "extraDefault" => array(
            0, 0, 0, 1, 0, 3, 0
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            6 => 10
        ),
        "extraDesc" => array(
            6 => "The value to initially set the control channel to"
        ),
        "extraNames" => array(
            "initialvalue" => 6,
        ),
        "min" => 0,
        "max" => array(0 => 4095, 1 => 65535),
        "zero" => array(0 => 1556, 1 => 24900),
        "port" => "DAC0",
        "provides" => array("CC"),
    );
    /** This tells us our mapping from extra to entry */
    protected $entryMap = array(
        "DACBUFLP", "OPAMP", "DACBUFBYPASS", "DACMODE", "Rate", "Range"
    );
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    public function get($name)
    {
        $ret = parent::get($name);
        if ($name == "extraValues") {
            $entry = $this->entry()->fullArray();
            foreach ($this->entryMap as $key => $field) {
                $ret[$key]  = -1;
            }
        } else if ($name == "extraText") {
            $entry = $this->entry()->fullArray();
            foreach ($this->entryMap as $key => $field) {
                $ret[$key]  = $entry[$field]["desc"];
            }
        } else if ($name == "extraNames") {
            foreach ($this->entryMap as $key => $field) {
                $ret[strtolower($field)]  = $key;
            }
        } else if ($name == "extraDesc") {
            $entry = $this->entry()->fullArray();
            foreach ($this->entryMap as $key => $field) {
                $ret[$key]  = $entry[$field]["longdesc"];
            }
        } else if ($name == "max") {
            $entry = $this->entry()->toArray();
            if ($entry["DACMODE"] == 1) {
                $ret = 65535; 
            } else {
                $ret = 4095;
            }
        } else if ($name == "zero") {
            $entry = $this->entry()->toArray();
            if ($entry["DACMODE"] == 1) {
                $ret = 24900; 
            } else {
                $ret = 1556;
            }
        }
        return $ret;
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
        $this->entry()->decode($string);
        $this->output()->set("tableEntry", $this->entry()->toArray());
        $extra = (array)$this->output()->get("extra");
        $extra[6] = $this->decodeInt(substr($string, 4, 4));
        $this->output()->set("extra", $extra);
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function encode()
    {
        $string  = $this->entry()->encode();
        $string .= $this->encodeInt($this->getExtra(6));
        return $string;
    }

}


?>
