<?php
/**
 * Main class for dealing with endpoints.
 *
 * PHP version 5
 *
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
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */


if (!defined(HUGNET_INCLUDE_PATH)) {
    define("HUGNET_INCLUDE_PATH", dirname(__FILE__));
}
/** This is our interface */
require_once HUGNET_INCLUDE_PATH."/interfaces/endpoint.php";
/** This is the plugin interface */
require_once HUGNET_INCLUDE_PATH."/lib/plugins.inc.php";
/** This is for the base class */
require_once dirname(__FILE__)."/base/HUGnetClass.php";

/**
 * Class for talking with HUGNet endpoints
 *
 *  All communication with endpoints should go through this class.
 *
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @deprecated since version 0.9.0
 */
class Endpoint extends HUGnetClass
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

    /** @var int This is the default number of decimal places to use if
    *  it is not specified anywhere else
    */
    private $_decimalPlaces = 2;

    /** @var int The error number.  0 if no error occurred  */
    public $Errno = 0;
    /** @var string Error String  */
    public $Error = "";
    /** @var array An array of driver information.  */
    private $_drivers = array();
    /** @var This is just a storehouse for what colors to print things in HTML  */
    private $_errorColors = array(
        "DevOnBackup" => array(
            "Severity" => "Low",
            "Description" => "Device is polled on backup server",
            "Style" => "#00E000"
       ),
    );
    /** @var string Table to use for devices */
    private $_device_table = "devices";
    /** @var string Table to use for devices */
    private $_analysis_table = "analysis";
    /** @var string Table to use for devices */
    private $_raw_history_table = "history_raw";
    /** @var string Table to use for devices */
    private $_packet_log_table = "PacketLog";
    /** @var array Our configuration */
    private $_config = array();
    /** @var object This is our devInfo object */
    private $_devInfo;

    /**
    * returns an endpoint object with the device in it.
    *
    * @param mixed $id   This is either the DeviceID, DeviceName or DeviceKey
    * @param int   $type The type of the 'id' parameter.  It is "ID" for DeviceID,
    *         "NAME" for DeviceName or "KEY" for DeviceKey.  "KEY" is the default.
    *
    * @return Endpoint object
    */
    public static function &getDevice($id, $type="KEY")
    {
        // $analysis = &HUGnetDB::getInstance("Analysis", $this->_config);
        // $gateway  = &HUGnetDB::getInstance("Gateway", $this->_config);
        // $device   = &HUGnetDB::getInstance("Device", $this->_config);
    }
    /**
    * returns an array of endpoint objects, one for each device
    *
    * @param string $where where clause for the database query
    *
    * @return array of Endpoint objects
    */
    public static function &getDevices($where)
    {
    }
    /**
    * Checks to see if this is a controller.
    *
    * @param array &$info This is a device information array
    *
    * @return bool
    */
    public function isController()
    {
        return (bool) $this->_driver->isController;
    }

    /**
    * Checks to make sure that all of the device information is valid
    *
    * Mostly this just forces the type of variable.  In other cases it does other
    * things.
    *
    * @return bool Whether or not the driver is valid
    */
    function _checkDevice()
    {
        foreach ($this->_attributes as $attrib) {
            if (method_exists($this->devInfo, $attrib)) {
                $this->$attrib();
            }
        }
    }
    /**
    * Checks to see if the driver is valid.  If it is it returns true.
    *
    * For the driver to be valid, it has to be a object of the class stored
    * in $this->Driver.
    *
    * @return bool Whether or not the driver is valid
    */
    function _checkDriver()
    {
        if (!is_object($this->_driver)) {
            return false;
        }
        return (get_class($this->_driver) === $this->Driver);
    }
    /**
    * Sets the driver correctly
    *
    * @return null
    */
    function _setDriver()
    {
        $HWPart = &$this->HWPartNum;
        $FWPart = &$this->FWPartNum;
        $FWVer  = &$this->FWVersion;
        if (isset($this->_drivers[$HWPart][$FWPart][$FWVer])) {
            $Driver = $this->_drivers[$HWPart][$FWPart][$FWVer];
        } else if (isset($this->_drivers[$HWPart][$FWPart]["BAD"])) {
            $Driver = "eDEFAULT";
        } else if (isset($this->_drivers[$HWPart][$FWPart]["DEFAULT"])) {
            $Driver = $this->_drivers[$HWPart][$FWPart]["DEFAULT"];
        } else if (isset($this->_drivers[$HWPart]["DEFAULT"]["DEFAULT"])) {
            $Driver = $this->_drivers[$HWPart]["DEFAULT"]["DEFAULT"];
        } else {
            $Driver = "eDEFAULT";
        }
        if (class_exists($Driver)) {
            $this->Driver = $Driver;
            $this->_driver = new $Driver($this->_config);
        } else {
            $this->Driver = null;
        }
    }
    /**
    * Register all of our drivers
    *
    * @return null
    */
    private function _registerDrivers()
    {
        $this->_plugins = new Plugins(
            $this->_config["pluginDir"],
            $this->_config["pluginExt"]
        );
        if (is_array($plugins->plugins["Generic"]["endpoint"])) {
            foreach ($plugins->plugins["Generic"]["endpoint"] as $driver) {
                $devices = @eval(
                    'return '.$driver["Class"].'::$devices;'
                );
                if (is_array($devices)) {
                    foreach ($devices as $fw => $Firm) {
                        foreach ($Firm as $hw => $ver) {
                            $dev = explode(",", $ver);
                            foreach ($dev as $d) {
                                $this->_drivers[$hw][$fw][$d] = $driver["Class"];
                            }
                        }
                    }
                }
            }
        }

    }

    /**
    * Constructor
    *
    * @param array  $config  The configuration to use
    * @param object $plugins A plugin object
    */
    function __construct($config = array())
    {
        // Register our drivers
        $this->_registerDrivers();

        // Set up the device if we can
        if (is_string($config["devInfo"])) {
            $this->setDevice($config["devInfo"]);
        } else if (is_string($config["where"])) {
            $this->getDevices($config["where"]);
        } else if (isset($config["DeviceKey"])) {
            $this->getDevice($config["DeviceKey"], $config["DeviceKeyType"]);
        }
        // Set the configuration
        $this->setConfig($config);


    }
    /**
    * Gets an instance of the HUGnet Driver
    *
    * @param array $config The configuration to use
    *
    * @return object A reference to a driver object
    */
    function setConfig($config = array())
    {
        // Make sure it is an array
        if (!is_array($config)) {
            $config = array();
        } else {
            // Unset some stuff we don't want in the config
            // We don't need to do this if we didn't get an array
            unset($config["where"]);
            unset($config["DeviceKey"]);
            unset($config["DeviceKeyType"]);
            unset($config["devInfo"]);
        }
        // Make sure a couple of things are set.
        if (empty($config["pluginDir"])) {
            $config["pluginDir"] = dirname(__FILE__)."/drivers/endpoints";
        }
        if (empty($config["pluginExt"])) {
            $config["pluginExt"] = "php";
        }
        $this->_config = $config;
    }
    /**
    * Gets an instance of the HUGnet Driver
    *
    * @param array $config The configuration to use
    *
    * @return object A reference to a driver object
    */
    function &_getInstance($config)
    {
        static $instances;
        $key = serialize($config);

        if (empty($instances[$key])) {
            $instances[$key] = new Endpoint($config);
        }
        return $instances[$key];
    }

}


?>
