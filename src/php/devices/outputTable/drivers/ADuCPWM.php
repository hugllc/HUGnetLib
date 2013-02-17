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
namespace HUGnet\devices\outputTable\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../DriverADuC.php";

/**
 * Driver for reading voltage based pressure sensors
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.10.2
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCPWM extends \HUGnet\devices\outputTable\DriverADuC
{
    /** This is the class for our table entry */
    protected $entryClass = "ADuCPWM";
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "Pulse Width Modulator",
        "shortName" => "PWM",
        "extraText" => array(
        ),
        "extraDefault" => array(
            0, 0, 0, 0, 0, 0, 0, 0xFFFF, 0xFFFF, 0xFFFF
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
        ),
        "min" => 0,
        "max" => 0xFFFF,
        "zero" => 0,
    );
    /** This tells us our mapping from extra to entry */
    protected $entryMap = array(
        "PWM5INV", "PWM3INV", "PWM1INV", "PWMCP", "POINV", "HOFF", "DIR",
        "PWM0LEN", "PWM1LEN", "PWM2LEN"
    );
    /** This is the base for our setup byte */
    protected $regBase = 0x0010;
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
                if (is_array($entry[$field]["valid"])) {
                    $ret[$key]  = $entry[$field]["valid"];
                } else {
                    $ret[$key]  = $entry[$field]["size"];
                }
            }
        } else if ($name == "extraText") {
            $entry = $this->entry()->fullArray();
            foreach ($this->entryMap as $key => $field) {
                $ret[$key]  = $entry[$field]["desc"];
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
        $extra = (array)$this->output()->get("extra");
        $this->entry()->decode($string);
        $decode = $this->entry()->toArray();
        foreach ($this->entryMap as $key => $field) {
            $extra[$key] = $decode[$field];
        }
        $this->output()->set("extra", $extra);
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function encode()
    {
        $encode = array();
        foreach ($this->entryMap as $key => $field) {
            $encode[$field] = (int)$this->getExtra($key);
        }
        $this->entry()->fromArray($encode);
        return $this->entry()->encode();
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channels()
    {
        return array(
            array(
                "min" => $this->get("min"),
                "max" => $this->get("max"),
                "label" => (string)$this->output()->get("location")." 0",
                "index" => 0,
            ),
            array(
                "min" => $this->get("min"),
                "max" => $this->get("max"),
                "label" => (string)$this->output()->get("location")." 1",
                "index" => 1,
            ),
            array(
                "min" => $this->get("min"),
                "max" => $this->get("max"),
                "label" => (string)$this->output()->get("location")." 2",
                "index" => 2,
            ),
        );
    }

}


?>
