<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetClass.php";
require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";
require_once dirname(__FILE__)."/../devInfo.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceContainer extends HUGnetContainer
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

    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "DeviceKey"         => 0,               // Database key
        "DeviceID"          => "000000",        // Device ID
        "DeviceName"        => "",              // Name of the device
        "SerialNum"         => 0,               // Serial number
        "HWPartNum"         => "",              // Hardware Part Number
        "FWPartNum"         => "",              // Firmware Part Number
        "FWVersion"         => "",              // Firmware Version
        "RawSetup"          => "",              // The raw setup
        "RawCalibration"    => "",              // The raw calibration
        "Active"            => 0,               // Is the device active
        "GatewayKey"        => 0,               // The gateway for this
        "ControllerKey"     => 0,               // The controller to use
        "ControllerIndex"   => 0,               // The index on the controller
        "DeviceLocation"    => "",              // The location of the device
        "DeviceJob"         => "",              // The job of the device
        "Driver"            => "eDEFAULT",      // The driver to use
        "PollInterval"      => 0,               // The poll interval in minutes
        "ActiveSensors"     => 0,               // How many active sensors
        "DeviceGroup"       => "FFFFFF",        // What group the device is in
        "BoredomThreshold"  => 0x50,            // Not currently used
        "LastConfig"        => "2000-01-01 00:00:00",  // Last configuration check
        "LastPoll"          => "2000-01-01 00:00:00",  // Last poll
        "LastHistory"       => "2000-01-01 00:00:00",  // Last history record
        "LastAnalysis"      => "2000-01-01 00:00:00",  // Last analysis performed
        "MinAverage"        => "15MIN",         // How often to do averages
        "CurrentGatewayKey" => 0,               // Not used
        "params"            => null,            // Device Parameters
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** The database table to use */
    protected $table = "devices";
    /** This is the Field name for the key of the record */
    protected $id = "DeviceKey";

    /** @var object This is the endpoint driver */
    protected $epDriver = null;
    /** @var object These are the registered devices */
    protected $myDev = array();
    /**
    * Builds the class
    *
    * @param array $data The data to build the class with
    *
    * @return null
    */
    public function __construct($data = array())
    {
        $this->myConfig = &ConfigContainer::singleton();
        $this->_registerDriverPlugins();
        parent::__construct($data);
    }

    /**
    * Disconnects from the gateway
    *
    * @return null
    */
    public function __destruct()
    {
    }
    /**
    * Disconnects from the gateway
    *
    * @return null
    */
    private function _registerDriverPlugins()
    {
        $myDev = $this->myConfig->plugins->getClass("device");
        $this->myDev = array();
        foreach ((array)$myDev as $device) {
            foreach ($device["Devices"] as $fw => $Firm) {
                foreach ($Firm as $hw => $ver) {
                    $dev = explode(",", $ver);
                    foreach ($dev as $d) {
                        if (!isset($this->dev[$hw][$fw][$d])) {
                            if (empty($this->myDev["drivers"][$device["Name"]])) {
                                $this->myDev["drivers"][$device["Name"]] = $device;
                            }
                            $this->myDev[$hw][$fw][$d]
                                = &$this->myDev["drivers"][$device["Name"]];
                        }
                    }
                }
            }
        }
    }

    /**
    *  Encodes the parameter array and returns it as a string
    *
    * @param array &$params the parameter array to encode
    *
    * @return string
    */
    function encodeParams(&$params)
    {
        if (is_array($params)) {
            $params = serialize($params);
            $params = base64_encode($params);
        }
        if (!is_string($params)) {
            $params = "";
        }
        return $params;
    }

    /**
     *  Decodes the parameter string and returns it as a array
     *
     * @param string &$params the parameter array to decode
     *
     * @return array
     */
    function decodeParams(&$params)
    {
        if (is_string($params)) {
            $params = base64_decode($params);
            $params = unserialize($params);
        }
        if (!is_array($params)) {
            $params = array();
        }
        return $params;
    }

    /**
    * Creates the object from a string
    *
    * @return null
    */
    private function _registerDriver()
    {
        $hwLoc = &$this->myDev[$this->HWPartNum];
        $driver = array("Name" => "eDEFAULT", "Class" => "eDEFAULTDriver");
        if (is_array($hwLoc[$this->FWPartNum][$this->FWVersion])) {
            $driver = $hwLoc[$this->FWPartNum][$this->FWVersion];
        } else if (is_array($hwLoc[$this->FWPartNum]["BAD"])) {
            // Use the default driver here
        } else if (is_array($hwLoc[$this->FWPartNum]["DEFAULT"])) {
            $driver = $hwLoc[$this->FWPartNum]["DEFAULT"];
        } else if (is_array($hwLoc["DEFAULT"]["DEFAULT"])) {
            $driver = $hwLoc["DEFAULT"]["DEFAULT"];
        }

        if ($driver["Name"] !== $this->Driver) {
            $this->Driver = $driver["Name"];
        }
        if (get_class($this->epDriver) !== $driver["Class"]) {
            $class = $driver["Class"];
            if (class_exists($class)) {
                $this->epDriver = new $class($this);
            }
        }
    }

    /**
    * Creates the object from a string
    *
    * @param string $string This is the raw string for the device
    *
    * @return null
    */
    public function fromString($string)
    {
        $this->SerialNum = hexdec(substr($string, 0, 10));
        $this->DeviceID  = devInfo::sn2DeviceID($this->SerialNum);
        $this->HWPartNum = devInfo::dehexifyPartNum(
            substr($string, self::HW_START, 10)
        );
        $this->FWPartNum        = devInfo::dehexifyPartNum(
            substr($string, self::FW_START, 10)
        );
        $this->FWVersion        = devInfo::dehexifyVersion(
            substr($string, self::FWV_START, 6)
        );
        $this->DeviceGroup      = trim(strtoupper(substr($string, self::GROUP, 6)));
        $this->BoredomThreshold = hexdec(trim(substr($string, self::BOREDOM, 2)));
        $this->RawSetup         = $string;
        $this->_registerDriver();
        if (is_object($this->epDriver)) {
            $this->epDriver->fromString(substr($string, self::CONFIGEND));
        }
    }
    /**
    * Returns the object as a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return string
    */
    public function toString($default = true)
    {
        $string  = devInfo::hexify($this->SerialNum, 10);
        $string .= devInfo::hexifyPartNum($this->HWPartNum);
        $string .= devInfo::hexifyPartNum($this->FWPartNum);
        $string .= devInfo::hexifyVersion($this->FWVersion);
        $string .= $this->DeviceGroup;
        $string .= devInfo::hexify($this->BoredomThreshold, 2);
        if (is_object($this->epDriver)) {
            $string .= $this->epDriver->toString($default);
        }
        return $string;

    }

    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($array)
    {
        parent::fromArray($array);
        $this->_registerDriver();
        if (is_object($this->epDriver)) {
            $this->epDriver->fromString(substr($string, self::CONFIGEND));
        }
        if (empty($this->RawSetup)) {
            $this->RawSetup = substr($this->toString(), 0, self::CONFIGEND);
        }
    }

}
?>
