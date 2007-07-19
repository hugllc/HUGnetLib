<?php
/**
	$Id$
	@file sensors/moisture.inc.php
	@brief Class for dealing with resistive sensors
	
	This class does all the math for resistive sensors.
	
	$Log: moisture.inc.php,v $
	Revision 1.3  2005/09/12 15:00:35  prices
	The formula for % moisture now tops out at 35.
	
	Revision 1.2  2005/09/02 21:26:04  prices
	Fixed the moisture sensor printout.
	
	Revision 1.1  2005/09/01 17:15:43  prices
	Modified the moisture sensors to read % moisture.  This is a first shot
	at it.  It is probably off.
	
	

	
*/
/**
	@page rSensors Resistive Sensors
	@section rSensors_intro Introduction
	There are a whole host of resistive sensors on the market.  They are often cheap
	and are very easy to read.  In this case we read the voltage across
	a voltage divider, then calculate the resistance.
	@par


	@section rSensors_circuit The Circuit Used
	@image html GetRCircuit.png "Resistive sensor circuit diagram"

	<b>Notes:</b>
	-# AtoD +Ref should be the voltage that the AtoD reads as its maximum value
	-# AtoD -Ref should be the voltage that the AtoD reads as 0
	-# The AtoD simply returns the value it reads.

	@section rSensors_deriving_formula Deriving the formula
	@subsection rSensors_deriving_formula_assuming Assuming
	-# \f$R\f$ is resistance of the sensor (k Ohms)
	-# \f$A_r\f$ is the AtoD reading
	-# \f$A\f$ is the averaged AtoD reading
	-# \f$A_m\f$ is the AtoD maximum value
	-# \f$A_a\f$ is the averager's maximum value
	-# \f$T\f$ is the time constant (# of samples)
	-# \f$B\f$ is the bias resistance (k Ohms)

	@subsection rSensors_deriving_formula_1iteration Initial Formula
	The circuit in section @ref rSensors_circuit gives us the standard formula
	for resistor dividers:
	\f[
		A_r=A_m(\frac{R}{B + R})
	\f]
	Solving for R gives us:
	\f[
		R=\frac{A_rB}{A_m - A_r}
	\f]

	@subsection rSensors_deriving_formula_2iteration Compensating for a Shifted Output
	The AtoD result is 10 bits, but it is left justified in a 16 bit number.
	We need to shift Am 6 to the left or multiply by 64 to take this into account. 
	The AtoD reading that we get will automatically take this into account, 
	so we don't need to worry about modifying 'A'. The resulting formula is:
	\f[
		R=\frac{A_rB}{sA_m - A_r}
	\f]
	Where
	-# \f$s\f$ is the shift multiplier.

	@subsection rSensors_deriving_formula_3iteration Adding in the Averager
	This modification in the formula is to take the averager into account.  The
	averager gives us the generic formula:
	\f[
		A=T_fTA_r/D
	\f]
	Where
	-# \f$A\f$ is the averaged AtoD reading
	-# \f$A_r\f$ is the AtoD reading
	-# \f$T_f\f$ is the fixed portion of the time constant
	-# \f$T\f$ is the variable portion of the time constant
	-# \f$D\f$ is the amount the buffer is divided by when the endpoint sends it out

	The formula as it sits is:
	\f[
		R=\frac{A_rB}{sA_m - A_r}
	\f]
	We have \f$B\f$ and \f$A_m\f$ but we don't have \f$A_r\f$.  To get the
	formula to a place we can deal with it we are going to multiply the right
	side if it by:
	\f[
		(\frac{T_fT/D}{T_fT/D})
	\f]
	Which gives us:
	\f[
		R=\frac{(T_fT/D)(A_rB)}{(T_fT/D)(sA_m - A_r)}
	\f]
	Which can be written:
	\f[
		R=\frac{(T_fTA_r/D)B}{(sA_mTT_f/D) - (T_fTA_r/D)}
	\f]

	So we can then substitute in \f$A=TfTA_r/D\f$ because \f$A\f$ is given.  This
	gives us our final formula:
	\f[
		R=\frac{AB}{(sA_mTT_f/D) - A}
	\f]

	
	@section rSensors_final_formula The Final Formula
		
	\f[
		R=\frac{AB}{(sA_mTT_f/D) - A}
	\f]
	Where:
	-# \f$R\f$ is resistance of the sensor (k Ohms)
	-# \f$A\f$ is the averaged AtoD reading
	-# \f$A_m\f$ is the AtoD maximum value
	-# \f$T\f$ is the time constant (# of samples)
	-# \f$B\f$ is the bias resistance (k Ohms)
	-# \f$T_f\f$ is the fixed portion of the time constant
	-# \f$D\f$ is the amount the buffer is divided by when the endpoint sends it out
	-# \f$s\f$ is the shift multiplier.


	@section rSensors_example Example
	<b>Explaination of the Averager in the 0039-20-03-C firmware</b>
	The averager adds the 16 bit numbers from the AtoD converter into a 40 bit buffer. 
	The number of averages is 65536*T where T is the time constant in the E2. Only the
	top 24 bits are returned to the host computer.  This gives us the following values:

	- \f$T_f = 65536\f$
	- \f$D = 65536\f$
	
	The result is:
	\f[
		R=\frac{AB}{((65536)64A_mT/65536) - A}
	\f]
	Which can be simplified to
	\f[
		R=\frac{AB}{64A_mT - A}
	\f]


*/

