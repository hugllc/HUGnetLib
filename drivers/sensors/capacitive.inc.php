<?php
/**
 *   Sensor driver for capactive sensors
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
    @page cSensors Capacitive Sensors
    @section cSensors_intro Introduction


    @section cSensors_circuit The Circuit Used
    @image html GetCCircuit.png "Resistive sensor circuit diagram"

    <b>Notes</b>
    -# AtoD +Ref should be the voltage that the AtoD reads as its maximum value
    -# AtoD -Ref should be the voltage that the AtoD reads as 0
    -# V+ must be switched.  If it starts high we switch it low, if it starts low
        we switch it high.

    @section cSensors_reading Reading the AtoD
    The following step should be followed to read the AtoD converter
    -# Set up the AtoD
    -# pull the port high
    -# Wait \f$t\f$ microseconds
    -# Make the port high impedance
    -# Take an AtoD reading \f$A_r\f$

    @section cSensors_deriving_formula Deriving the formula
    @subsection cSensors_deriving_formula_assuming Assuming
    -# \f$C\f$ is capacitance of the sensor (uF)
    -# \f$A_r\f$ is the AtoD reading
    -# \f$A\f$ is the averaged AtoD reading
    -# \f$A_m\f$ is the AtoD maximum value
    -# \f$A_a\f$ is the averager's maximum value
    -# \f$T\f$ is the time constant (# of samples)
    -# \f$R\f$ is the bias resistance (k Ohms)
    -# \f$t\f$ is the time between readings (useconds)
    -# \f$V\f$ is the voltage across the resistor B (V)

    @subsection cSensors_deriving_formula_1derivation Initial Formula
    The circuit in section @ref cSensors_circuit gives us the standard formula
    for a capacitive charge curve:
    \f[
        V=V_m +[V_0 - V_m]e^{-(t-t_0)/\tau}
    \f]
    We can simplify this because \f$V_0 = 0\f$ and \f$t_0 = 0\f$
    \f[
        V=V_m - V_me^{-t/\tau}
    \f]
    If we substitute \f$V = A_r\f$, \f$V_m = A_m\f$ and \f$\tau = RC\f$ we get:
    \f[
        A_r=A_m - A_me^{-t/RC}
    \f]
    Solving for C we get:
    \f[
        e^{-t/RC} = 1-\frac{A_r}{A_m}
    \f]
    \f[
        \frac{-t}{RC} = ln(1-\frac{A_r}{A_m})
    \f]
    \f[
        \frac{RC}{-t} = \frac{1}{ln(1 - \frac{A_r}{A_m})}
    \f]
    \f[
        RC = \frac{-t}{ln(1 - \frac{A_r}{A_m})}
    \f]
    \f[
        C = \frac{-t}{ln(1 - \frac{A_r}{A_m})R}
    \f]
    

    @subsection cSensors_deriving_formula_2derivation Compensating for a Shifted Output
    The AtoD result is 10 bits, but it is left justified in a 16 bit number.
    We need to shift Am 6 to the left or multiply by 64 to take this into account. 
    The AtoD reading that we get will automatically take this into account, 
    so we don't need to worry about modifying 'A'. The resulting formula is:
    \f[
        C = \frac{-t}{ln(1 - \frac{A_r}{sA_m})R}
    \f]
    Where
    -# \f$s\f$ is the shift multiplier.
    
    @subsection cSensors_deriving_formula_3derivation Adding in the Averager
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
        C = \frac{-t}{ln(1 - \frac{A_r}{sA_m})R}
    \f]
    We have \f$B\f$ and \f$A_m\f$ but we don't have \f$A_r\f$.  To get the
    formula to a place we can deal with it we are going to multiply what is inside
    the natural log by:
    \f[
        (\frac{T_fT/D}{T_fT/D})
    \f]
    Which gives us:
    \f[
        C = \frac{-t}{ln(1 - \frac{T_fTA_r/D}{T_fTsA_m/D})R}
    \f]

    So we can then substitute in \f$A=TfTA_r/D\f$ because \f$A\f$ is given.  This
    gives us our final formula:
    \f[
        C = \frac{-t}{ln(1 - \frac{A}{T_fTsA_m/D})R}
    \f]
    \f[
        C = \frac{-t}{ln(1 - \frac{AD}{T_fTsA_m})R}
    \f]
    
    @section cSensors_final_formula The Final Formula
        
    \f[
        C = \frac{-t}{ln(1 - \frac{AD}{T_fTsA_m})R}
    \f]
    Where:
    -# \f$C\f$ is capacitance of the sensor (uF)
    -# \f$A\f$ is the averaged AtoD reading
    -# \f$A_m\f$ is the AtoD maximum value
    -# \f$T\f$ is the time constant (# of samples)
    -# \f$R\f$ is the bias resistance (k Ohms)
    -# \f$s\f$ is the shift multiplier.
    -# \f$t\f$ is the time between readings (useconds)
    Notes:
    -# When \f$A_r = A_m\f$ the result is infinite.
*/
/*
    \f[
        i=C\frac{dv}{dt}
    \f]
    We will be using the approximation:
    \f[
        i=C\frac{\Delta v}{\Delta t}
    \f]
    In our case \f$\Delta t = t\f$ and \f$\Delta v = A_r\f$ so we have:
    \f[
        i=C(\frac{A_r}{t})
    \f]
    Solving for C gives us:
    \f[
        C=i\frac{t}{A_r}
    \f]
    Since we don't know \f$i\f$ we can use \f$V=iB\f$ or \f$i=V/B\f$
    \f[
        C=(\frac{V}{B})(\frac{t}{A_r})
    \f]    
    or
    \f[
        C=\frac{Vt}{BA_r}
    \f]
    V is constantly changing, but we can approximate it (if we assume \f$V_1\f$ or \f$V_2\f$ was at a rail) as 
    \f[
        V \approx A_m - \frac{A_r}{2}
    \f]
    so if we plug that in we get
    \f[
        C=\frac{(A_m - (A_r/2))t}{BA_r}
    \f]
    or
    \f[
        C=\frac{2A_mt - A_rt}{2BA_r}
    \f]
*/
/**
 *   class for dealing with capacitive sensors.
*/
if (!class_exists('capacitiveSensor')) {
    $this->add_generic(array("Name" => "capacitiveSensor", "Type" => "sensor", "Class" => "capacitiveSensor"));
    class capacitiveSensor extends filter_base
    {
    
        /**
         *   This function takes in the AtoD value and returns the calculated
         *   resistance of the sensor.  It does this using a fairly complex
         *   formula.  This formula and how it was derived is detailed below.
         *   
         *   @param int $A The AtoD reading
         *   @param int $T The time constant used to get the reading
         *   @param float $R The bias resistance in kOhms
         *   @return float The resistance corresponding to the values given
         *   
        */
        function getCapacitance($A, $T, $R, $t=1)
        {
            $Den1 = $this->Tf * $T * $this->s * $this->Am;
            if ($Den1 == 0) return(0);
            $insideLN = (1 - (($A * $this->D)/$Den1));
            if ($insideLN < 0) return(0);
            $Den2 = $R * log($insideLN);
            if ($Den2 == 0) return(0);
    
            $C = (-1.0 / $Den2) * $t;
            return($C);
        }
        
        /**
         *   Converts resistance to temperature for BC Components #2322 640 66103 10K thermistor.
         *
         *   <b>BC Components #2322 640 series</b>
         *
         *   This function implements the formula in $this->BCThermInterpolate
         *   for a is from BCcomponents PDF file for thermistor
         *   #2322 640 series datasheet on page 6.  
         *
         *   <b>Thermistors available:</b>
         *   
         *   -# 10K Ohm BC Components #2322 640 66103. This is defined as thermistor 0 in the type code.                
         *       - R0 10
         *       - A 3.354016e-3
         *       - B 2.569355e-4
         *       - C 2.626311e-6
         *       - D 0.675278e-7
         *
         *   @param float $R The current resistance of the thermistor
         *   @param int $type The type of sensor.
         *   @return float The temperature in degrees C.    
        
        */
        function ThermInterpolate($R, $type) 
        {
            switch($type) {
                case 0:
                    $T = $this->BCTherm2322640Interpolate($R, 10, 3.354016e-3, 2.569355e-4, 2.626311e-6, 0.675278e-7);
                    break;
                default:
                    break;
            }
            return($T);
        }
    
    
        /**
         *   This formula is from BCcomponents PDF file for the
         *   # 2322 640 thermistor series on page 6.  See the data sheet for
         *   more information.
         *    
         *   This function should be called with the values set for the specific
         *   thermistor that is used.  See eDEFAULT::Therm0Interpolate as an example.
         *
         *   @param float $R The current resistance of the thermistor.
         *   @param float $R0 The resistance of the thermistor at 25C
         *   @param float $A Thermistor Constant A (From datasheet)
         *   @param float $B Thermistor Constant B (From datasheet)
         *   @param float $C Thermistor Constant C (From datasheet)
         *   @param float $D Thermistor Constant D (From datasheet)
         *   @return float The Temperature in degrees C
         * 
        */
        function BCTherm2322640Interpolate($R, $R0, $A, $B, $C, $D)
        {
            if ($R == 0) return(0);
            $T = $A;
            $T += $B * log($R/$R0);
            $T += $C * pow(log($R/$R0),2);
            $T += $D * pow(log($R/$R0), 3);
            $T = pow($T, -1);
    
            $T -= 273.15;
            return($T);
        }
    
    
    
    }
}



?>
