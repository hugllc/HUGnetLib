<php
/*
HUGnetLib is a library of HUGnet code
Copyright (C) 2007 Hunt Utilities Group, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 3
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
?>
<?php
/**
	$Id$
	@file drivers/endpoints/00391200.inc.php
	@brief Driver for the 0039-12-XX boards.
		
*/


if (!class_exists("e00391200")) {

/** The location of the time constant in the setup string */
define("e00391102B_TC", ENDPOINT_CONFIGEND);	
/** The location of the types in the setup string */
define("e00391102B_TYPES", ENDPOINT_CONFIGEND+4);
/** The number of sensors for this device */
define("e00391102B_SENSORS", 9);

	$this->add_generic(array("Name" => "e00391200", "Type" => "driver", "Class" => "e00391200", "deviceJOIN" => "e00391200_location"));


	/**
		@brief Driver for the 0039-12 endpoint board and select firmwares
	*/
	class e00391200 extends eDEFAULT{

        var $HWName = "0039-12 Endpoint";
        var $average_table = "e00391200_average";
        var $history_table = "e00391200_history";

		var $devices = array(
		
			"0039-11-02-B" => array(
				"0039-12-00-A" => "DEFAULT",
				"0039-12-01-A" => "DEFAULT",
				"0039-12-02-A" => "DEFAULT",
				"0039-12-01-B" => "DEFAULT",
				"0039-12-02-B" => "DEFAULT",
			),
			"0039-11-03-B" => array(
				"0039-12-00-A" => "DEFAULT",
				"0039-12-01-A" => "DEFAULT",
				"0039-12-02-A" => "DEFAULT",
				"0039-12-01-B" => "DEFAULT",
				"0039-12-02-B" => "DEFAULT",
			),

			"0039-20-02-C" => array(
				"0039-12-02-A" => "DEFAULT",
				"0039-12-02-B" => "DEFAULT",
			),
			"0039-20-03-C" => array(
				"0039-12-02-A" => "DEFAULT",
				"0039-12-02-B" => "DEFAULT",
			),
			"0039-20-07-C" => array(
				"0039-12-02-A" => "DEFAULT",
				"0039-12-02-B" => "DEFAULT",
			),

			"DEFAULT" => array(
				"0039-12-00-A" => "DEFAULT",
				"0039-12-01-A" => "DEFAULT",
				"0039-12-02-A" => "DEFAULT",
				"0039-12-01-B" => "DEFAULT",
				"0039-12-02-B" => "DEFAULT",
			),

		);

		var $deflocation = array("Sensor 1", "Sensor 2", "Sensor 3", "Sensor 4", "Sensor 5", "Sensor 6", "Sensor 7", "Sensor 8", "Sensor 9");

		var $config = array(
			"0039-11-02-B" => array("Function" => "Temperature/Moisture Sensor", "Sensors" => e00391102B_SENSORS, "SensorLength" => e00391102B_SENSOR_LENGTH),		
			"0039-11-03-B" => array("Function" => "Temperature Sensor", "Sensors" => e00391102B_SENSORS, "SensorLength" => e00391103B_SENSOR_LENGTH),		
			"0039-20-02-C" => array("Function" => "Moisture Sensor", "Sensors" => e00391102B_SENSORS, "SensorLength" => e00391102B_SENSOR_LENGTH, "DisplayOrder" => "0,4,1,5,2,6,3,7,8"),
			"0039-20-03-C" => array("Function" => "Temperature Sensor", "Sensors" => e00391102B_SENSORS, "SensorLength" => e00391103B_SENSOR_LENGTH),		
			"0039-20-07-C" => array("Function" => "Capactive Sensor", "Sensors" => e00391102B_SENSORS, "SensorLength" => e00391103B_SENSOR_LENGTH),		
			"0039-20-12-C" => array("Function" => "Pulse Counter", "Sensors" => 4, "SensorLength" => 13),		
			"0039-20-13-C" => array("Function" => "Sensor Board", "Sensors" => 8, "SensorLength" => 24),		
			"DEFAULT" => array("Function" => "Unknown", "Sensors" => 9, "SensorLength" => e00391102B_SENSOR_LENGTH),		
		);

	
		/**
			@brief Extra columns to display for these endpoints
		*/
		var $cols = array("TimeConstant" => "Time Constant", 
								"ActiveSensors" => "Active Sensors",
								"NumSensors" => "# Sensors",
								);


		function ReadMem($Info) {
		
			switch($Info["MemType"]) {
				case EEPROM:
					$Type = eDEFAULT_EEPROM_READ;
					break;
				case SRAM:
				default:
					$Type = eDEFAULT_SRAM_READ;
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

		
    	function CheckRecord($Info, &$Rec) {
    	
    	    if (isset($Rec['Status'])) {
    			$Rec['StatusOld'] = $Rec['Status'];
            }
    
    		if (empty($Rec['RawData'])) {
    			$Rec["Status"] = 'BAD';
    			return;
    		}
            if ($Rec['Status'] == "NEW") $Rec['Status'] = "GOOD";    		
    		
    		$Bad = 0;
    
    		$zero = TRUE;
    		for($i = 0; $i < $Rec['NumSensors']; $i ++) {
    			if (!is_null($Rec['Data'.$i])) {
    				$zero = FALSE;
    				break;
    			}
    		}
    
    		if ($zero && ($i > 3)) {
    			$Rec["Status"] = "BAD";
    			$Rec["StatusCode"] = " All Zero";		
    		}
    
    		if ($Rec["TimeConstant"] == 0) {
    			$Rec["Status"] = "BAD";
    			$Rec["StatusCode"] = " Bad TC";
    		}
    		if (($Bad != 0) && ($Bad >= $Rec["ActiveSensors"])) {
    			$Rec["Status"] = "BAD";
    			$Rec["StatusCode"] = "All Bad Readings";
    		}
    		
    	}
	
		
		function InterpConfig(&$Info) {
			//$Info["Location"] = $this->deflocation;
            $Info['HWName'] = $this->HWName;

			If (isset($this->config[$Info["FWPartNum"]])) {
				$Info["NumSensors"] = $this->config[$Info["FWPartNum"]]["Sensors"];	
				$Info["Function"] = $this->config[$Info["FWPartNum"]]["Function"];
/*
				if (isset($this->config[$Info["FWPartNum"]]["DisplayOrder"])) {
					$Info["DisplayOrder"] = explode(",", $this->config[$Info["FWPartNum"]]["DisplayOrder"]);
				}
*/
			} else {
				$Info["NumSensors"] = $this->config["DEFAULT"]["Sensors"];	
				$Info["Function"] = $this->config["DEFAULT"]["Function"];
			}
			if ($Info["NumSensors"] > 0) {
				$Info["TimeConstant"] = hexdec(substr($Info["RawSetup"], e00391102B_TC, 2));
				if ($Info["TimeConstant"] == 0) $Info["TimeConstant"] = hexdec(substr($Info["RawSetup"], e00391102B_TC, 4));
			} else {
				$Info["TimeConstant"] = 0;
			}

            $Info['DriverInfo'] = substr($Info["RawSetup"], e00391102B_TC);
//			$Info["Function"] = "Temperature/Moisture Sensor";	
			$start = strlen($Info["RawSetup"]) - (2*$this->config[$Info["FWPartNum"]]["Sensors"]);
			$Info["Types"] = array();
			$Info["Labels"] = array();
			$Info["Units"] = array();
            $Info['params'] = device::decodeParams($Info['params']);


			switch(trim(strtoupper($Info["FWPartNum"]))) {
			case "0039-20-12-C":
				$Info["Types"] = array(0 => 0x70, 1 => 0x70, 2 => 0x71, 3 => 0x72);
			default:
				break;
			}

			for ($i = 0; $i < $this->config[$Info["FWPartNum"]]["Sensors"]; $i++) {
				
				$key = $this->getOrder($Info, $i);
				
				if (!isset($Info['Types'][$i])) {
					if ($start > e00391102B_TC) {
						$Info["Types"][$i] = hexdec(substr($Info["RawSetup"], ($start + ($key*2)), 2));
					} else {
						$Info["Types"][$i] = 0;
					}
				}
//				$Info["Labels"][$i] = $this->labels[$Info["Types"][$i]];
                $Info["unitType"][$i] = $this->driver->sensors->getUnitType($Info["Types"][$i], $Info['params']['sensorType'][$i]);
                $Info["Labels"][$i] = $Info['unitType'][$i]; //$this->driver->sensors->getUnitType($Info["Types"][$i], $Info['params']['sensorType'][$i]);
				$Info["Units"][$i] = $this->driver->sensors->getUnits($Info["Types"][$i], $Info['params']['sensorType'][$i]);	
				$Info["dType"][$i] = $this->driver->sensors->getUnitDefMode($Info["Types"][$i], $Info['params']['sensorType'][$i], $Info["Units"][$i]);	
                $Info["doTotal"][$i] = $this->driver->sensors->doTotal($Info["Types"][$i], $Info['params']['sensorType'][$i]);

			}
			return($Info);
		}
		function getOrder($Info, $key, $rev = FALSE) {
			if (isset($this->config[$Info["FWPartNum"]]["DisplayOrder"])) { 
				$Order = explode(",", $this->config[$Info["FWPartNum"]]["DisplayOrder"]);
				if ($rev) $Order = array_flip($Order);
				$ukey = $Order[$key];
			} else {
				$ukey = $key;
			}
			return $ukey;
		}


//		function DecodeData($data) {
		function InterpSensors($Info, $Packets) {
			$Info = $this->InterpConfig($Info);
			$ret = array();

			foreach($Packets as $key => $data) {
				$this->checkDataArray($data);
				if(isset($data['RawData'])) {
				    $index = 0;
            		$data['NumSensors'] = $Info['NumSensors'];
            		$data["ActiveSensors"] = $Info["ActiveSensors"];
            		$data["Driver"] = $Info["Driver"];
            		$data["DeviceKey"] = $Info["DeviceKey"];
            		$data["Types"] = $Info["Types"];
            		$data["DataIndex"] = $data["Data"][$index++];
            		$oldtc = $data["Data"][$index++];  // There is nothing here.
            		$data["TimeConstant"] = $data["Data"][$index++];
        	    	if ($data["TimeConstant"] == 0) $data["TimeConstant"] = $oldtc;


					if (is_array($data["Data"])) {
						for ($i = 0; $i < $Info["NumSensors"]; $i++) {
							$key = $this->getOrder($Info, $i, TRUE);
							switch ($data["Types"][$key]) {
								case 1:
									$data["raw"][$key] = $data["Data"][$index++];
									$data["raw"][$key] += $data["Data"][$index++] << 8;
									break;											
								case 0x72:
									$d = $data["Data"][$index++];
									$d = $d ^ 0xF0;  // invert the top half of the value.
									$data["raw"][$key] = $d;
									break;											
								case 0x2:
								case 0x0:
								case 0x30:
								case 0x70:
								case 0x71:
								case 0x10:
								default:
									$data["raw"][$key] = $data["Data"][$index++];
									$data["raw"][$key] += $data["Data"][$index++] << 8;
									$data["raw"][$key] += $data["Data"][$index++] << 16;
									break;
		
							}					
		
						}
						
					}
    				$this->driver->sensors->decodeData($Info, $data);
                    $this->checkRecord($Info, $data);
    				$ret[] = $data;
				}
			}
		
			return($ret);
		}
	


		/**
			@brief Constructor
			@param $db String The database to use
			@param $servers Array The servers to use.
			@param $options the database options to use.
		*/	
		function e00391200 (&$driver) {
//			$this->eDEFAULT($servers, $db, $options);
            parent::eDEFAULT($driver);
//			$this->packet =& $driver->packet;
			$this->R = new resistiveSensor(65536, 65536, 1<<6, 1023);
			$this->C = new capacitiveSensor(65536, 65536, 1<<6, 1023);
			$this->Light = new lightSensor(65536, 65536, 1<<6, 1023);
			$this->Moisture = new moistureSensor();
			$this->V = new voltageSensor(65536, 65536, 1<<6, 1023);
		}



	}

}

?>
