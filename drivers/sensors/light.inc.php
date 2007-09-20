<?php
/**
	$Id$
	@file sensors/resistive.inc.php
	@brief Class for dealing with resistive sensors
	
	
*/
/**
	@brief class for dealing with resistive sensors.
*/
if (!class_exists('lightSensor')) {
    $this->add_generic(array("Name" => "lightSensor", "Type" => "sensor", "Class" => "lightSensor"));

    class lightSensor extends sensor_base
    {
        var $defaultSensor = 'OSRAM BPW-34';
        /**
            This defines all of the sensors that this driver deals with...
        */
        var $sensors = array(
            0x30 => array(
                'OSRAM BPW-34' => array(
                    "longName" =>  "OSRAM BPW-34 Photodiode",
                    "validUnits" => array("W/m^2"),
                    "storageUnit" =>  "W/m^2",
                    "function" => "OSRAMBPW34",
                    "unitModes" => array(
                        'W/m^2' => 'raw,diff',
                    ),
                    
                ),
            ),
        );
    
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
    	
        function OSRAMBPW34($A, $TC) {
    
    		$den = $this->Am*$this->s*$this->D; 
    		if ($den == 0) return(1500);
    		$L = (-1500)*$A * $this->Tf * $TC;
    		$L = $L / $den;
    		$L += 1500;
    
    		return($L);    
        }
    }
}




?>
