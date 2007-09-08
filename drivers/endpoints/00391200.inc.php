<?php
/**
	$Id$
	@file drivers/endpoints/00391200.inc.php
	@brief Driver for the 0039-12-XX boards.
		
*/
require_once(HUGNET_INCLUDE_PATH."/sensors/resistive.inc.php");
require_once(HUGNET_INCLUDE_PATH."/sensors/light.inc.php");
require_once(HUGNET_INCLUDE_PATH."/sensors/moisture.inc.php");
require_once(HUGNET_INCLUDE_PATH."/sensors/capacitive.inc.php");
require_once(HUGNET_INCLUDE_PATH."/sensors/light.inc.php");
require_once(HUGNET_INCLUDE_PATH."/sensors/voltage.inc.php");


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

		var $configvars = array(
			"TimeConstant" => array(
				"Name" => "Time Constant", 
				"Input" => array(
					'type' => 'text',
					'attrib' => array('size' => 3, 'maxlength' => 3),
					'rule' => array(
						array(
							'type' => 'numeric',
							'message' => 'Time Constant must be numeric',
						),
						array(
							'type' => 'required',
							'message' => 'Time Constant can not be empty',
						),
					),
				),
				"Start" => 4, 
				"Length" => 1, 
				"Range" => "1-255"
			),
		);
			
