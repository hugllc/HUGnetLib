<?php
/**
	$Id: 00390304.inc.php 53 2006-05-14 20:57:54Z prices $
	@file drivers/endpoints/00390304.inc.php
	@brief Drivers for the 0039-03-04 hardware
	@deprecated
	
	
	
*/
if (!class_exists("e00390304")) {
/** Constant */
define("e00391101A", TRUE);
/** Constant */
define("e00391101A_EEPROM_READ", "0A");
/** Constant */
define("e00391101A_SRAM_READ", "0B");
/** Constant */
define("e00391101A_SENSOR_LENGTH", 30);
/** Constant */
define("e00391101A_TC", 36);
/** Constant */
define("e00391101A_TYPES", 40);
/** Constant */
define("e00391101A_SENSORS", 0);



	$this->add_generic(array("Name" => "e00390304", "Type" => "driver", "Class" => "e00390304", "deviceJOIN" => ""));


	/**
		@brief Driver for the RS232 gateway board 0039-03-04
		@deprecated
	*/
	class e00390304 extends eDEFAULT{

		var $devices = array(
			"0039-11-01-A" => array(
				"0039-03-04-A" => "DEFAULT",
			),
			"0039-11-04-A" => array(
				"0039-03-04-A" => "DEFAULT",
			),
		);


		var $cols = array("Function" => "Function", "# Sensors" => "NumSensors");

		function ReadMem($Info) {
		
			switch($Info["MemType"]) {
				case EEPROM:
					$Type = e00391101A_EEPROM_READ;
					break;
				case SRAM:
				default:
					$Type = e00391101A_SRAM_READ;
					break;
			}
			$return = array();
			$return["Command"] = $Type;
			$return["To"] = $Info["DeviceID"];
			$return["Data"][0] = "00" ;
			$return["Data"][1] = $Info["MemAddress"] & 0xFF;
			$return["Data"][2] = $Info["MemLength"] & 0xFF;
			return($return);
		}
	
		
		function GetInfo($Info) {
	
			$Info["TimeConstant"] = hexdec(substr($Info["RawSetup"], e00391101A_TC, 4));
			switch($Info["FWPartNum"]) {
				case "0039-11-04-A":
				default:
					$Info["Function"] = "HUGnet to HUGnet Gateway";
					break;
				case "0039-11-01-A":
				default:
					$Info["Function"] = "RS232 to HUGnet Gateway";
					break;

			}
			$Info["NumSensors"] = e00391101A_SENSORS;	
			return($Info);
		}
		
	
	}	
}


?>
