<?php
/**
	$Id$
	@file sensors/resistive.inc.php
	@brief Class for dealing with resistive sensors
	
	
*/

class sensor {

	function __construct(&$plugins = "") {
		if (!is_object($plugins)) {
			if (!isset($_SESSION["incdir"])) $_SESSION["incdir"] = dirname(__FILE__)."/";
			$plugins = new plugins(dirname(__FILE__)."/plugins/", "inc.php");
		}

		foreach($plugins->plugins["Generic"]["sensor"] as $driver) {
			if (class_exists($driver["Class"])) {
				$class = $driver["Class"];
				$this->sensors[$class] = new $class();
				if (is_array($this->sensors[$class]->sensors)) {
					foreach($this->sensors[$class]->sensors as $type => $sInfo) {
						foreach($sInfo as $sensor => $val) {
							$this->dev[$type][$sensor] = $class;
						}
						if (!isset($this->dev[$type]['default'])) $this->dev[$type]['default'] = $class;
					}
				}
			}
		}
	}

	/**
		@public
		@brief Return the voltage
		@param $R Float The current resistance of the thermistor
		@param $type Int The type of sensor.
		@return Sensor value	
	
		@par Introduction
		This function 

	
	*/
	function getReading($val, $type, $sensor=NULL) 
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
            $args = array_merge($args); // Compacts the array
            $val = $this->runFunction($class, $stuff['function'], $args, $args[0]);
        }
        return($val);
	}

    /**
        Returns the class
        $return is the default sent to it.
    */
    function runFunction(&$class, $function, &$args, $return = NULL) {
        if (isset($function)) {
            if (method_exists($class, $function)) {
                $fct = array(&$class, $function);
                $return = call_user_func_array($fct, $args);
            }
        }
        return $return;
    }

    /**
        Returns the class
    */
    function &getClass(&$type, &$sensor) {
        $type = (int) $type;
        $sensor = (string) $sensor;
        $class = $this->dev[$type][$sensor];
        if (is_null($class)) {
            if (is_array($this->dev[$type])) {
                reset($this->dev[$type]);
                $sensor = key($this->dev[$type]);
                $class = current($this->dev[$type]);
            }            
//            $class = $this->dev[$type]['default'];
        }
        return $this->sensors[$class];    
    }
    /**
        Returns the default units for this type of sensor
    */
	function getUnits($type, $sensor, $unit = FALSE) 
	{
        $return = NULL;
	    $class = $this->getClass($type, $sensor);
        if (is_object($class)) {
            if (array_search($unit, $class->sensors[$type][$sensor]['validUnits']) !== FALSE) {
                $return = $unit;
            } else {
                $return = $class->sensors[$type][$sensor]['storageUnit'];
                if (is_null($return)) {
                    reset($class->sensors[$type][$sensor]['validUnits']);
                    $return = current($class->sensors[$type][$sensor]['validUnits']);
                }
            }
        }
		return $return;
	}
    /**
        Returns the default units for this type of sensor
    */
	function getExtra($type, $sensor) 
	{
        $return = array();
	    $class = $this->getClass($type, $sensor);
        if (is_object($class)) {
            if (is_array($class->sensors[$type][$sensor]['extraText'])) {
                foreach($class->sensors[$type][$sensor]['extraText'] as $key => $val) {
                    $return[$key]['text'] = $val;
                    $return[$key]['default'] = $class->sensors[$type][$sensor]['extraDefault'][$key];
                }
            } else if (isset($class->sensors[$type][$sensor]['extraText'])) {
                $return[0]['text'] = $class->sensors[$type][$sensor]['extraText'];
                $return[0]['default'] = $class->sensors[$type][$sensor]['extraDefault'];
            }
        }
		return $return;
	}
    /**
        Returns the default units for this type of sensor
    */
	function getUnitType($type, $sensor) 
	{
        $return = NULL;
	    $class = $this->getClass($type, $sensor);
        if (is_object($class)) {
            $return = $class->sensors[$type][$sensor]['unitType'];
        }
		return $return;
	}
    /**
        Returns the default units for this type of sensor
    */
	function getSize($type, $sensor) 
	{
        $return = 1;
	    $class = $this->getClass($type, $sensor);
        if (is_object($class)) {
            if (isset($class->sensors[$type][$sensor]['inputSize'])) {
                $return = $class->sensors[$type][$sensor]['inputSize'];
            }
        }
        if ($return < 1) $return = 1;
		return $return;
	}
    /**
        Returns the default units for this type of sensor
    */
	function doTotal($type, $sensor) 
	{
        $return = NULL;
	    $class = $this->getClass($type, $sensor);
        if (is_object($class)) {
            $return = (bool) $class->sensors[$type][$sensor]['doTotal'];
        }
		return $return;
	}
    /**
        Returns the default units for this type of sensor
    */
    
	function getUnitMode($type, $sensor, $unit=NULL, $mode=FALSE) 
	{
        $return = array();
	    $class = $this->getClass($type, $sensor);
        if (is_object($class)) {
            if (is_null($unit)) {
                $return = $class->sensors[$type][$sensor]['unitModes'];
                if (is_array($return)) {
                    foreach($return as $key => $val) {
                        $return[$key] = explode(",", $val);
                    }
                }
            } else {
                $return = $class->sensors[$type][$sensor]['unitModes'][$unit];
                $return = explode(",", $return);
                if ($mode !== FALSE) {
                    if ($mode == "ignore") {
                        $return = $mode;
                    } else if (array_search($mode, $return) !== FALSE) {
                        $return = $mode;
                    } else {
                        $return = $return[0];
                    }
                }
            }
        }
		return $return;
	}
    /**
        Returns the default units for this type of sensor
    */
    
	function getUnitDefMode($type, $sensor, $unit) 
	{
        $return = $this->getUnitMode($type, $sensor, $unit);
		return $return[0];
	}

    /**
        Returns all possible units for this type of sensor
    */
	function getAllUnits($type, $sensor) 
	{
        $return = array();
	    $class = $this->getClass($type, $sensor);
        if (is_object($class)) {
            $return = $class->sensors[$type][$sensor]['validUnits'];
        }
		return $return;
	}


    /**
        Returns the class
    */
    function getAllSensors($type) {
        $type = (int)$type;
        $sensors = array();
        $done = array();
        if (is_array($this->dev[$type])) {
            foreach($this->dev[$type] as $key => $class) {
                if ($done[$class] !== TRUE) {
                    $s = $this->sensors[$class]->sensors[$type];
                    if (is_array($s)) $sensors = array_merge($sensors, $s);
                    $done[$class] = TRUE;
                }
            }
        }    

        return $sensors;
    }

    function checkUnits(&$type, &$sensor, &$units, &$mode) {
        if (is_array($type)) {
            if (!is_array($units)) $units = array();
            if (!is_array($mode)) $mode = array();
            if (!is_array($sensor)) $sensor = array();
        
            $skip = 0;
            foreach($units as $key => $value) {                
                $units[$key] = $this->getUnits($type[$key], $sensor[$key], $units[$key]);
                $mode[$key] = $this->getUnitMode($type[$key], $sensor[$key], $units[$key], $mode[$key]);
            }
        } else {
            if (is_array($sensor)) unset($sensor);
            $units = $this->getUnits($type, $sensor, $units);
            $mode = $this->getUnitMode($type, $sensor, $units, $mode);           
        }
    }

    function decodeData(&$Info, &$data) {
		if (!isset($data["Date"])) $data["Date"] = date("Y-m-d H:i:s");
		if (is_array($data["raw"])) {
            $skip = 0;
			foreach($data["raw"] as $rawkey => $rawval) {
                // This is so we skip useless data points.  If a sensor takes more than
                // one input then skip the extra ones.
                if ($skip > 0) {
                    $data["Data".$rawkey] = NULL;
    				$data["data"][$rawkey] = NULL;
                    $skip--;
                    continue;
                }
                $skip = $this->getSize($data["Types"][$rawkey], $Info['params']['sensorType'][$rawkey]) - 1;


                if (is_null($data['Units'][$rawkey]))
                {
                    $data['Units'][$rawkey] = $this->getUnits($data["Types"][$rawkey], $Info['params']['sensorType'][$rawkey]);
                }
                $data['dType'][$rawkey] = $this->getUnitMode($data["Types"][$rawkey], $Info['params']['sensorType'][$rawkey], $data['Units'][$rawkey], $Info['params']['dType'][$rawkey]);
                $data['unitType'][$rawkey] = $this->getUnitType($data["Types"][$rawkey], $Info['params']['sensorType'][$rawkey]);
                if ($data['dType'][$rawkey] == 'diff') {
                    if (isset($this->lastRecord[$data['DeviceID']])) {
						$newraw = $rawval - $this->lastRecord[$data['DeviceID']]["raw"][$rawkey];
                        if (!isset($data['deltaT'])) $data['deltaT'] =  strtotime($data['Date']) - strtotime($this->lastRecord[$data['DeviceID']]['Date']);
						$data["Data".$rawkey] = $this->getReading($newraw, $data["Types"][$rawkey], $Info['params']["sensorType"][$rawkey], $data["TimeConstant"], $Info['params']['Extra'][$rawkey], $data['deltaT']);
                    } else {
						$data["Data".$rawkey] = NULL;
                    }
                } else {
					$data["Data".$rawkey] = $this->getReading($rawval, $data["Types"][$rawkey], $Info['params']["sensorType"][$rawkey], $data["TimeConstant"], $Info['params']['Extra'][$rawkey], $deltaT);
                }
				$data["data"][$rawkey] = $data["Data".$rawkey];
			}
    		$this->checkRecord($data);
    		if ($data['Status'] == "GOOD") {
    		    $this->lastRecord[$data['DeviceID']] = $data;
            }    
		}
    }

    function checkRecord(&$data) {
        // Start with the assumption that the packet is good.
        if (!isset($data["Status"])) $data["Status"] = "GOOD";

        // Check each reading.
        for ($i = 0; $i < $data['ActiveSensors']; $i++) {
            if (!$this->checkPoint($data['Data'.$i], $data['Types'][$i], $data['params']['sensorType'][$i], $data['Units'][$i], $data['dType'][$i])) {
                $data['Data'.$i] = NULL;
                $data['data'][$i] = NULL;
            }
        }
    }
    function checkPoint($value, $type, $sensor, $dType) {
        $class = $this->getClass($type, $sensor);
        if (is_object($class)) {
            $args = func_get_args();
            $args[1] = $class->sensors[$type][$sensor]; // This overwrites the type
            unset($args[2]); // Remove the $sensor
            $stuff = $class->sensors[$type][$sensor];
            $ret = $this->runFunction($class, $stuff['checkFunction'], $args, TRUE);
        }
        return $ret;
    }
}


