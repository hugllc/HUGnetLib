<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * Networking for devices.
 *
 * This class will do all of the networking for devices.  It will poll, get configs,
 * update software, and anything else related to talking to devices.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DevConfig
{
    /** Where in the config string the hardware part number starts  */
    const HW_START = 10;
    /** Where in the config string the firmware part number starts  */
    const FW_START = 20;
    /** Where in the config string the firmware version starts  */
    const FWV_START = 30;
    /** Where in the config string the group starts  */
    const GROUP = 36;
    /** Where in the config string the boredom constant starts  */
    const BOREDOM = 42;
    /** Where in the config string the configuration ends  */
    const CONFIGEND = 44;
    /**
    * This is the cache object
    */
    private $_table = null;
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param string &$table The table object
    *
    * @return null
    */
    private function __construct(&$table)
    {
        \HUGnet\System::exception(
            get_class($this)." needs to be passed a table object",
            "InvalidArgument",
            !is_object($table)
        );
        $this->_table   = &$table;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
    }
    /**
    * This function creates the system.
    *
    * @param object &$table (object)The table to use
    *
    * @return null
    */
    public static function &factory(&$table)
    {
        $obj = new DevConfig($table);
        return $obj;
    }
    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function decode($string)
    {
        if (!$this->_checkSetupString($string)) {
            return;
        }
        $did = hexdec(substr($string, 0, 10));
        $this->_table->set("id", $did);
        $this->_table->set("DeviceID", $did);
        $this->_table->set("HWPartNum", substr($string, self::HW_START, 10));
        $this->_table->set("FWPartNum", substr($string, self::FW_START, 10));
        $this->_table->set("FWVersion", substr($string, self::FWV_START, 6));
        $this->_table->set(
            "DeviceGroup", trim(strtoupper(substr($string, self::GROUP, 6)))
        );
        $this->_table->set("RawSetup", $string);
        $leftover =substr($string, self::CONFIGEND);
    }
    /**
    * Checks to see if the string is valid
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    private function _checkSetupString($string)
    {
        if (substr($string, self::HW_START, 4) !== "0039") {
            return false;
        }
        if (substr($string, self::FW_START, 4) !== "0039") {
            return false;
        }

        return true;
    }

    /**
    * Returns the object as a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return string
    */
    public function encode($default = true)
    {
        $string  = sprintf("%010X", $this->_table->get("id"));
        $string .= $this->_hexifyPartNum($this->_table->get("HWPartNum"));
        $string .= $this->_hexifyPartNum($this->_table->get("FWPartNum"));
        $string .= $this->_hexifyVersion($this->_table->get("FWVersion"));
        $string .= sprintf("%06X", hexdec($this->_table->get("DeviceGroup")));
        $string .= "FF";
        return $string;

    }
    /**
    * Hexifies a version in x.y.z form.
    *
    * @param string $version The version is x.y.z form
    *
    * @return string Hexified version (asciihex)
    */
    private function _hexifyVersion($version)
    {
        $ver = explode(".", $version);
        return sprintf(
            "%02X%02X%02X",
            hexdec($ver[0]),
            hexdec($ver[1]),
            hexdec($ver[2])
        );
    }

    /**
    * Hexifies a version in x.y.z form.
    *
    * @param string $PartNum The part number in XXXX-XX-XX-A form
    *
    * @return string Hexified version (asciihex)
    */
    private function _hexifyPartNum($PartNum)
    {
        $part = explode("-", $PartNum);
        $chr  = ord($part[3]);
        return sprintf(
            "%04X%02X%02X%02X",
            hexdec($part[0]),
            hexdec($part[1]),
            hexdec($part[2]),
            $chr
        );
    }

}


?>
