<?php
/**
	$Id: winddirection.inc.php 121 2007-07-19 13:13:02Z prices $
	@file sensors/resistive.inc.php
	@brief Class for dealing with resistive sensors
	
	
*/

/**
	@brief class for dealing with resistive sensors.
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
        $stuff = $this->sensors[$type][$sensor];
        if (is_null($stuff)) $stuff = $this->sensors[$type][$this->defaultSensor];


        if (isset($stuff['mult'])) $val *= $stuff['mult'];
        if (isset($stuff['function'])) {
            if (method_exists($this, $stuff['function'])) {
                $fct = array(&$this, $stuff['function']);
                $args = func_get_args();
                unset($args[1]); // Remove the $type
                unset($args[2]); // Remove the $sensor
                $val = call_user_func_array($fct, $args);
            }
        }
        return($val);
	}

    /**
        Returns the default units for this type of sensor
    */
	function getUnits($type, $sensor) 
	{
print $type." => ".$sensor." => ".$this->defaultSensor."\n";
        $return = $this->sensors[$type][$sensor]['defaultUnits'];
        if (is_null($return)) $return = $this->sensors[$type][$this->defaultSensor]['defaultUnits'];
		return $return;
	}

    /**
        Returns all possible units for this type of sensor
    */
	function getAllUnits($type, $sensor) 
	{
        $return = $this->sensors[$type][$sensor]['validUnits'];
        if (is_null($return)) $return = $this->sensors[$type][$this->defaultSensor]['validUnits'];
		return $return;
	}

}

?>
