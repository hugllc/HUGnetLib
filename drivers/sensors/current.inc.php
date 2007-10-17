<?php
/**
 *   Sensor driver for current sensors
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *   
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *   
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *   
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Sensors
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$    
 *
 */
/**
	@page iSensors Current Sensors
	@section iSensors_intro Introduction

	@par


	@section iSensors_circuit The Circuit Used
	@image html GetICircuit.png "Current sensor circuit diagram"

	<b>Notes:</b>
	-# AtoD +Ref should be the voltage that the AtoD reads as its maximum value
	-# AtoD -Ref should be the voltage that the AtoD reads as 0
	-# The AtoD simply returns the value it reads.

	@section iSensors_deriving_formula Deriving the formula
	@subsection iSensors_deriving_formula_assuming Assuming
	-# \f$R\f$ is resistance we are using to measure the current
	-# \f$I\f$ is Current we are reading
	-# \f$V\f$ The voltage across R
	-# \f$G\f$ The Gain
	-# \f$V_0\f$ is voltage into the AtoD
	-# \f$A_r\f$ is the AtoD reading
	-# \f$A\f$ is the averaged AtoD reading
	-# \f$A_m\f$ is the AtoD maximum value
	-# \f$A_a\f$ is the averager's maximum value
	-# \f$T\f$ is the time constant (# of samples)
	-# \f$V_c\f$ is the maximum AtoD voltage

	@subsection iSensors_deriving_formula_1iteration Initial Formula
	The circuit in section @ref iSensors_circuit gives us Ohm's law:
	\f[
		V=IR
	\f]
	@subsection iSensors_deriving_formula_2iteration Getting the Formula in Terms We Know
	\f$V_0\f$ and \f$A_r\f$ are directly related, so that gives us \f$V_0=mA_r\f$ where
	\f$m\f$ is just a constant.  Since we know that when \f$V_0=V_c\f$ then \f$A_r=A_m\f$
	we can substitute that in and get \f$V_c = mA_m\f$ or 
	\f[
		m = \frac{V_c}{A_m}
	\f]
	if we plug that back into \f$V_0=mA_r\f$ we get
	\f[
		V_0 = \frac{A_rV_c}{A_m}
	\f]
	\f$V_0\f$ and \f$V\f$ are directly related by the gain, so that gives us \f$V_0=GV\f$.
	plugging in the formula for V_0 we get:
	\f[
		\frac{A_rV_c}{A_m}=GV
	\f]
	Plugging in Ohm's law we get:
	\f[
		\frac{A_rV_c}{A_m}=GIR
	\f]	
	Solving for I gets us:
	\f[
		I = \frac{A_rV_c}{A_mGR}
	\f]	
	

	@subsection iSensors_deriving_formula_3iteration Compensating for a Shifted Output
	The AtoD result is 10 bits, but it is left justified in a 16 bit number.
	We need to shift Am 6 to the left or multiply by 64 to take this into account. 
	The AtoD reading that we get will automatically take this into account, 
	so we don't need to worry about modifying 'A'. The resulting formula is:
	\f[
		I = \frac{A_rV_c}{sA_mGR}
	\f]	

	Where
	-# \f$s\f$ is the shift multiplier.

	@subsection iSensors_deriving_formula_4iteration Adding in the Averager
	This modification in the formula is to take the averager into account.  The
	averager gives us the generic formula:
	\f[
		A=\frac{T_fTA_r}{D}
	\f]
	Solving for \f$A_r\f$ we get:
	\f[
		A_r=\frac{AD}{TT_f}
	\f]
	
	Where
	-# \f$A\f$ is the averaged AtoD reading
	-# \f$A_r\f$ is the AtoD reading
	-# \f$T_f\f$ is the fixed portion of the time constant
	-# \f$T\f$ is the variable portion of the time constant
	-# \f$D\f$ is the amount the buffer is divided by when the endpoint sends it out

	The formula as it sits is:
	\f[
		I = \frac{A_rV_c}{sA_mGR}
	\f]	
	we plug in the averager formula and get
	\f[
		I = \frac{ADV_c}{sTT_fA_mGR}
	\f]	
	
	
	@section iSensors_final_formula The Final Formula
	\f[
		I = \frac{ADV_c}{sTT_fA_mGR}
	\f]	

	Where:

	-# \f$R\f$ is resistance we are using to measure the current
	-# \f$I\f$ is Current we are reading
	-# \f$V\f$ The voltage across R
	-# \f$G\f$ The Gain
	-# \f$V_0\f$ is voltage into the AtoD
	-# \f$V_c\f$ is processor supply voltage, and the AtoD +Ref voltage
	-# \f$A_r\f$ is the AtoD reading
	-# \f$A\f$ is the averaged AtoD reading
	-# \f$A_m\f$ is the AtoD maximum value
	-# \f$T\f$ is the variable portion of the time constant (# of samples)
	-# \f$T_f\f$ is the fixed portion of the time constant (# of samples)
	-# \f$s\f$ is the shift multiplier.
	-# \f$D\f$ is the amount the buffer is divided by when the endpoint sends it out



*/
if (!class_exists('currentSensor')) {
    $this->add_generic(array("Name" => "currentSensor", "Type" => "sensor", "Class" => "currentSensor"));

    /**
    	@brief class for dealing with resistive sensors.
    */
    class currentSensor extends sensor_base
    {
    
        var $sensors = array(
            0x50 => array(
                "FETBoard" => array(
                    "longName" => "FET Board Current Sensor",
                    "unitType" => "Current",
                    "validUnits" => array('mA', 'A'),
                    "defaultUnits" =>  'mA',
                    "function" => "FETBoard",
                    "storageUnit" => 'mA',
                    "unitModes" => array(
                        'mA' => 'raw,diff',
                        'A' => 'raw,diff',
                    ),
                    "extraText" => array("R in Ohms", "Gain"),
                    "extraDefault" => array(0.5, 1),
                ),
                "Controller" => array(
                    "longName" => "Controller Board Current Sensor",
                    "unitType" => "Current",
                    "validUnits" => array('mA', 'A'),
                    "defaultUnits" =>  'mA',
                    "function" => "FETBoard",
                    "storageUnit" => 'mA',
                    "unitModes" => array(
                        'mA' => 'raw,diff',
                        'A' => 'raw,diff',
                    ),
                    "extraText" => array("R in Ohms", "Gain"),
                    "extraDefault" => array(0.5, 7),
                ),
            ),
        );
    	/**
    		@public
    		@brief Return the voltage
    		@param $R Float The current resistance of the thermistor
    		@param $type Int The type of sensor.
    		@return Sensor value	
    	
    		@par Introduction
    		This function 
    
    	
    		@par Thermistors available:
    		
    	*/
    	function getCurrent($A, $R, $G, $T) 
    	{
    		$denom = $this->s * $T * $this->Tf * $this->Am * $G * $R;
    		if ($denom == 0) return 0;
    		$numer = $A * $this->D * $this->Vcc;
    
    		$Read = $numer/$denom;
    		return($Read);
    	}
    
        function FETBoard($val, $sensor, $TC, $extra=NULL) {
            $R = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $G = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $A = $this->getCurrent($val, $R, $G, $TC);
            return round($A * 1000, 1);
        }    
    
    }
}



?>
