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
		The D value from @ref vSensors_final_formula
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
		Constructor.
	*/
	function __construct($s=FALSE, $Am=FALSE, $Vcc=FALSE)
	{
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
	function getReading($val, $type, $sensor) 
	{
        $stuff = $this->sensors[$type][$sensor];
        if (is_null($stuff)) return $val;  // If we don't know how to deal with it return the original value sent

        if (isset($stuff['mult'])) $val *= $stuff['mult'];
        if (isset($stuff['function'])) {
            if (method_exists($this, $stuff['function'])) {
                $val = call_user_func(array(&$this, $stuff['function']), $val);
            }
        }
        return($val);
	}

    /**
        Returns the default units for this type of sensor
    */
	function getUnit($type, $sensor) 
	{
		return($this->sensors[$type][$sensor]['defaultUnit']);
	}

    /**
        Returns all possible units for this type of sensor
    */
	function getUnit($type, $sensor) 
	{
		return($this->sensors[$type][$sensor]['validUnits']);
	}



}




?>
