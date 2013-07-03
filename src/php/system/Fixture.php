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
/* THis is our outputs */
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
        $import["inputs"] = array();
        for ($i = 0; $i < $dev->get("InputTables"); $i++) {
            $import["inputs"][$i] = $this->_importIOP($dev->input($i));
        }
        $import["outputs"] = array();
        for ($i = 0; $i < $dev->get("OutputTables"); $i++) {
            $import["outputs"][$i] = $this->_importIOP($dev->output($i));
        }
        $import["processes"] = array();
        for ($i = 0; $i < $dev->get("ProcessTables"); $i++) {
            $import["processes"][$i] = $this->_importIOP($dev->process($i));
        }
        return json_encode($import);
    }
    /**
    * This builds the class from a setup string
    *
    * @param mixed $iop This an input, output, or process record
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
            $import["data"] = $data;
            $import["table"] = $iop->toArray("entryonly");
        } else {
            $import["data"] = array("id" => 0xFF);
        }
        return $import;
        
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
    * This creates the sensor drivers
    *
    * @param int $sid The sensor id to get.  They are labaled 0 to sensors
    *
    * @return null
    */
    public function &input($sid)
    {
        $input = $this->get("inputs");
        include_once dirname(__FILE__)."/../devices/Input.php";
        $system = $this->system();
        $ret = \HUGnet\devices\Input::factory(
            $system,
            (array)$input[$sid]["data"],
            null,
            $this,
            (array)$input[$sid]["table"]
        );
        return $ret;
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
        $output = $this->get("outputs");
        include_once dirname(__FILE__)."/../devices/Output.php";
        $system = $this->system();
        $ret = \HUGnet\devices\Output::factory(
            $system,
            (array)$output[$sid]["data"],
            null,
            $this,
            (array)$output[$sid]["table"]
        );
        return $ret;
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
        $proc = $this->get("processes");
        include_once dirname(__FILE__)."/../devices/Process.php";
        $system = $this->system();
        $ret = \HUGnet\devices\Process::factory(
            $system,
            $proc[$sid]["data"],
            null,
            $this,
            (array)$proc[$sid]["table"]
        );
        return $ret;
    }

}


?>