/**
	@brief class for dealing with resistive sensors.
*/
class moistureSensor
{

	/**
		The maximum resistance considered Red Zone (Ohms)
	*/
	var $Rr = 10000;
	/**
		The maximum resistance considered Yellow Zone (Ohms)
	*/
	var $Ry = 100000;
	/**
		The minimum % moisture considered Red Zone (% moisture)
	*/
	var $Mr = 18;
	/**
		The minimum % moisture considered Yellow Zone (%moisture)
	*/
	var $My = 12;

	/**
		Constructor.
	*/
	function moistureSensors($Rr=FALSE, $Ry=FALSE, $Mr=FALSE, $My=FALSE)
	{
		if (is_numeric($Rr)) {
			$this->Rr = $Rr;
		}
		if (is_numeric($Ry)) {
			$this->Ry = $Ry;
		}
		if (is_numeric($Mr)) {
			$this->Mr = $Mr;
		}
		if (is_numeric($My)) {
			$this->My = $My;
		}
	}

	/**
		@public
		@brief Converts resistance into % moisture 
		@param $R float The resistance (k Ohms) of the sensor.
		
		@par Introduction
		This routine take in k Ohms and returns % moisture.
		
	*/
	function getMoisture($R)
	{
		if ($R == 0) return(35);
		//$R is coming in k Ohms.  We need Ohms.
		$R = $R * 1000;
		$num = $this->My - $this->Mr;
		$den = log($this->Ry) - log($this->Rr);
		if ($den == 0) return(35);
		$B = $num / $den;
		$A = $this->Mr - ($B * log($this->Rr));
		
		$M = $A + ($B * log($R));
		return($M);
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
			case 2:
			case 0:
				$Read = $this->BCTherm2322640Interpolate($R, 10, 3.354016e-3, 2.569355e-4, 2.626311e-6, 0.675278e-7);
				break;
			default:
				$Read = $R;
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
			case 2:
			case 0:
				$U = "Degrees C";
				break;
			default:
				$U = "k Ohms";
				break;
		}
		return($T);
	}


	/**
		@private
		@brief Uses a formula to convert resistance to temp for BC Components #2322 640 series thermistors.
		@param $R The current resistance of the thermistor.
		@param $R0 The resistance of the thermistor at 25C
		@param $A Thermistor Constant A (From datasheet)
		@param $B Thermistor Constant B (From datasheet)
		@param $C Thermistor Constant C (From datasheet)
		@param $D Thermistor Constant D (From datasheet)
		@return The Temperature in degrees C

		@par Introduction
		This formula is from BCcomponents PDF file for the
		# 2322 640 thermistor series on page 6.  See the data sheet for
		more information.

		@par Usage
		This function should be called with the values set for the specific
		thermistor that is used.  See eDEFAULT::Therm0Interpolate as an example.
	*/
	function BCTherm2322640Interpolate($R, $R0, $A, $B, $C, $D)
	{
		if ($R0 == 0) return(0);
		$T = $A;
		$T += $B * log($R/$R0);
		$T += $C * pow(log($R/$R0),2);
		$T += $D * pow(log($R/$R0), 3);
		$T = pow($T, -1);

		$T -= 273.15;
		return($T);
	}



}




?>
