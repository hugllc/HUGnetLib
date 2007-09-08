<?php
/**
	$Id: winddirection.inc.php 121 2007-07-19 13:13:02Z prices $
	@file sensors/resistive.inc.php
	@brief Class for dealing with resistive sensors
	
	
*/

/**
	@brief class for dealing with resistive sensors.
*/
class pulseCounter
{

	/**
		The maximum value for the AtoD convertor from @ref vSensors_final_formula
	*/
	var $Am = 1023;
	/**
		The Tf value from @ref vSensors_final_formula
	*/
	var $Tf = 65536;
	/**
		The D value from @ref vSensors_final_formula
	*/
	var $D = 65536;
	/**
		The D value from @ref vSensors_final_formula
	*/
	var $s = 64;

    /**
        This defines all of the sensors that this driver deals with...
    */
    var $sensors = array(
        "0x70" => array(
            'maximumAnemometer' => array(
                "longName" => "Maximum Inc type #41 Anemometer",
                "validUnits" => array('RPM', 'MPH', 'counts'),
                "defaultUnit" =>  'MPH',
                "extraData" => 2,
            ),
            'maximumRainGauge' => array(
                "longName" => "Maximum Inc rain gauge",
                "validUnits" => array('&#34;'),
                "defaultUnit" =>  '&#34;',
            ),
            'genericRevolver' => array(
                "longName" => "Generic Revolving Thingy",
                "validUnits" => array('counts', 'RPM'),
                "defaultUnit" =>  'RPM',
                "extra" => "Counts per Revolution"
            ),
            'generic' => array(
                "longName" => "Generic Pulse Counter",
                "validUnits" => array('counts'),
                "defaultUnit" =>  'counts',
            ),
        ),
    );
	/**
		Constructor.
	*/
	function pulseCounter($Tf=FALSE, $D=FALSE, $s=FALSE, $Am=FALSE, $Vcc=FALSE)
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
		if (is_numeric($D)) {
			$this->D = $D;
		}
		if (is_numeric($Tf)) {
			$this->Tf = $Tf;
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
	function getReading($val, $type) 
	{
	    $output = 0;
            if ($type == "0x6F") {
                $output = $this->maximumIncSensor($val);
            }   
            return($output);
	}

    function maximumIncSensor($ndir) {
        
        return $dir;
    }

	/**
		@public
		@brief Gets the units for a sensor
		@param $type Int The type of sensor.
		@return The units for a particular sensor type
	
		@par Introduction
	*/
	function getUnit($type, $sensor) 
	{
		return($this->sensors[$type][$sensor]['defaultUnit']);
	}



}




?>
