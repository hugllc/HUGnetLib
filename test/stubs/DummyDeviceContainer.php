<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once CODE_BASE."base/HUGnetClass.php";
/** This is our test configuration */
require_once CODE_BASE."containers/DeviceSensorsContainer.php";
/** This is our test configuration */
require_once CODE_BASE."containers/DeviceParamsContainer.php";
/** This is our test configuration */
require_once CODE_BASE."containers/DeviceContainer.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DummyDeviceContainer extends HUGnetClass
{
    /** @var This is where the driver info goes */
    public $DriverInfo = array();
    /** @var The gateway key */
    public $GatewayKey = 5;
    /** @var The string to return */
    public $string = "000000000100392601500039260150010203FFFFFF10";
    /** @var The string to return */
    public $otherString = "";
    /** @var The last config time */
    public $LastConfig = "1970-01-01 00:00:00";
    /** @var The last poll time */
    public $LastPoll = "1970-01-01 00:00:00";
    /** @var The last poll time */
    public $PollInterval = 10;
    /** @var The device ID */
    public $DeviceID = "000123";
    /** @var The device ID */
    public $id = 0x123;

    public $default = array(
        "DeviceID" => "000123",
        "DriverInfo" => array(),
        "GatewayKey" => 5,
        "string" => "000000000100392601500039260150010203FFFFFF10",
        "LastConfig" => "1970-01-01 00:00:00",
        "LastPoll" => "1970-01-01 00:00:00",
        "PollInterval" => 10,
    );
    /**
    * Builds the class
    *
    * @return null
    */
    public function __construct()
    {
        $this->sensors = new DeviceSensorsContainer(array(), $this);
        $this->params = new DeviceParamsContainer(array(), $this);
    }

    /**
    * Builds the class
    *
    * @return null
    */
    public function __toString()
    {
        return $this->string;
    }
    /**
    * Builds the class
    *
    * @param string $string The string to set
    *
    * @return null
    */
    public function fromSetupString($string)
    {
        $this->string = $string;
    }
    /**
    * returns a string
    *
    * @return null
    */
    public function toSetupString()
    {
        return $this->__toString();
    }
    /**
    * returns a string
    *
    * @return null
    */
    public function toString()
    {
        return $this->otherString;
    }
    /**
    * resets a value to its default
    *
    * @param string $name This is the attribute to get
    *
    * @return mixed The value of the attribute
    */
    public function setDefault($name)
    {
        if (array_key_exists($name, $this->default)) {
            $this->$name = $this->default[$name];
        }
    }
    /**
    * Creates a sensor object
    *
    * @param int $key The array key for the sensor object
    *
    * @return Returns a reference to the sensor object
    */
    public function &sensor($key)
    {
        return $this->sensors->sensor($key);
    }



}
?>
