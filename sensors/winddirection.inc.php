<?php
/**
	$Id: voltage.inc.php 69 2006-05-18 02:43:16Z prices $
	@file sensors/resistive.inc.php
	@brief Class for dealing with resistive sensors
	
	
*/

/**
	@brief class for dealing with resistive sensors.
*/
class windDirectionSensor
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
		Constructor.
	*/
	function windDirectionSensors($Tf=FALSE, $D=FALSE, $s=FALSE, $Am=FALSE, $Vcc=FALSE)
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
        
/*
        if ($ndir & (1<<0)) $dir .= "N";
        if ($ndir & (1<<2)) $dir .= "E";
        if ($ndir & (1<<4)) $dir .= "S";
        if ($ndir & (1<<6)) $dir .= "W";

        if ($ndir & (1<<1)) $dir .= "NE";
        if ($ndir & (1<<3)) $dir .= "SE";
        if ($ndir & (1<<5)) $dir .= "SW";
        if ($ndir & (1<<7)) $dir .= "NW";
*/

        if ($ndir & (1<<0)) $dir1 = 0;   //N
        if ($ndir & (1<<2)) $dir1 = 90;  //E
        if ($ndir & (1<<4)) $dir1 = 180; //S
        if ($ndir & (1<<6)) $dir1 = 270; //W

        if ($ndir & (1<<1)) $dir2 .= 45;    //NE
        if ($ndir & (1<<3)) $dir2 .= 135;    //SE
        if ($ndir & (1<<5)) $dir2 .= 225;    //SW
        if ($ndir & (1<<7)) $dir2 .= 315;    //NW


        if (!is_null($dir1) && is_null($dir2)) {
            $dir = $dir1;
        } else if (is_null($dir1) && !is_null($dir2)) {
            $dir = $dir2
        } else {
            if ($dir1 == 0) {
                if ($dir2 == 45) {
                    $dir = 22.5;
                } else {
                    $dir = 337.5;
                }
            } else {
                $dir = ($dir1 + $dir2) / 2;
            }
        }
        
        return $dir;
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
		return('&#179;');
	}



}




?>