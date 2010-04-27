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
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../tables/DevicesTable.php";
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";
require_once dirname(__FILE__)."/../interfaces/DeviceContainerInterface.php";
require_once dirname(__FILE__).'/../interfaces/PacketConsumerInterface.php';

/**
 * This class does all of the work on endpoint devices.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceContainer extends DevicesTable
    implements DeviceContainerInterface, PacketConsumerInterface
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
        "group" => "default",        //  The database group to use
        "DriverInfo" => array(),     //  This is space for the driver to use
    );
    /** @var array This is where the data is stored */
    protected $data = array();

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
        $this->DeviceID  = $this->SerialNum;
        $this->HWPartNum = substr($string, self::HW_START, 10);
        $this->FWPartNum = substr($string, self::FW_START, 10);
        $this->FWVersion = substr($string, self::FWV_START, 6);
        $this->DeviceGroup      = trim(strtoupper(substr($string, self::GROUP, 6)));
        $this->BoredomThreshold = hexdec(trim(substr($string, self::BOREDOM, 2)));
        $this->RawSetup         = $string;
        $this->_registerDriver();
        if (is_object($this->epDriver)) {
            $this->epDriver->fromString(substr($string, self::CONFIGEND));
        }
        $this->params = $this->data["params"];
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
        $string  = self::hexify($this->SerialNum, 10);
        $string .= self::hexifyPartNum($this->HWPartNum);
        $string .= self::hexifyPartNum($this->FWPartNum);
        $string .= self::hexifyVersion($this->FWVersion);
        $string .= $this->DeviceGroup;
        $string .= self::hexify($this->BoredomThreshold, 2);
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
        if (!is_object($this->data["params"])) {
            $this->params = $this->data["params"];
        }
    }
    /**
    * Hexifies a version in x.y.z form.
    *
    * @param string $version The version is x.y.z form
    *
    * @return string Hexified version (asciihex)
        */
    public static function hexifyVersion($version)
    {
        $ver = explode(".", $version);
        $str = "";
        for ($i = 0; $i < 3; $i++) {
            $str .= self::stringSize($ver[$i], 2);
        }
        return $str;
    }

    /**
    * Hexifies a version in x.y.z form.
    *
    * @param string $PartNum The part number in XXXX-XX-XX-A form
    *
    * @return string Hexified version (asciihex)
        */
    public static function hexifyPartNum($PartNum)
    {
        $part = explode("-", $PartNum);
        $str  = self::stringSize($part[0], 4);
        $str .= self::stringSize($part[1], 2);
        $str .= self::stringSize($part[2], 2);
        if (!empty($part[3])) {
            $chr  = ord($part[3]);
            $str .= self::hexify($chr, 2);
        }
        self::stringSize($str, 10);
        return $str;
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
        if (is_object($this->epDriver)) {
            $this->epDriver->packetConsumer($pkt);
        }
    }
}
?>
