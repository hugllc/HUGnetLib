<?php
/**
	$Id: light.inc.php 674 2007-03-01 03:00:00Z prices $
	@file sensors/resistive.inc.php
	@brief Class for dealing with resistive sensors
	
	
*/

/**
	@brief class for dealing with resistive sensors.
*/
class lightSensor
{

	/**
		The maximum value for the AtoD convertor from @ref lightSensors_final_formula
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
		The D value from @ref lightSensors_final_formula
	*/
	var $s = 64;

	/**
		Constructor.
	*/
	function lightSensor($Tf=FALSE, $D=FALSE, $s=FALSE, $Am=FALSE)
	{
		if (is_numeric($Am)) {
			$this->Am = $Am;
		}
		if (is_numeric($s)) {
			$this->s = $s;
		}
		if (is_numeric($D)) {
			$this->D = $D;
		}
		if (is_numeric($Tf)) {
			$this->Tf = $Tf;
		}
	}

	/**
		@public
		@brief Converts a raw AtoD reading into resistance
		@param $A Integer The AtoD reading
		@param $TC Integer The time constant used to get the reading
		@param $Bias Float The bias resistance in kOhms
		@return The resistance corresponding to the values given
		
		@par Introduction
		This function takes in the AtoD value and returns the calculated
		resistance of the sensor.  It does this using a fairly complex
		formula.  This formula and how it was derived is detailed in 
		@ref lightSensors.
		
	*/
	function getLight($A, $TC)
	{
	/*
		$den = $this->Am * $this->s * $this->*this->Tf * $T;
		if ($den == 0) return(1500);
		$L = (-1500)*$this->D*$A;
		$L = $L / $den;
		$L += 1500;
*/
/*
		$den = $this->Am * $this->s * $this->Tf * $TC;
		if ($den == 0) return(1500);
		$L = (-1500)*$this->s*$this->D*$A;
		$L = $L / $den;
		$L += 1500;
*/


		// I am not sure why this is here.  It seems to be the
		// wrong formula according to the docs  2006-12-15 SLP
		// This formula is correct.
		$den = $this->Am*$this->s*$this->D; 
		if ($den == 0) return(1500);
		$L = (-1500)*$A * $this->Tf * $TC;
		$L = $L / $den;
		$L += 1500;

		return($L);
	}
	
	/**
		@public
		@brief Converts resistance to temperature for BC Components #2322 640 66103 10K thermistor.
		@param $R Float The current resistance of the thermistor
		@param $type Int The type of sensor.
		@return Sensor value	
	
		@par Introduction
		This function 

	
		@par Thermistors available:
		
		-# 10K Ohm BC Components #2322 640 66103. This is defined as thermistor 0 in the type code.				
			- R0 10
			- A 3.354016e-3
			- B 2.569355e-4
			- C 2.626311e-6
			- D 0.675278e-7
	*/
	function getReading($R, $type) 
	{
		switch($type) {
			default:
				$Read = $this->getLight($R);
				break;
		}
		return($Read);
	}
	/**
		@public
		@brief Gets the units for a sensor
		@param $type Int The type of sensor.
		@return The units for a particular sensor type
	
		@par Introduction
	*/
	function getUnit($type) 
	{
		switch($type) {
			default:
				$U = "W/m^2";
				break;
		}
		return($T);
	}




}




?>
