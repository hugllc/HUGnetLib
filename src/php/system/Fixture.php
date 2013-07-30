<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.13.0
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.13.0
 */
class Fixture extends \HUGnet\Device
    implements \HUGnet\interfaces\SystemInterface
{
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
    * Gets a value
    *
    * @param string $field the field to get
    *
    * @return null
    */
    public function get($field)
    {
        $fixture = json_decode($this->table()->get('fixture'), true);
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
        return json_decode($this->table()->get('fixture'), true);
    }
    /**
    * This takes the class and makes it into a setup string
    *
    * @return string The encoded string
    */
    public function export()
    {
        return $this->table()->get('fixture');
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
            $fixture = json_decode($data, true);
            if (isset($fixture["id"])) {
                $this->table()->set("dev", $fixture["id"]);
                $this->table()->set("fixture", $data);
                $now = $this->system()->now();
                $this->table()->set("created", $now);
                $this->table()->set("modified", $now);
            } else {
                return false;
            }
        }
        return parent::store();
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
        $import = $dev->toArray(false);
        unset($import["localParams"]);
        unset($import["group"]);
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
        return json_encode($import);
    }
    /**
    * This builds and stores an actual device from the fixture
    *
    * @return object The device that I am exporting
    */
    public function &exportDevice()
    {
        $dev = $this->system()->device();
        $data = json_decode($this->table()->get('fixture'), true);
        $dev->table()->fromArray($data);
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
        $data = $iop->toArray(false);
        $import = array();
        if (isset($data["id"])) {
            unset($data["RawSetup"]);
            unset($data["group"]);
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
        $system = $this->system();
        $class = "\\HUGnet\\devices\\".$class;
        $ret = $class::factory($system, $iop, null, $this);
        $ret->table()->readonly();
        return $ret;
    }

}


?>
