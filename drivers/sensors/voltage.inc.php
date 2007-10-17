<?php
/**
 *   Sensor driver for voltage sensors.
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
	@page vSensors Voltage Sensors
	@section vSensors_intro Introduction
	Sensing Voltage is only natural since that is what the A to D is doint.
	@par


	@section vSensors_circuit The Circuit Used
	@image html GetVCircuit.png "Resistive sensor circuit diagram"

	<b>Notes:</b>
	-# AtoD +Ref should be the voltage that the AtoD reads as its maximum value
	-# AtoD -Ref should be the voltage that the AtoD reads as 0
	-# The AtoD simply returns the value it reads.

	@section vSensors_deriving_formula Deriving the formula
	@subsection vSensors_deriving_formula_assuming Assuming
	-# \f$R_1\f$ is resistance to the V
	-# \f$R_2\f$ is resistance to Ground
	-# \f$V\f$ is voltage we are reading
	-# \f$V_0\f$ is voltage into the AtoD
	-# \f$V_c\f$ is processor supply voltage, and the AtoD +Ref voltage
	-# \f$A_r\f$ is the AtoD reading
	-# \f$A\f$ is the averaged AtoD reading
	-# \f$A_m\f$ is the AtoD maximum value
	-# \f$A_a\f$ is the averager's maximum value
	-# \f$T\f$ is the time constant (# of samples)

	@subsection vSensors_deriving_formula_1iteration Initial Formula
	The circuit in section @ref vSensors_circuit gives us the standard formula
	for resistor dividers:
	\f[
		V_0=V(\frac{R_2}{R_1 + R_2})
	\f]
	Solving for \f$V\f$ gives us:
	\f[
		V=V_0(\frac{R_1 + R_2}{R_2})
	\f]
	@subsection vSensors_deriving_formula_2iteration Getting the Formula in Terms We Know
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
	Now we plug that into are equasion above and get:
	\f[
		V=\frac{A_rV_c(R_1 + R_2)}{A_mR_2}
	\f]
	

	@subsection vSensors_deriving_formula_3iteration Compensating for a Shifted Output
	The AtoD result is 10 bits, but it is left justified in a 16 bit number.
	We need to shift Am 6 to the left or multiply by 64 to take this into account. 
	The AtoD reading that we get will automatically take this into account, 
	so we don't need to worry about modifying 'A'. The resulting formula is:
	\f[
		V=\frac{A_rV_c(R_1 + R_2)}{sA_mR_2}
	\f]

	Where
	-# \f$s\f$ is the shift multiplier.

	@subsection vSensors_deriving_formula_4iteration Adding in the Averager
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
		V=\frac{A_rV_c(R_1 + R_2)}{sA_mR_2}
	\f]
	we plug in the averager formula and get
	\f[
		V=\frac{ADV_c(R_1 + R_2)}{sTT_fA_mR_2}
	\f]
	
	
	@section vSensors_final_formula The Final Formula
		
	\f[
		V=\frac{ADV_c(R_1 + R_2)}{sTT_fA_mR_2}
	\f]
	Where:
	-# \f$R_1\f$ is resistance to the V
	-# \f$R_2\f$ is resistance to Ground
	-# \f$V\f$ is voltage we are reading
	-# \f$V_c\f$ is processor supply voltage, and the AtoD +Ref voltage
	-# \f$A_r\f$ is the AtoD reading
	-# \f$A\f$ is the averaged AtoD reading
	-# \f$A_m\f$ is the AtoD maximum value
	-# \f$T\f$ is the variable portion of the time constant (# of samples)
	-# \f$T_f\f$ is the fixed portion of the time constant (# of samples)
	-# \f$s\f$ is the shift multiplier.
	-# \f$D\f$ is the amount the buffer is divided by when the endpoint sends it out



*/
if (!class_exists('voltageSensor')) {
    $this->add_generic(array("Name" => "voltageSensor", "Type" => "sensor", "Class" => "voltageSensor"));

    /**
    	@brief class for dealing with resistive sensors.
    */
    class voltageSensor extends sensor_base
    {
        /**
            This defines all of the sensors that this driver deals with...
        */
        var $sensors = array(
            0x10 => array(
                'CHSMSS' => array(
                    "longName" => "TDK CHS-MSS ",
                    "unitType" => "Relative Humidity",
                    "validUnits" => array('%'),
                    "defaultUnits" =>  '%',
                    "function" => "CHSMSS",
                    "storageUnit" => '%',
                    "checkFunction" => "CHSMSS_check",
                    "unitModes" => array(
                        '%' => 'raw,diff',
                    ),
                ),
            ),
            0x40 => array(
                "FETBoard" => array(
                    "longName" => "FET Board Voltage Sensor",
                    "unitType" => "Voltage",
                    "validUnits" => array('V', 'mV'),
                    "defaultUnits" =>  'V',
                    "function" => "FETBoard",
                    "storageUnit" => 'V',
                    "unitModes" => array(
                        'mV' => 'raw,diff',
                        'V' => 'raw,diff',
                    ),
                    "extraText" => array("R1 in kOhms", "R2 in kOhms"),
                    "extraDefault" => array(150, 10),
                ),
                "Controller" => array(
                    "longName" => "Controller Board Voltage Sensor",
                    "unitType" => "Voltage",
                    "validUnits" => array('V', 'mV'),
                    "defaultUnits" =>  'V',
                    "function" => "FETBoard",
                    "storageUnit" => 'V',
                    "unitModes" => array(
                        'mV' => 'raw,diff',
                        'V' => 'raw,diff',
                    ),
                    "extraText" => array("R1 in kOhms", "R2 in kOhms"),
                    "extraDefault" => array(180, 27),
                ),
            ),
        );
    
        function getDividerVoltage($A, $R1, $R2, $T)
        {
                $denom = $this->s * $T * $this->Tf * $this->Am * $R2;
                if ($denom == 0) return 0;
                $numer = $A * $this->D * $this->Vcc * ($R1 + $R2);

                $Read = $numer/$denom;
                return($Read);
        }

        function FETBoard($val, $sensor, $TC, $extra=NULL) {
            $R1 = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $R2 = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $V = $this->getDividerVoltage($val, $R1, $R2, $TC);
            if ($V < 0) $V = NULL;
    	    $V = round($V, 4);
            return $V;
        }    
    
    	/**
    		@public
    		@brief Gets the units for a sensor
    		@param $type Int The type of sensor.
    		@return The units for a particular sensor type
    	
    		@par Introduction
    	*/
    	function getVoltage($A, $T, $Vref) 
    	{
    	    if (is_null($A)) return NULL;
    	    if (is_null($Vref)) return NULL;
    		$denom = $T * $this->Tf * $this->Am * $this->s;
    		if ($denom == 0) return 0;
    		$num = $A * $this->D * $Vref;
    	    
    	    $volts = $num / $denom;
    		return $volts;
    	}
    
    
        /**
            This sensor returns us 10mV / % humidity
        */
        function CHSMSS($A, $sensor, $T, $Vref=1.1) {
            if (is_null($A)) return NULL;
            $volts = $this->getVoltage($A, $T, (float) $Vref);
            $humidity = $volts * 100;
            if (($humidity > 100) && ($humidity < 105)) return 100;
            if ($humidity > 100) return NULL;
            if ($humidity < 0) return NULL;
            return $humidity;
        }
        
        function CHSMSS_check($humidity, $units) {
            return TRUE;
        }

    }
}




?>
