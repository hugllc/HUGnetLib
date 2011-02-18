<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
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
        "DriverInfo" => array(
            "PacketTimeout" => 0,    //  Timeout for packets. 0 == default
        ),                           //  This is space for the driver to use
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** @var object This is the endpoint driver */
    protected $epDriver = null;
    /** @var object This is the endpoint driver */
    public $params = "";
    /** @var object This is the endpoint driver */
    public $sensors = null;
    /** @var object This is the endpoint driver */
    public $DriverInfo = array();
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
        parent::__construct($data);
    }
    /**
    * Tries to run a function defined by what is called..
    *
    * @param string $name The name of the function to call
    * @param array  $args The array of arguments
    *
    * @return mixed
    */
    public function __call($name, $args)
    {
        if (method_exists($this->epDriver, $name)) {
            $code  ='return $this->epDriver->$name(';
            if (count($args) > 0) {
                $code .= '$args['.implode('], $args[', array_keys($args)).']';
            }
            $code .= ');';
            return eval($code);
        }
        return false;
    }

    /**
    * Creates the object from a string
    *
    * @return null
    */
    private function _registerDriver()
    {
        // Set this as the default
        $driver = $this->myConfig->plugins->getPlugin(
            "device",
            $this->FWPartNum.":".$this->HWPartNum.":".$this->FWVersion
        );
        if ($driver["Name"] !== $this->Driver) {
            $this->Driver = $driver["Name"];
        }
        if (get_class($this->epDriver) !== $driver["Class"]) {
            $class = $driver["Class"];
            if (class_exists($class)) {
                $this->epDriver = new $class($this);
                $this->data["DriverInfo"] = &$this->DriverInfo;
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
    public function fromSetupString($string)
    {
        $this->id = hexdec(substr($string, 0, 10));
        $this->DeviceID  = $this->id;
        $this->HWPartNum = substr($string, self::HW_START, 10);
        $this->FWPartNum = substr($string, self::FW_START, 10);
        $this->FWVersion = substr($string, self::FWV_START, 6);
        $this->DeviceGroup      = trim(strtoupper(substr($string, self::GROUP, 6)));
        $this->RawSetup         = $string;
        $this->_setupClasses();
        $this->params->DriverInfo["BoredomThreshold"] = hexdec(
            trim(substr($string, self::BOREDOM, 2))
        );
        if (is_object($this->epDriver)) {
            $this->epDriver->fromSetupString(substr($string, self::CONFIGEND));
        }
    }
    /**
    * Converts the object to a string
    *
    * @return mixed The value of the attribute
    */
    public function __toString()
    {
        return $this->toSetupString();
    }
    /**
    * Returns the object as a string
    *
    * @param bool $default Return items set to their default?
    *
    * @return string
    */
    public function toSetupString($default = true)
    {
        $string  = self::hexify($this->id, 10);
        $string .= self::hexifyPartNum($this->HWPartNum);
        $string .= self::hexifyPartNum($this->FWPartNum);
        $string .= self::hexifyVersion($this->FWVersion);
        $string .= $this->DeviceGroup;
        $string .= self::hexify($this->params->DriverInfo["BoredomThreshold"], 2);
        if (is_object($this->epDriver)) {
            $string .= $this->epDriver->toSetupString($default);
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
        // This is to upgrade the tables from the old format
        $upgrade = false;
        // Setup our classes
        $this->_setupClasses();
        // Make sure RawSetup is populated
        if (empty($this->RawSetup)) {
            $this->RawSetup = $this->toSetupString();
        }
        // Get the driver config
        if (is_object($this->epDriver)) {
            $this->epDriver->fromSetupString(
                substr($this->RawSetup, self::CONFIGEND)
            );
        }
    }
    /**
    * Creates the object from a string or array
    *
    * @param mixed &$data This is whatever you want to give the class
    *
    * @return null
    */
    public function fromAny(&$data)
    {
        if (is_string($data)
            && (preg_match("/(0039){1}[0-9A-F]{6}/", $data) > 0)
        ) {
            $this->fromSetupString($data);
        } else {
            parent::fromAny($data);
        }
        $this->_setupClasses();
    }
    /**
    * Sets the extra attributes field
    *
    * @return mixed The value of the attribute
    */
    public function clearData()
    {
        parent::clearData();
        $this->_setupClasses();
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @return null
    */
    private function _setupClasses()
    {
        if (!is_a($this->params, "DeviceParamsContainer")) {
            // Do the sensors
            $this->params = new DeviceParamsContainer($this->params);
            //$this->params = &$this->data["params"];
        }
        $this->_registerDriver();
        if (!is_a($this->sensors, "DeviceSensorsContainer")) {
            // Do the sensors
            $this->sensors = new DeviceSensorsContainer(
                $this->sensors, $this
            );
            //$this->sensors = &$this->data["sensors"];
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
    * returns true if the container is empty.  False otherwise
    *
    * @return bool Whether this container is empty or not
    */
    public function isEmpty()
    {
        return (bool)(empty($this->data["DeviceID"])
            || ($this->data["DeviceID"] === '000000'));
    }
    /**
    * Consumes packets and returns some stuff.
    *
    * This function deals with setup and ping requests
    *
    * @param PacketContainer &$pkt The packet that is to us
    *
    * @return string
    */
    public function packetConsumer(PacketContainer &$pkt)
    {
        if (method_exists($this->epDriver, "packetConsumer")) {
            $this->epDriver->packetConsumer($pkt);
        }
    }
    /**
    * Returns the name of the history table
    *
    * @param bool $history History if true, average if false
    *
    * @return string
    */
    public function historyTable($history = true)
    {
        if ($history) {
            $hist = $this->myConfig->plugins->getPlugin(
                "historyTable",
                $this->Driver
            );
        } else {
            $hist = $this->myConfig->plugins->getPlugin(
                "averageTable",
                $this->Driver
            );
        }
        return $hist["Class"];
    }

    /**
    * returns a history object for this device
    *
    * @param array $data    The data to build the history record with.
    * @param bool  $history History if true, average if false
    *
    * @return string
    */
    public function &historyFactory($data, $history = true)
    {
        $class = $this->historyTable($history);
        $obj = new $class($data);
        $obj->labels($this->historyHeader());
        $obj->device = &$this;
        return $obj;
    }

    /**
    * returns the header for the history table
    *
    * @return array
    */
    public function historyHeader()
    {
        $ret = array("Date" => "Date");
        for ($i = 0; $i < $this->sensors->Sensors; $i++) {
            if ($this->sensors->sensor($i)->dataType !== UnitsBase::TYPE_IGNORE) {
                $loc = $this->sensors->sensor($i)->location;
                if (empty($loc) && !is_numeric($loc)) {
                    $loc = "Sensor ".($i+1);
                }
                if (!empty($this->sensors->sensor($i)->units)) {
                    $loc .= " (".$this->sensors->sensor($i)->units.")";
                }
                $ret["Data".$i] = $loc;
            }
        }
        return $ret;
    }

    /**
    * returns an object with the controller of this device in it
    *
    * @return DeviceContainer
    */
    public function &getController()
    {
        static $cache;
        if (!is_object($cache[$this->ControllerKey])) {
            $cache[$this->ControllerKey] = new DeviceContainer();
            $cache[$this->ControllerKey]->getRow($this->ControllerKey);
        }
        return $cache[$this->ControllerKey];
    }
    /**
    * returns a reference to our driver
    *
    * @return DeviceContainer
    */
    public function &driver()
    {
        return $this->epDriver;
    }
}
?>
