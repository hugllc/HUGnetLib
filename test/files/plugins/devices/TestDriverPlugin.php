<?php
/**
 * Tests the filter class
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
 * @package    HUGnetLibTest
 * @subpackage Files
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the base for this plugin */
require_once CODE_BASE.'base/DeviceDriverBase.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Files
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class TestDriverPlugin extends DeviceDriverBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "testDriver",
        "Type" => "device",
        "Class" => "TestDriverPlugin",
        "Flags" => array(
            "0039-24-67-C:0039-CE-01-A:DEFAULT",
            "0039-24-67-C:0039-CF-01-A:0.1.2",
            "0039-24-67-C:0039-CF-01-A:0.2.3",
            "DEFAULT:0039-CB-01-A:DEFAULT",
            "DEFAULT:0039-CD-01-A:DEFAULT",
            "0039-25-67-C:0039-CC-01-A:BAD",
        ),
    );
    /**
    * Builds the class
    *
    * @param object &$obj   The object that is registering us
    * @param mixed  $string The string we will use to build the object
    *
    * @return null
    */
    public function __construct(&$obj, $string = "")
    {
        parent::__construct($obj, $string);
        $this->myDriver->DriverInfo["PhysicalSensors"] = 2;
        $this->myDriver->DriverInfo["VirtualSensors"] = 0;
        $this->fromSetupString($string);
    }
    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function fromSetupString($string)
    {
        // This is just so we can check for it.
        $this->myDriver->DriverInfo["RawDriverInfo"] = (string)$string;
    }
    /**
    * Creates the object from a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toSetupString($default = true)
    {
        return $this->myDriver->DriverInfo["RawDriverInfo"];
    }
    /**
    * This takes the numeric job and replaces it with a name
    *
    * @param PacketContainer &$pkt The packet that is to us
    *
    * @return string
    */
    public function packetConsumer(PacketContainer &$pkt)
    {
        $GLOBALS["packetConsumer"] = &$pkt;
    }

    /**
    * This takes the numeric job and replaces it with a name
    *
    * @param mixed &$value The value to use
    *
    * @return string
    */
    public function &testCall(&$value)
    {
        $GLOBALS["testCall"] = &$value;
        return $value;
    }

}