/**
	@brief Base class for sensors.
*/
class sensor_base
{

	/**
		The maximum value for the AtoD convertor from @ref vSensors_final_formula
	*/
	var $Am = 1023;
	/**
		The Tf value from @ref lightSensors_final_formula
	*/
	var $Tf = 65536;
	/**
		The D value from @ref lightSensors_final_formula
	*/
	var $D = 65536;
	/**
		The s value from @ref vSensors_final_formula
	*/
	var $s = 64;
	/**
		The Vcc value
	*/
	var $Vcc = 5;
    /**
        This defines all of the sensors that this driver deals with...
    */
    var $sensors = array();
    /**
        This is the default sensor if no sensor is defined...
    */
    var $defaultSensors = array();
    
	/**
		Constructor.
	*/
	function __construct($Tf=FALSE, $D=FALSE, $s=FALSE, $Am=FALSE, $Vcc=FALSE)
	{
		if (is_numeric($Tf)) {
			$this->Tf = $Tf;
		}
		if (is_numeric($Vcc)) {
			$this->D = $D;
		}
		if (is_numeric($Am)) {
			$this->Am = $Am;
		}
		if (is_numeric($Vcc)) {
			$this->Vcc = $Vcc;
		}
		if (is_numeric($s)) {
			$this->s = $s;
		}
	}


}

?>
