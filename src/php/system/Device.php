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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/SystemTableAction.php";
/** This the driver class we use */
require_once dirname(__FILE__)."/../devices/Driver.php";
/** This is our base class */
require_once dirname(__FILE__)."/../interfaces/WebAPI.php";
/** This is our system interface */
require_once dirname(__FILE__)."/../interfaces/SystemInterface.php";

/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Device extends \HUGnet\base\SystemTableAction
    implements \HUGnet\interfaces\WebAPI, \HUGnet\interfaces\SystemInterface
{
    /**
    * This is the cache for the drivers.
    */
    private $_driverCache = array();
    /**
    * This is the cache the roles.
    */
    private $_role = null;
    /**
    * This is the firmware table
    */
    private $_firmware = null;
    /** This is where we store our objects */
    protected $functions = array(
        "insertVirtual" => "table",
        "dataChannel" => "dataChannels",
        "controlChannel" => "controlChannels",
        "reboot" => "network",
        "send" => "action",
    );
    /** This is where we store our objects */
    protected $classes = array(
    );
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        foreach (array_keys($this->_driverCache) as $key) {
            unset($this->_driverCache[$key]);
        }
        unset($this->_firmware);
        parent::__destruct();
    }
    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $table   The table to use
    *
    * @return null
    */
    public static function &factory(&$system, $data=null, $table="Devices")
    {
        $object = parent::factory($system, $data, $table);
        return $object;
    }
    /**
    * Gets a value
    *
    * @param string $field the field to get
    *
    * @return null
    */
    public function get($field)
    {
        $ret = $this->driver()->get($field);
        if (is_null($ret)) {
            $ret = $this->table()->get($field);
        }
        return $ret;
    }
    /**
    * Returns the table as an array
    *
    * @param bool $default Whether or not to include the default values
    *
    * @return array
    */
    public function toArray($default = false)
    {
        $return = $this->table()->toArray($default);
        if ($default) {
            $return = array_merge($this->driver()->toArray(), $return);
        }
        // This could be fixed in the driver
        $return["Role"] = $this->get("Role");
        $return["dataChannels"] = $this->dataChannels()->toArray($default);
        $return["controlChannels"] = $this->controlChannels()->toArray($default);
        if (is_string($return["params"])) {
            $return["params"] = (array)json_decode($return["params"], true);
        }
        if (is_string($return["localParams"])) {
            $return["localParams"] = (array)json_decode(
                $return["localParams"], true
            );
        }
        if ($default) {
            $this->_toArrayExtra($return);
            $int = ($return["PollInterval"] < 30) ? 30 : $return["PollInterval"];
            $late = $this->system()->now() - ($int * 2);
            if (($late > $return["params"]["LastPoll"])
                && ($return["PollInterval"] > 0)
                && ($return["Active"] != 0)
            ) {
                $return["LatePoll"] = true;
            } else {
                $return["LatePoll"] = false;
            }
        }
        return $return;
    }
    /**
    * Returns the extra bits of the table as an array
    *
    * @param array &$return The array to add to
    *
    * @return null
    */
    private function _toArrayExtra(&$return)
    {
        $return["Roles"] = $this->_role()->getAll($this->driver()->get("arch"));
        $return["averageTypes"] = array_merge(
            (array)$this->historyFactory(array(), false)->averageTypes(),
            array("history" => "History")
        );
        if ($return["loadable"]) {
            $this->firmware()->set("HWPartNum", $return["HWPartNum"]);
            $this->firmware()->set("FWPartNum", $return["FWPartNum"]);
            $this->firmware()->set("RelStatus", \HUGnet\db\tables\Firmware::DEV);
            $this->firmware()->getLatest();
            $new = $this->firmware()->compareVersion(
                $return["FWVersion"], $this->firmware()->Version
            );
            // @codeCoverageIgnoreStart
            if ($new < 0) {
                $return["update"] = $this->firmware()->Version;
            }
            // @codeCoverageIgnoreEnd
        }
    }
    /**
    * Returns the table as an array
    *
    * @return array
    */
    public function fullArray()
    {
        $return = array_merge(
            $this->driver()->toArray(),
            $this->table()->toArray(true)
        );
        unset($return["sensors"]);
        $params = json_decode($return["params"], true);
        $return["params"] = (array)$params;
        $return["sensors"] = array();
        for ($i = 0; $i < $return["totalSensors"]; $i++) {
            $return["sensors"][$i] = $this->input($i)->toArray();
        }
        $return["channels"] = $this->dataChannels()->toArray(true);
        if ($return["loadable"]) {
            $this->firmware()->set("HWPartNum", $return["HWPartNum"]);
            $this->firmware()->set("FWPartNum", $return["FWPartNum"]);
            $this->firmware()->set("RelStatus", \HUGnet\db\tables\Firmware::DEV);
            $this->firmware()->getLatest();
            $new = $this->firmware()->compareVersion(
                $return["FWVersion"], $this->firmware()->Version
            );
            // @codeCoverageIgnoreStart
            if ($new < 0) {
                $return["update"] = $this->firmware()->Version;
            }
            // @codeCoverageIgnoreEnd
        }
        return $return;
    }

    /**
    * This function creates the system.
    *
    * @return Reference to the network object
    */
    public function &network()
    {
        include_once dirname(__FILE__)."/../devices/Network.php";
        return \HUGnet\devices\Network::factory(
            $this->system(),
            $this,
            $this->driver()
        );
    }
    /**
    * This function creates the system.
    *
    * @return Reference to the network object
    */
    protected function &webInterface()
    {
        include_once dirname(__FILE__)."/../devices/WebInterface.php";
        return \HUGnet\devices\WebInterface::factory(
            $this->system(),
            $this,
            $this->driver()
        );
    }
    /**
    * returns a history object for this device
    *
    * @param object $args  The argument object
    * @param array  $extra Extra data from the
    *
    * @return string
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function webAPI($args, $extra)
    {
        return $this->webInterface()->webAPI($args, $extra);
    }
    /**
    * This function creates the system.
    *
    * @return Reference to the network object
    */
    public function &action()
    {
        $class = $this->driver()->get("actionClass");
        $file = dirname(__FILE__)."/../devices/".$class.".php";
        if (file_exists($file)) {
            include_once $file;
        }
        $class = "\\HUGnet\\devices\\".$class;
        return $class::factory(
            $this->system(),
            $this,
            $this->driver()
        );

    }
    /**
    * This takes the class and makes it into a setup string
    *
    * @param bool $showFixed Show the fixed portion of the data
    *
    * @return string The encoded string
    */
    public function encode($showFixed = true)
    {
        include_once dirname(__FILE__)."/../devices/Config.php";
        $string  = \HUGnet\devices\Config::encode($this, $showFixed);
        $string .= $this->driver()->encode($showFixed);
        return $string;
    }
    /**
    * This builds the class from a setup string
    *
    * @param string $string The setup string to decode
    *
    * @return bool True on success, false on failure
    */
    public function decode($string)
    {
        include_once dirname(__FILE__)."/../devices/Config.php";
        $extra = \HUGnet\devices\Config::decode($string, $this);
        if (is_string($extra)) {
            $this->driver()->decode($extra);
            return true;
        }
        return false;
    }
    /**
    * This creates the driver
    *
    * It doesn't worry too much about a valid driver.  If the driver is not valid
    * then devices\Driver::factory returns an EDEFAULT object.
    *
    * @param string $driver The driver to use.  Leave blank for automatic.
    *
    * @return null
    */
    protected function &driver($driver = null)
    {
        if (empty($driver)) {
            $driver = \HUGnet\devices\Driver::getDriver(
                $this->table()->get("HWPartNum"),
                $this->table()->get("FWPartNum"),
                $this->table()->get("FWVersion")
            );
        }
        if (!is_object($this->_driverCache[$driver])) {
            include_once dirname(__FILE__)."/../devices/Driver.php";
            $this->_driverCache[$driver] = devices\Driver::factory($driver, $this);
        }
        return $this->_driverCache[$driver];
    }
    /**
    * This creates the sensor drivers
    *
    * @param mixed $chans Channel information
    *
    * @return null
    */
    public function &dataChannels($chans = null)
    {
        include_once dirname(__FILE__)."/../devices/DataChannels.php";
        return \HUGnet\devices\DataChannels::factory($this->system(), $this, $chans);
    }
    /**
    * This creates the sensor drivers
    *
    * @param mixed $chans Channel information
    *
    * @return null
    */
    public function &controlChannels($chans = null)
    {
        include_once dirname(__FILE__)."/../devices/ControlChannels.php";
        return \HUGnet\devices\ControlChannels::factory(
            $this->system(), $this, $chans
        );
    }
    /**
    * Gets one of the parameters
    *
    * @param string $field The field to get
    *
    * @return The value of the field
    */
    public function &getParam($field)
    {
        $value = $this->_getParam("params", $field);
        if (!is_null($value)) {
            return $value;
        }
        $params = $this->table()->get("params");
        /* This converts the old system */
        $array = unserialize(base64_decode($params));
        /* Most of the old stuff is stored in "DriverInfo" */
        if (is_array($array["DriverInfo"])) {
            $array = $array["DriverInfo"];
        }
        /* Now re encode it properly, or return null if it is empty */
        if (is_array($array)) {
            $this->table()->set("params", json_encode($array));
        } else {
            $array = array();
        }
        return $array[$field];
    }
    /**
    * Gets one of the parameters
    *
    * @param string $field The field to get
    *
    * @return The value of the field
    */
    public function &getLocalParam($field)
    {
        return $this->_getParam("localParams", $field);
    }
    /**
    * Gets one of the parameters
    *
    * @param string $key   The key to use
    * @param string $field The field to get
    *
    * @return The value of the field
    */
    private function &_getParam($key, $field)
    {
        $params = $this->table()->get($key);
        $array = json_decode($params, true);
        return $array[$field];
    }
    /**
    * Sets one of the parameters
    *
    * @param string $field The field to set
    * @param mixed  $value The value to set the field to
    *
    * @return null
    */
    public function &setParam($field, $value)
    {
        return $this->_setParam("params", $field, $value);
    }
    /**
    * Sets one of the parameters
    *
    * @param string $field The field to set
    * @param mixed  $value The value to set the field to
    *
    * @return null
    */
    public function &setLocalParam($field, $value)
    {
        return $this->_setParam("localParams", $field, $value);
    }
    /**
    * Sets one of the parameters
    *
    * @param string $key   The key to use
    * @param string $field The field to set
    * @param mixed  $value The value to set the field to
    *
    * @return null
    */
    private function &_setParam($key, $field, $value)
    {
        /* This makes sure the field is always in json format */
        $this->_getParam($key, $field);
        /* get the fields */
        $params = $this->table()->get($key);
        $params = json_decode($params, true);
        $params[$field] = $value;
        return $this->table()->set($key, json_encode($params));
    }
    /**
    * This function gives us access to the table class
    *
    * @return reference to the table class object
    */
    public function &firmware()
    {
        if (!is_object($this->_firmware)) {
            $this->_firmware = $this->system()->table("Firmware");
        }
        return $this->_firmware;
    }

    /**
    * Loads the data into the table class
    *
    * @param mixed $data (int)The id of the record,
    *                    (array) or (string) data info array
    *
    * @return null
    */
    public function load($data)
    {
        $ret = parent::load($data);
        if ($ret) {
            $this->table()->set(
                "Driver",
                \HUGnet\devices\Driver::getDriver(
                    $this->table()->get("HWPartNum"),
                    $this->table()->get("FWPartNum"),
                    $this->table()->get("FWVersion")
                )
            );
        }
        return $ret;
    }
    /**
    * Decodes the sensor data
    *
    * @param string $string  The string of sensor data
    * @param string $command The command that was used to get the data
    * @param float  $deltaT  The time difference between this packet and the next
    * @param float  $prev    The previous record
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function decodeData($string, $command="", $deltaT = 0, $prev = null)
    {
        $data = $this->driver()->decodeSensorString((string)$string);
        $ret = array(
            "deltaT" => $deltaT,
            "DataIndex" => $data["DataIndex"],
            "timeConstant" => $data["timeConstant"],
            "rawData" => $string,
        );
        $sensors = $this->get("InputTables");

        for ($i = 0; $i < $sensors; $i++) {
            $ret = array_merge(
                $ret,
                (array)$this->input($i)->decodeData(
                    $data["String"], $deltaT, $prev[$i], $ret
                )
            );
        }
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @param array $data    The data to build the history record with.
    * @param bool  $history History if true, average if false
    *
    * @return string
    */
    public function &historyFactory($data = array(), $history = true)
    {
        $class = $this->driver()->historyTable($history);
        $obj = $this->system()->table($class, $data);
        $obj->device = &$this;
        return $obj;
    }
    /**
    * This gets the roles
    *
    * @return Object The role object
    */
    private function &_role()
    {
        if (!is_object($this->_role)) {
            include_once dirname(__FILE__)."/../devices/Role.php";
            $this->_role = \HUGnet\devices\Role::factory();
        }
        return $this->_role;
    }
    /**
    * This creates the sensor drivers
    *
    * @param int $sid The sensor id to get.  They are labaled 0 to sensors
    *
    * @return null
    */
    public function &input($sid)
    {
        $role = $this->get("Role");
        if (!empty($role)) {
            $info = $this->_role()->input($role, $sid);
            if (is_array($info)) {
                unset($info["data"]["dev"]);
                unset($info["data"]["input"]);
                include_once dirname(__FILE__)."/../devices/Input.php";
                $system = $this->system();
                $ret = \HUGnet\devices\Input::factory(
                    $system,
                    array("dev" => $this->id(), "input" => $sid),
                    null,
                    $this,
                    (array)$info["table"]
                );
                $this->_fixIOP($ret, $info["data"]);
                return $ret;
            }
        }

        return $this->driver()->input($sid);
    }
    /**
    * This creates the sensor drivers
    *
    * @param int $sid The sensor id to get.  They are labaled 0 to sensors
    *
    * @return null
    */
    public function &output($sid)
    {
        $role = $this->get("Role");
        if (!empty($role)) {
            $info = $this->_role()->output($role, $sid);
            if (is_array($info)) {
                include_once dirname(__FILE__)."/../devices/Output.php";
                unset($info["data"]["dev"]);
                unset($info["data"]["output"]);
                $system = $this->system();
                $ret = \HUGnet\devices\Output::factory(
                    $system,
                    array("dev" => $this->id(), "output" => $sid),
                    null,
                    $this,
                    (array)$info["table"]
                );
                $this->_fixIOP($ret, $info["data"]);
                return $ret;
            }
        }

        return $this->driver()->output($sid);
    }
    /**
    * This creates the sensor drivers
    *
    * @param int $sid The sensor id to get.  They are labaled 0 to sensors
    *
    * @return null
    */
    public function &process($sid)
    {
        $role = $this->get("Role");
        if (!empty($role)) {
            $info = $this->_role()->process($role, $sid);
            if (is_array($info)) {
                include_once dirname(__FILE__)."/../devices/Process.php";
                unset($info["data"]["dev"]);
                unset($info["data"]["process"]);
                $system = $this->system();
                $ret = \HUGnet\devices\Process::factory(
                    $system,
                    array("dev" => $this->id(), "process" => $sid),
                    null,
                    $this,
                    (array)$info["table"]
                );
                $this->_fixIOP($ret, $info["data"]);
                return $ret;
            }
        }

        return $this->driver()->process($sid);
    }
    /**
    * This fixes the IOP object
    *
    * @param object &$iop The IOP object to fix
    * @param array  $data The data to fix the object with
    *
    * @return null
    */
    private function _fixIOP(&$iop, $data)
    {
        foreach (array("extra", "location") as $field) {
            if (isset($data[$field])) {
                $iop->mix($field, $data[$field]);
                unset($data[$field]);
            }
        }
        foreach ($data as $field => $value) {
            $iop->set($field, $value);
        }
    }
    /**
    * Returns the devices XML file as an array
    *
    * @param mixed $obsolete Bool true for yes, bool false for no, anything
    *                        else for both
    *
    * @return array
    */
    public function getHardwareTypes($obsolete=0)
    {
        $xml = simplexml_load_file(dirname(__FILE__).'/../devices.xml');
        $devs = array();
        foreach ($xml->endpoints as $d) {
            $data = get_object_vars($d);
            if ((int)$data["Obsolete"] == (int)$obsolete) {
                foreach (explode("\n", $data['Parameters']) as $val) {
                    $value = explode(":", $val);
                    $key = trim($value[0]);
                    $value = trim($value[1]);
                    if (!empty($key)) {
                        $data['Param'][$key] = $value;
                    }
                }
                $devs[] = $data;

            }
        }
        return $devs;
    }

}


?>
