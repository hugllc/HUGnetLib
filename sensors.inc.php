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
            $stuff = $class->sensors[$type][$sensor];
            if (!is_array($stuff)) $stuff = $class->sensors[$type][$class->defaultSensor];
            if (isset($stuff['mult'])) $val *= $stuff['mult'];
            $args = func_get_args();
            unset($args[1]); // Remove the $type
            unset($args[2]); // Remove the $sensor
            $val = $this->runFunction($class, $stuff['function'], $args);
        }
        return($val);
	}

    /**
        Returns the class
    */
    function runFunction(&$class, $function, $args) {
        if (isset($function)) {
            if (method_exists($class, $function)) {
                $fct = array(&$class, $function);
                $val = call_user_func_array($fct, $args);
            }
        }
        if (is_null($val)) $val = $args[0];
        return $val;
    }

    /**
        Returns the class
    */
    function &getClass($type, &$sensor) {
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
	function getUnits($type, $sensor) 
	{
        $return = NULL;
	    $class = $this->getClass($type, $sensor);
        if (is_object($class)) {
            $return = $class->sensors[$type][$sensor]['defaultUnits'];
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
        Returns all possible units for this type of sensor
    */
	function getAllUnits($type, $sensor) 
	{
        $return = NULL;
	    $class = $this->getClass($type, $sensor);
        if (is_object($class)) {
            $return = $class->sensors[$type][$sensor]['validUnits'];
        }
		return $return;
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