//			0 => array(-40 => 332.1, -35 => 240, -30 => 175.2, -25 => 129.3, -20 => 96.36, -15 => 72.5, -10 => 55.05, -5 => 42.16, 0 => 32.56, 5 => 25.34, 10 => 19.87, 15 => 15.70, 20 => 12.49, 25 => 10.00, 30 => 8.059, 35 => 6.535, 40 => 5.330, 45 => 4.372, 50 => 3.606, 55 => 2.989, 60 => 2.490, 65 => 2.084, 70 => 1.753, 75 => 1.481, 80 => 1.256, 85 => 1.070, 90 => 0.9154, 95 => 0.7860, 100 => 0.6773, 105 => 0.5858, 110 => 0.5083, 115 => 0.4426, 120 => 0.3866, 125 => 0.3387),
		/**
			@brief Calibration data
		*/
		var $caldata = array(
			0 => array(-40 => 50304, -35 => 46208, -30 => 41664, -25 => 36928, -20 => 32128, -15 => 27520, -10 => 23232, -5 => 19392, 0 => 16064, 5 => 13248, 10 => 10880, 15 => 8896, 20 => 7296, 25 => 5952, 30 => 4864, 35 => 4032, 40 => 3328, 45 => 2752, 50 => 2304, 55 => 1920, 60 => 1600, 65 => 1344, 70 => 1152, 75 => 960, 80 => 832, 85 => 704, 90 => 576, 95 => 512, 100 => 448, 105 => 384, 110 => 320, 115 => 320, 120 => 256, 125 => 192, 130 => 192, 135 => 192, 140 => 128, 145 => 128, 150 => 128),
			1 => array(0 => 65472, 5 => 65408, 10 => 65152, 15 => 64256, 20 => 61120, 25 => 51712, 30 => 32768, 35 => 13760, 40 => 4352, 45 => 1216, 50 => 320, 55 => 64),
			);
		var $labels = array(
		    0 => "Temp", 
		    1 => "Moisture", 
		    2 => "Temp", 
		    0x10 => "Relative Humidity", 
		    0x20 => "Capacitance", 
		    0x30 => "Light", 
		    0x70 => "Revolutions", 
		    0x71 => "HalfRevolutions", 
		    0x72 => 'Numeric Direction',
		    0x73 => 'Pulse Counter',
		    );  //!< Default labels for the sensor inputs
		var $units = array(
		    0 => "&#176;C", 
		    1 => "%", 
		    2 => "&#176;C", 
		    0x10 => "%", 
		    0x20 => "pF", 
		    0x30 => "W/m^2", 
		    0x70 => "Revs", 
		    0x71 => "HalfRevs", 
		    0x72 => 'numDir',
		    0x73 => 'counts',
		); //!< Default units for the sensor inputs

		var $sensorTypes = array(
			0 => "Temperature 100k Bias", 
			1 => "Moisture", 
			2 => "Temperature 10k Bias", 
			0x10 => "Relative Humidity", 
			0x20 => "Capacitance",
			0x70 => "Pulse Counter",
			0x73 => "Pulse Counter",
		);  //!< Default labels for the sensor inputs
	
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

		
	function CheckRecord($Info, $Rec) {
	
//	print get_stuff($Rec);
		$Rec['StatusOld'] = $Rec['Status'];
		if (empty($Rec['RawData'])) {
			$Rec["Status"] = 'BAD';
			return $Rec;
		} else {
			$Rec["Status"] = 'GOOD';
		}
		$Bad = 0;
		// This checks if the data is in range.
		if (is_array($Rec["data"])) {
			foreach($Rec["data"] as $key => $value) {
				if ($key < $Rec["ActiveSensors"]) {
					switch ($Info["Types"][$key]) {
						case 1:
							break;											
						case 2:		// These thermistors only go from -40 to +150
						case 0:		// These thermistors only go from -40 to +150
							if (($value > 150) || ($value < -40)) {
								$Bad++;
								$Rec['Data'.$key] = NULL;
							}
							break;
					    case 0x10:
							if (($value > 100) || ($value < 0)) {
								$Bad++;
								$Rec['Data'.$key] = NULL;
							}
							break;					        
						default:
							break;
					}
				} else {
					$Rec['Data'.$key] = NULL;
				}
			}
		}
		$zero = TRUE;
		for($i = 0; $i < $Rec['NumSensors']; $i ++) {
			if ($Rec['Data'.$i] != 0) {
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
		
		return($Rec);	
	}
	
		
		function InterpConfig($Info) {
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
				$Info["Labels"][$i] = $this->labels[$Info["Types"][$i]];
				$Info["Units"][$i] = $this->units[$Info["Types"][$i]];	

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
				$data = $this->checkDataArray($data);
				if(isset($data['RawData'])) {

	//				$data = $this->InterpConfig($data);
					$return = $data;
					$index = 0; 
					$return["ReplyTime"] = $data["ReplyTime"];
					$return["RawData"] = $data["RawData"];
					$return['sendCommand'] = $data['sendCommand'];
					$return['NumSensors'] = $Info['NumSensors'];
					$return["DataIndex"] = $data["Data"][$index++];
		//			$return["TimeConstant"] = $data["data"][$index++];
					$oldtc = $data["Data"][$index++];  // There is nothing here.
					$return["TimeConstant"] = $data["Data"][$index++];
					$return["ActiveSensors"] = $Info["ActiveSensors"];
					$return["Driver"] = $Info["Driver"];
					if ($return["TimeConstant"] == 0) $return["TimeConstant"] = $oldtc;
					if (!isset($data["Date"])) {
						$return["Date"] = date("Y-m-d H:i:s");
					} else {
						$return["Date"] = $data["Date"];
					}
					$return["DeviceKey"] = $Info["DeviceKey"];
					$return["Types"] = $Info["Types"];

					if (is_array($data["Data"])) {
						for ($i = 0; $i < $Info["NumSensors"]; $i++) {
							$key = $this->getOrder($Info, $i, TRUE);

							switch ($Info["Types"][$key]) {
								case 1:
									$return["raw"][$key] = $data["Data"][$index++];
									$return["raw"][$key] += $data["Data"][$index++] << 8;
									break;											
								case 0x72:
									$d = $data["Data"][$index++];
									$d = $d ^ 0xF0;  // invert the top half of the value.
									$return["raw"][$key] = $d;
									break;											
								case 2:
								case 0:
								case 0x30:
								case 0x70:
								case 0x71:
								case 0x10:
								default:
									$return["raw"][$key] = $data["Data"][$index++];
									$return["raw"][$key] += $data["Data"][$index++] << 8;
									$return["raw"][$key] += $data["Data"][$index++] << 16;
									break;											
		
							}					
		
						}
						
					}
					if (is_array($return["raw"])) {
						/* This sets up what we should see from the double poll averager */
		
						foreach($this->caldata as $type => $val) {
							foreach ($val as $key => $value) {
								switch($type) {
									case 1:
										$cal[$type][$key] = (int) $value;
										break;
									default:
										$cal[$type][$key] = (int)($value * ($return["TimeConstant"]/256));
										break;
								}
							}
							$cal[$type] = array_flip($cal[$type]);
						}
						foreach($return["raw"] as $rawkey => $rawval) {
							unset($lastkey);
							if (!is_array($cal[$Info["Types"][$rawkey]])) $cal[$Info["Types"][$rawkey]] = array();
//print $rawval." => ".$Info["Types"][$rawkey]."\n";
							switch($Info["Types"][$rawkey]) {
								case 0:
									$ohms = $this->R->getResistance($rawval, $return["TimeConstant"], 100);
	//								$return["Data".$rawkey] = $ohms;
									$return["Data".$rawkey] = $this->R->getReading($ohms, $data["Types"][$rawkey]);
									break;
								case 1:
									$ohms = $this->R->getResistance($rawval, 1, 100);
									$M = $this->Moisture->getMoisture($ohms);
									$return["Data".$rawkey] = $M;
									break;
								case 2:
									$ohms = $this->R->getResistance($rawval, $return["TimeConstant"], 10);
	//print $ohms."\r\n";
	//								$return["Data".$rawkey] = $ohms;
									$return["Data".$rawkey] = $this->R->getReading($ohms, $data["Types"][$rawkey]);
									break;
								case 0x10:
									$volts = $this->V->getVoltage($rawval, $return["TimeConstant"], 1.1);
									$return["Data".$rawkey] = $volts * 100;
									break;

								case 0x20:
									$farads = $this->C->getCapacitance($rawval, $return["TimeConstant"], 10000, 1);
		//							$return["Data".$rawkey] = $this->R->getReading($ohms, $data["Types"][$rawkey]);
									$return["Data".$rawkey] = $farads;
									break;
								case 0x30:					
//									$light = $this->Light->getLight($rawval, $return["TimeConstant"]);
									$light = $this->Light->getReading($rawval, $Info["Types"][$rawkey], NULL, $return["TimeConstant"]);
									$return["Data".$rawkey] = $light;
                                    if (is_null($return['Units'][$rawkey]))
                                    {
                                        $return['Units'][$rawkey] = $this->Light->getUnits($Info["Types"][$rawkey], $Info['params']['sensorType'][$rawkey]);
                                    }
									break;
								case 0x70:
								case 0x71:
								case 0x72:
								default:
									$return["Data".$rawkey] = $rawval;
									break;
							}
//print $return["Data".$rawkey]."\n";
		
							$return["data"][$rawkey] = $return["Data".$rawkey];
						}
					}
					$return = $this->CheckRecord($Info, $return);
					$ret[] = $return;
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
