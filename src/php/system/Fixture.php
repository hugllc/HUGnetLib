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
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/Device.php";
/** This is our base class */
require_once dirname(__FILE__)."/../interfaces/WebAPI.php";
/** This is our system interface */
require_once dirname(__FILE__)."/../interfaces/SystemInterface.php";
/* THis is our output */
require_once dirname(__FILE__)."/../devices/Output.php";

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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.13.0
 */
class Fixture extends \HUGnet\Device
    implements \HUGnet\interfaces\SystemInterface
{
    /** @var int The database table class to use */
    protected $tableClass = "Fixtures";
    /** This is our url */
    protected $url = "/fixture";
    /**
    * This is the destructor
    */
    public function __destruct()
    {
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
    public static function &factory(&$system, $data=null, $table="Fixtures")
    {
        $object = parent::factory($system, $data, $table);
        return $object;
    }
    /**
    * Lists the ids of the table values
    *
    * @return int The ID of this device
    *
    * @SuppressWarnings(PHPMD.ShortMethodName)
    */
    public function id()
    {
        return $this->table()->get("dev");
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
        $fixture = json_decode($this->table()->get('fixture'), true);
        if ($field == "id") {
            $fixture["id"] = $this->table()->get("dev");
        } else if ($field == "DeviceID") {
            $fixture["DeviceID"] = sprintf("%06X", $this->table()->get("dev"));
        }
        return $fixture[$field];
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
        $params = $this->get("params");
        return $params[$field];
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
        // There are no local parameters 
        return null;
    }
    /**
    * Sets a value
    *
    * @param string $field the field to set
    * @param mixed  $value the value to set
    *
    * @return null
    */
    public function set($field, $value)
    {
        return $value;
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
        // There is no setting of parameters
        return $value;
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
        // There is no setting of parameters
        return $value;
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
        $fixture = (array)json_decode($this->table()->get('fixture'), true);
        $id = array(
            "id" => $this->table()->get("dev"),
            "DeviceID" => sprintf("%06X", $this->table()->get("dev"))
        );
        $fixture = $id + $fixture;
        return $fixture;
    }
    /**
    * This takes the class and makes it into a setup string
    *
    * @param bool $default Whether or not to push out the default values
    *
    * @return string The encoded string
    */
    public function export($default = false)
    {
        $fixture = $this->toArray($default);
        unset($fixture["DeviceID"]);
        return json_encode($fixture);
    }
    /**
    * This builds the class from a setup string
    *
    * @param mixed $data This could be a string, or a device record.
    *
    * @return bool True on success, false on failure
    */
    public function import($data)
    {
        if (is_object($data) && is_a($data, "\HUGnet\Device")) {
            $data = $this->_importDevice($data);
        }
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        if (is_array($data) && isset($data["id"])) {
            $this->table()->clearData();
            $this->table()->set("dev", $data["id"]);
            unset($data["id"]);
            unset($data["DeviceID"]);
            $this->table()->set("fixture", json_encode($data));
            $now = $this->system()->now();
            $this->table()->set("created", $now);
            $this->table()->set("modified", $now);
            return true;
        }
        return false;
    }
    /**
    * Stores data into the database
    *
    * @param bool $replace Replace any record that is in the way
    *
    * @return null
    */
    public function store($replace = false)
    {
        // Doesn't actually save anything.
        return true;
    }
    /**
    * This builds the class from a setup string
    *
    * @param mixed &$dev This a device record
    *
    * @return bool True on success, false on failure
    */
    private function _importDevice(&$dev)
    {
        $import = $dev->table()->toArray(true);
        unset($import["localParams"]);
        unset($import["group"]);
        $arrays = array("params", "dataChannels", "controlChannels");
        foreach ($arrays as $key) {
            if (is_string($import[$key])) {
                $import[$key] = (array)json_decode($import[$key], true);
            }
        }
        
        $import["input"] = array();
        for ($i = 0; $i < $dev->get("InputTables"); $i++) {
            $import["input"][$i] = $this->_importIOP($dev->input($i));
        }
        $import["output"] = array();
        for ($i = 0; $i < $dev->get("OutputTables"); $i++) {
            $import["output"][$i] = $this->_importIOP($dev->output($i));
        }
        $import["process"] = array();
        for ($i = 0; $i < $dev->get("ProcessTables"); $i++) {
            $import["process"][$i] = $this->_importIOP($dev->process($i));
        }
        $power = $dev->get("PowerTables");
        if ($power > 0) {
            $import["power"] = array();
            for ($i = 0; $i < $power; $i++) {
                $import["power"][$i] = $this->_importIOP($dev->power($i));
            }
        }
        return $import;
    }
    /**
    * This builds and stores an actual device from the fixture
    *
    * @return object The device that I am exporting
    */
    public function &exportDevice()
    {
        return $this->mergeDevice(true);
    }
    /**
    * This builds and stores an actual device from the fixture
    *
    * @param bool $replace If true, this totally replaces the old device
    *
    * @return object The device that I am exporting
    */
    public function &mergeDevice($replace = false)
    {
        $dev = $this->system()->device();
        $dev->table()->clearData();
        $id = $this->table()->get("dev");
        if (!$replace) {
            $dev->table()->getRow($id);
        }
        $data = json_decode($this->table()->get('fixture'), true);
        $dev->set("id", $id);
        $dev->set("DeviceID", sprintf("%06X", $id));
        $params = (array)$data["params"];
        unset($data["params"]);
        unset($data["localParams"]);
        $dev->table()->fromArray($data);
        foreach ($params as $key => $value) {
            $dev->setParam($key, $value);
        }
        $dev->table()->insertRow(true);
        /* Now do the iopTables */
        $input = $dev->input(0);
        for ($i = 0; $i < $dev->get("InputTables"); $i++) {
            $input->table()->clearData();
            $input->table()->fromArray($data["input"][$i]);
            $input->table()->set("dev", $dev->get("id"));
            $input->table()->set("input", $i);
            $input->table()->insertRow(true);
        }
        $output = $dev->output(0);
        for ($i = 0; $i < $dev->get("OutputTables"); $i++) {
            $output->table()->clearData();
            $output->table()->fromArray($data["output"][$i]);
            $output->table()->set("dev", $dev->get("id"));
            $output->table()->set("output", $i);
            $output->table()->insertRow(true);
        }
        $proc = $dev->process(0);
        for ($i = 0; $i < $dev->get("ProcessTables"); $i++) {
            $proc->table()->clearData();
            $proc->table()->fromArray($data["process"][$i]);
            $proc->table()->set("dev", $dev->get("id"));
            $proc->table()->set("process", $i);
            $proc->table()->insertRow(true);
        }
        for ($i = 0; $i < $dev->get("PowerTables"); $i++) {
            $proc->table()->clearData();
            $proc->table()->fromArray($data["power"][$i]);
            $proc->table()->set("dev", $dev->get("id"));
            $proc->table()->set("power", $i);
            $proc->table()->insertRow(true);
        }
        return $dev;
    }
    /**
    * This builds the class from a setup string
    *
    * @param mixed &$iop This an input, output, or process record
    *
    * @return bool True on success, false on failure
    */
    private function _importIOP(&$iop)
    {
        $import = array();
        $data = $iop->table()->toArray(true);
        $import = array();
        if ($data["id"] != 0xFF) {
            unset($data["RawSetup"]);
            unset($data["group"]);
            unset($data["dev"]);
            unset($data["input"]);
            unset($data["output"]);
            unset($data["process"]);
            unset($data["power"]);
            $arrays = array("params", "tableEntry");
            foreach ($arrays as $key) {
                if (is_string($data[$key])) {
                    $data[$key] = (array)json_decode($data[$key], true);
                }
            }
            $import = $data;
        } else {
            $import = array("id" => 0xFF);
        }
        return $import;
        
    }
    /**
    * This creates the input object
    *
    * @param int $sid The input id to get.  They are labeled 0 to InputTables
    *
    * @return object The input object
    */
    public function &input($sid)
    {
        return $this->_iop($sid, "input");
    }
    /**
    * This creates the output object
    *
    * @param int $sid The output id to get.  They are labeled 0 to OutputTables
    *
    * @return object The output object
    */
    public function &output($sid)
    {
        return $this->_iop($sid, "output");
    }
    /**
    * This creates the process object
    *
    * @param int $sid The process id to get.  They are labeled 0 to ProcessTables
    *
    * @return object The process object
    */
    public function &process($sid)
    {
        return $this->_iop($sid, "process");
    }
    /**
    * This creates the process object
    *
    * @param int $sid The process id to get.  They are labeled 0 to ProcessTables
    *
    * @return object The process object
    */
    public function &power($sid)
    {
        return $this->_iop($sid, "power");
    }
    /**
    * This creates the process object
    *
    * @param int    $sid  The process id to get.  They are labeled 0 to xTables
    * @param string $type The type of iop to get
    *
    * @return object The process object
    */
    private function &_iop($sid, $type)
    {
        $iops = $this->get($type);
        $class = ucfirst($type);
        include_once dirname(__FILE__)."/../devices/".$class.".php";
        $iop  = (array)$iops[$sid];
        $iop["group"] = "null";
        $iop["dev"] = $this->table()->get("dev");
        $iop[$type] = $sid;
        $system = $this->system();
        $class = "\\HUGnet\\devices\\".$class;
        $ret = $class::factory($system, $iop, null, $this);
        $ret->table()->readonly();
        return $ret;
    }

}


?>
