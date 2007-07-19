<?php
/**
	$Id$
	@file sensors/resistive.inc.php
	@brief Class for dealing with resistive sensors
	
	This class does all the math for resistive sensors.
	
	$Log: resistive.inc.php,v $
	Revision 1.4  2005/11/23 16:45:57  prices
	Fixed a couple of things
	
	Revision 1.3  2005/06/13 23:02:29  prices
	Fixed a problem in how the drivers were checking records and how e00391200 was
	interpreting packets.
	
	Revision 1.2  2005/04/26 22:06:18  prices
	Updated documentation.
	

	
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
class resistiveSensor
{

	/**
		The maximum value for the AtoD convertor from @ref rSensors_final_formula
	*/
	var $Am = 1023;
	/**
		The Tf value from @ref rSensors_final_formula
	*/
	var $Tf = 65536;
	/**
		The D value from @ref rSensors_final_formula
	*/
	var $D = 65536;
	/**
		The D value from @ref rSensors_final_formula
	*/
	var $s = 64;

	/**
		Constructor.
	*/
	function resistiveSensors($Tf=FALSE, $D=FALSE, $s=FALSE, $Am=FALSE)
	{
		if (is_numeric($Am)) {
			$this->Am = $Am;
		}
		if (is_numeric($AtoDMax)) {
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
		@ref rSensors.
		
	*/
	function getResistance($A, $TC, $Bias, $Tf = NULL, $D = NULL, $s = NULL, $Am=NULL)
	{
		if (is_null($Tf)) $Tf = $this->Tf;	
		if (is_null($D)) $D = $this->D;	
		if (is_null($s)) $s = $this->s;	
		if (is_null($Am)) $Am = $this->Am;	
	
		if ($D == 0) return(0);
		$Den = ((($Am*$s*$TC*$Tf)/$D) - $A);
		if (($Den == 0) || !is_numeric($Den)) return($A*$Bias);
		$R = ($A*$Bias)/$Den;
		return($R);
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
		if ($R <= 0) return(0);
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
