<?php
/**
 * Main sensor driver.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2009 Hunt Utilities Group, LLC
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
 * @category   Sensors
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
// Get the base class for the sensors
require_once dirname(__FILE__)."/base/SensorBase.php";

/**
 * This class controls all of the sensors.  When given the right information
 * it will decode data from any of the sensors that it has information on.
 * 
 * @category   Sensors
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Sensor
{
    /**
     * This registers the sensor Plugins so we know what code we have available.
     *
     * @param object &$plugins This is a object of type plugin
     * 
     * @see plugin
      */
    function __construct(&$plugins = "") 
    {
        if (!is_object($plugins)) {
            if (!isset($_SESSION["incdir"])) {
                $_SESSION["incdir"] = dirname(__FILE__)."/";
            }
            $plugins = new Plugins(dirname(__FILE__)."/drivers/", "php");
        }

        if (is_array($plugins->plugins["Generic"]["sensor"])) {
            foreach ($plugins->plugins["Generic"]["sensor"] as $driver) {
                $this->registerSensor($driver["Class"]);
            }
        }
    }

    /**
     * Register a sensor class.
     *
     * @param mixed  $class The name of the sensor class to register, 
     *                  or the actual object
     * @param string $name  The name of the class if the above is an object.
     *
     * @return bool true on success, false on failure
      */
    public function registerSensor($class, $name=false) 
    {
        if (is_string($class) && class_exists($class)) {
            $this->sensors[$class] = new $class();
        } else if (is_object($class)) {
            if (empty($name)) $name = get_class($class);
            $this->sensors[$name] = $class;
            $class                = $name;
        } else {
            return false;
        }
        if (is_array($this->sensors[$class]->sensors)) {
            foreach ($this->sensors[$class]->sensors as $type => $sInfo) {
                foreach ($sInfo as $sensor => $val) {
                    $this->dev[$type][$sensor] = $class;
                }
                if (!isset($this->dev[$type]['default'])) {
                    $this->dev[$type]['default'] = $class;
                }
            }
            return true;
        } else {
            return false;
        }
    
    }
    /**
     * This is the generic function to get a sensor reading crunched into its correct
     * and useful value.
     * 
     * Returns the value it was given if it doesn't know how to deal with the sensor
     * specified.
     * 
     * @param mixed  $val     The raw reading
     * @param int    $type    Int The type of sensor
     * @param string &$sensor The short name of the sensor
     * @param int    $TC      The time constant
     *
     * @return mixed The cruched reading.
      */
    function getReading($val, $type, &$sensor, $TC) 
    {
        $class = $this->getClass($type, $sensor);
        if (is_object($class)) {
            $args = func_get_args();
            $args[1]; // Remove the $type
            unset($args[2]); // Remove the $sensor
            $stuff = $class->sensors[$type][$sensor];
            if (isset($stuff['mult'])) $val *= $stuff['mult'];
            $args[0] = $val;
            $args[1] = $stuff;
            $args    = array_merge($args); // Compacts the array
            $val     = $this->runFunction($class, $stuff['function'], $args, $args[0]);
        }
        return $val;
    }

    /**
     * Runs teh specified function or if that function doesn't exist returns
     * whatever was specified in the return paramter.
     *
     * @param object &$class   Reference to the class the function is a method of
     * @param string $function the name of the function to call
     * @param array  &$args    The arguments to send to that function
     * @param mixed  $return   The default return value
     *
     * @return mixed The return value of the function
      */
    function runFunction(&$class, $function, &$args, $return = null) 
    {
        if (is_string($function)) {
            if (method_exists($class, $function)) {
                $fct    = array(&$class, $function);
                $return = call_user_func_array($fct, $args);
            }
        }
        return $return;
    }

    /**
     *  Finds the class for the sensor given.
     *
     *  Returns null if no class is found.
     *
     *  This function will modify and correct the type and sensor
     *  arguments given.  It is done here because it makes sense since
     *  this function is finding the class to use
     * 
     * @param int    &$type   The type of sensor
     * @param string &$sensor The short name of the sensor
     *
     * @return object A reference to the sensor object
      */
    function &getClass(&$type, &$sensor) 
    {
        $type   = (int) $type;
        $sensor = (string) $sensor;
        $class  = $this->dev[$type][$sensor];
        if (is_null($class)) {
            if (is_array($this->dev[$type])) {
                reset($this->dev[$type]);
                $sensor = key($this->dev[$type]);
                $class  = current($this->dev[$type]);
            }
        }
        return $this->sensors[$class];    
    }

    /**
     * Checks the units given for validity based on the sensor and type given
     *
     * If the unit is valid it will be returned.  If it is not valid, 
     * the storageUnit will be returned instead.
     *
     * This function can be used to get the storageUnit for a device by not including
     * the unit parameter.
     *
     *  It will return null if the sensor doesn't exist.
     *
     * @param int    $type    Int The type of sensor
     * @param string &$sensor The short name of the sensor
     * @param string $unit    The unit to check.
     *
     * @return string A valid unit for the sensor given.
      */
    function getUnits($type, &$sensor, $unit = false) 
    {
        $return = null;
        $class  = $this->getClass($type, $sensor);
        if (is_object($class)) {
            if (array_search($unit, $class->sensors[$type][$sensor]['validUnits']) !== false) {
                $return = $unit;
            } else {
                $return = $class->sensors[$type][$sensor]['storageUnit'];
            }
        }
        return $return;
    }

    /**
     * Gets the information on the extra parameters that go with a sensor.
     *
     * This returns an array of the form:
     * <code>
     * array(
     *  0 => array(
     *    'text' => "Text description of this field",
     *    'default' => "default",
     * ),
     *);
     * </code>
     *
     * This array will have as many members as there are extra fields for that
     * sensor.
     *
     * @param int    $type    The type of sensor
     * @param string &$sensor The short name of the sensor
     *
     * @return array Array of information about the extra parameters of the sensor
      */
    function getExtra($type, &$sensor) 
    {
        $return = array();
        $class  = $this->getClass($type, $sensor);
        if (is_object($class)) {
            if (is_array($class->sensors[$type][$sensor]['extraText'])) {
                foreach ($class->sensors[$type][$sensor]['extraText'] as $key => $val) {
                    $return[$key]['text']    = $val;
                    $return[$key]['default'] = $class->sensors[$type][$sensor]['extraDefault'][$key];
                }
            } else if (isset($class->sensors[$type][$sensor]['extraText'])) {
                $return[0]['text']    = $class->sensors[$type][$sensor]['extraText'];
                $return[0]['default'] = $class->sensors[$type][$sensor]['extraDefault'];
            }
        }
        return $return;
    }

    /**
     * Returns the text name for the type of thing the sensor is measuring.
     *
     * A temperature sensor would return "Temperature".
     *
     * Returns null if no sensor is found.
     *
     * @param int    $type    Int The type of sensor
     * @param string &$sensor The short name of the sensor
     *
     * @return string The name of what the sensor is measuring.
      */
    function getUnitType($type, &$sensor) 
    {
        $return = null;
        $class  = $this->getClass($type, $sensor);
        if (is_object($class)) {
            $return = $class->sensors[$type][$sensor]['unitType'];
        }
        return $return;
    }

    /**
     * Returns how many inputs a sensor takes
     *
     * Most sensors will return 1, so it assumes the value
     * is 1 if the sensor doesn't specify this value.
     *
     * Returns 1 if no sensor is found.
     *
     * @param int    $type   The type of sensor
     * @param string $sensor The short name of the sensor
     *
     * @return int The number of inputs on the endpoint that this sensor takes.
      */
    function getSize($type, $sensor) 
    {
        $return = 1;
        $class  = $this->getClass($type, $sensor);
        if (is_object($class)) {
            if (isset($class->sensors[$type][$sensor]['inputSize'])) {
                $return = $class->sensors[$type][$sensor]['inputSize'];
            }
        }
        if ($return < 1) $return = 1;
        return $return;
    }

    /**
     * Check if we should total the readings instead of averaging them.
     *
     * @param int    $type   Int The type of sensor
     * @param string $sensor The short name of the sensor
     *
     * @return bool Whether to total instead of average.  Returns false by default
      */
    function doTotal($type, $sensor) 
    {
        $return = false;
        $class  = $this->getClass($type, $sensor);
        if (is_object($class)) {
            $return = (bool) $class->sensors[$type][$sensor]['doTotal'];
        }
        return $return;
    }

    /**
     * Check to see if a mode is valid.
     *
     *  If the mode is valid it is returned.  Otherwise a valid mode
     *  is found and returned.
     *
     * @param int    $type    The type of sensor
     * @param string &$sensor The short name of the sensor
     * @param string $unit    The unit to check
     * @param string $mode    The mode to check
     *
     * @return string A valid mode for the sensor given.
      */
    function getUnitMode($type, &$sensor, $unit=null, $mode=false) 
    {
        $return = array();
        $class  = $this->getClass($type, $sensor);
        if (is_object($class)) {
            if (is_null($unit)) {
                $return = $class->sensors[$type][$sensor]['unitModes'];
                if (is_array($return)) {
                    foreach ($return as $key => $val) {
                        $return[$key] = explode(",", $val);
                    }
                }
            } else {
                $return = $class->sensors[$type][$sensor]['unitModes'][$unit];
                $return = explode(",", $return);
                if ($mode !== false) {
                    if ($mode == "ignore") {
                        $return = $mode;
                    } else if (array_search($mode, $return) !== false) {
                        $return = $mode;
                    } else {
                        $return = $return[0];
                    }
                }
            }
        }
        // We don't want to return an invalid value, so return ignore if 
        // we don't get a valid value to return.
        if (empty($return)) $return = "ignore";
        return $return;
    }

    /**
     * Get the default units for a sensor
     *
     * @param int    $type    The type of sensor
     * @param string &$sensor The short name of the sensor
     * @param string $unit    The unit to check
     *
     * @return string
      */
    function getUnitDefMode($type, &$sensor, $unit) 
    {
        $return = $this->getUnitMode($type, $sensor, $unit);
        return $return[0];
    }

    /**
     * Get all possible units for this sensor.
     *
     * @param int    $type    The type of sensor
     * @param string &$sensor The short name of the sensor
     *
     * @return array
      */
    function getAllUnits($type, &$sensor) 
    {
        $return = array();
        $class  = $this->getClass($type, $sensor);
        if (is_object($class)) {
            $return = $class->sensors[$type][$sensor]['validUnits'];
        }
        return $return;
    }


    /**
     * Gets all possible sensors for a sensor type
     *
     * @param int $type Int The type of sensor
     *
     * @return array
      */
    function getAllSensors($type) 
    {
        $type    = (int)$type;
        $sensors = array();
        $done    = array();
        if (is_array($this->dev[$type])) {
            foreach ($this->dev[$type] as $key => $class) {
                if ($done[$class] !== true) {
                    $s = $this->sensors[$class]->sensors[$type];
                    if (is_array($s)) $sensors = array_merge($sensors, $s);
                    $done[$class] = true;
                }
            }
        }    

        return $sensors;
    }
    /**
     * Gets all possible sensors for a sensor type
     *
     * @param int $type Int The type of sensor
     *
     * @return array
      */
    function getSensor($type, $sensor) 
    {
        $type    = (int)$type;
        $class  = $this->getClass($type, $sensor);
        if (is_object($class)) return $class->sensors[$type][$sensor];
        return null;
    }

    /**
     * Check to see if an array of units is valid
     *
     *  This checks each unit in the array.  It expects all
     *  of its parameters to be arrays and they all have to
     *  be indexed identically.  It works directly on the arrays
     *  given and can modify any of them.
     *
     * @param array &$type   The type of sensor
     * @param array &$sensor The short name of the sensor
     * @param array &$units  The unit to check
     * @param array &$mode   The mode to check
     *
     * @return null
      */
    function checkUnits(&$type, &$sensor, &$units, &$mode) 
    {
        if (is_array($type)) {
            if (!is_array($units)) $units = array();
            if (!is_array($mode)) $mode = array();
            if (!is_array($sensor)) $sensor = array();
        
            $skip = 0;
            foreach ($type as $key => $value) {                
                $units[$key] = $this->getUnits($type[$key], $sensor[$key], $units[$key]);
                $mode[$key]  = $this->getUnitMode($type[$key], $sensor[$key], $units[$key], $mode[$key]);
            }
        } else {
            if (is_array($sensor)) unset($sensor);
            $units = $this->getUnits($type, $sensor, $units);
            $mode  = $this->getUnitMode($type, $sensor, $units, $mode);           
        }
    }

    /**
     *  Decodes the data given based on the device information.
     *
     *  This routine does the meat of the sensor decode.  It takes
     *  raw data from devices and turns it into numbers with units
     *  and data types.  It will change records into differential if
     *  that is required by whatever sensor is reading the data.
     *
     *  This requires that there is an array of integers in the data array
     *  called data['raw'].  This should be indexed numerically according to
     *  the sensors on the particular device specified in the Info array.
     *
     *  This returns the data as data['DataX'] and data['data'][X], where X is
     *  the sensor number.
     *
     * @param array &$Info The device information array for this device
     * @param array &$data The data to decode
     *
     * @return null
      */
    function decodeData(&$Info, &$data) 
    {
        if (!isset($data["Date"])) $data["Date"] = date("Y-m-d H:i:s");
        if (is_array($data["raw"])) {
            $skip = 0;
            foreach ($data["raw"] as $rawkey => $rawval) {
                // This is so we skip useless data points.  If a sensor 
                // takes more than one input then skip the extra ones.
                if ($skip > 0) {
                    unset($data["Data".$rawkey]);
                    unset($data["data"][$rawkey]);
                    $skip--;
                    continue;
                }
                $skip = $this->getSize($data["Types"][$rawkey], 
                                       $Info['params']['sensorType'][$rawkey]) - 1;

                if (is_null($data['Units'][$rawkey])) {
                    $data['Units'][$rawkey] = $this->getUnits($data["Types"][$rawkey], 
                                                              $Info['params']['sensorType'][$rawkey]);
                }
                $data['dType'][$rawkey]    = $this->getUnitMode($data["Types"][$rawkey], $Info['params']['sensorType'][$rawkey], $data['Units'][$rawkey], $Info['params']['dType'][$rawkey]);
                $data['unitType'][$rawkey] = $this->getUnitType($data["Types"][$rawkey], $Info['params']['sensorType'][$rawkey]);
                if ($data['dType'][$rawkey] == 'diff') {
                    if (isset($this->lastRecord[$data['DeviceKey']])) {
                        if (!isset($data['deltaT'])) {
                            $data['deltaT'] =  strtotime($data['Date']) - strtotime($this->lastRecord[$data['DeviceKey']]['Date']);
                        }
                        $newraw = $rawval - $this->lastRecord[$data['DeviceKey']]["raw"][$rawkey];
                        if ($data['deltaT'] < 0) $newraw = abs($newraw);
                        $data["Data".$rawkey] = $this->getReading($newraw,
                                                    $data["Types"][$rawkey], 
                                                    $Info['params']["sensorType"][$rawkey], 
                                                    $data["TimeConstant"], 
                                                    $Info['params']['Extra'][$rawkey], 
                                                    $data['deltaT']);
                    } else {
                        unset($data["Data".$rawkey]);
                        unset($data["data"][$rawkey]);
                    }
                } else {
                    $data["Data".$rawkey] = $this->getReading($rawval, $data["Types"][$rawkey], $Info['params']["sensorType"][$rawkey], $data["TimeConstant"], $Info['params']['Extra'][$rawkey], $data["deltaT"]);
                }
                $data["data"][$rawkey] = $data["Data".$rawkey];
            }
            $data['deltaT']                       = abs($data['deltaT']);
            $this->lastRecord[$data['DeviceKey']] = $data;
            $this->checkRecord($data);
        }
    }

    /**
     * Checks a record based on the sensor information.
     *
     * @param array &$data The data to decode
     *
     * @return null
      */
    function checkRecord(&$data) 
    {
        // Start with the assumption that the packet is good.
        if (!isset($data["Status"])) $data["Status"] = "GOOD";

        // Check each reading.
        for ($i = 0; $i < $data['ActiveSensors']; $i++) {
            if (!$this->checkPoint($data['Data'.$i], $data['Types'][$i], $data['params']['sensorType'][$i], $data['Units'][$i], $data['dType'][$i])) {
                $data['Data'.$i]  = null;
                $data['data'][$i] = null;
            }
        }
    }

    /**
     * Check to see if a data point is valid
     *
     *  This uses the check function in the sensor information to check
     *  to see if a reading is valid.
     *
     * @param mixed  $value  The value to check
     * @param int    $type   The type of sensor
     * @param string $sensor The short name of the sensor
     * @param string $mode   The mode
     *
     * @return bool true if the record is good, false if it is not
      */
    function checkPoint($value, $type, $sensor, $mode) 
    {
        $ret   = true;
        $class = $this->getClass($type, $sensor);
        if (is_object($class)) {
            $args    = func_get_args();
            $args[1] = $class->sensors[$type][$sensor]; // This overwrites the type
            unset($args[2]); // Remove the $sensor
            $stuff = $class->sensors[$type][$sensor];
            $ret   = $this->runFunction($class, $stuff['checkFunction'], $args, true);
        }
        return $ret;
    }
}


?>
