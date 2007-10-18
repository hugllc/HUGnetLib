<?php
/**
 *   Sensor driver for moisture sensors
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
	@brief class for dealing with resistive sensors.
*/
class moistureSensor extends sensor_base
{

	/** The maximum resistance considered Red Zone (Ohms) */
	var $Rr = 10000;
	/** The maximum resistance considered Yellow Zone (Ohms) */
	var $Ry = 100000;
	/** The minimum % moisture considered Red Zone (% moisture) */
	var $Mr = 18;
	/** The minimum % moisture considered Yellow Zone (%moisture) */
	var $My = 12;

	/**
		Constructor.
	*/
	function __constructor($Mr=FALSE, $My=FALSE)
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
}




?>
