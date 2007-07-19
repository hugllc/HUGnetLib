<?php
/**
	$Id$
	@file drivers/endpoints/00391201.inc.php
	@brief Driver for 0039-12-XX hardware
	
	
	
*/
if (!class_exists("e00391201")) {

/*
define("e00391106", TRUE);
define("e00391102B_EEPROM_READ", "0A");
define("e00391102B_SRAM_READ", "0B");
define("e00391106_SETCONFIG", "6F");
define("e00391106_SETFET0", "6E");
define("e00391106_SETFET1", "6D");
define("e00391106_SETFET2", "6C");
define("e00391106_SETFET3", "6B");

define("e00391102B_SENSOR_LENGTH", 28);
define("e00391103B_SENSOR_LENGTH", 33);
define("e00391102B_GROUP", 36);
*/
define("e00391201_SETUP", 44);
define("e00391201_FET0", 46);
define("e00391201_FET1", 48);
define("e00391201_FET2", 50);
define("e00391201_FET3", 52);
define("e00391201_FET0_MULT", 54);
define("e00391201_FET1_MULT", 56);
define("e00391201_FET2_MULT", 58);
define("e00391201_FET3_MULT", 60);
define("e00391201_SENSORS", 9);

	$this->add_generic(array("Name" => "e00391201", "Type" => "driver", "Class" => "e00391201", "deviceJOIN" => "e00391200_location"));



	/**
		@brief Driver for the 0039-12 endpoint board and select firmwares
	*/
	class e00391201 extends eDEFAULT{

        var $HWName = "0039-12 Endpoint";

        var $average_table = "e00391201_average";
        var $history_table = "e00391201_history";

		var $devices = array(
		
			"0039-11-06-A" => array(
				"0039-12-00-A" => "BAD",
				"0039-12-01-A" => "BAD",
				"0039-12-02-A" => "BAD",
				"0039-12-01-B" => "DEFAULT",
				"0039-12-02-B" => "DEFAULT",
			),
			"0039-11-07-A" => array(
				"0039-12-00-A" => "BAD",
				"0039-12-01-A" => "BAD",
				"0039-12-02-A" => "BAD",
				"0039-12-01-B" => "DEFAULT",
				"0039-12-02-B" => "DEFAULT",
			),
			"0039-11-08-A" => array(
				"0039-12-01-B" => "DEFAULT",
				"0039-12-02-B" => "DEFAULT",
			),
			"0039-20-04-C" => array(
				"0039-12-02-B" => "DEFAULT",
			),
			"0039-20-05-C" => array(
				"0039-12-02-B" => "DEFAULT",
			),

		);
		
		var $modes = array(
			0 => 'Digital', 
			1 => 'Analog - High Z', 
			2 => 'Analog - Voltage', 
			3 => 'Analog - Current'
		);
		
		var $deflocation = array(
			"0039-20-04-C" => array('Fan 1', 'Fan 2', 'Fan 3', 'Fan 4'),		
			'DEFAULT' => array("Out 1", "Out 2", "Out 3", "Out 4"),
		);

		var $config = array(
			"0039-11-06-A" => array("Function" => "Fan Controller", "Sensors" => 9, "MainV" => 8),		
			"0039-20-04-C" => array("Function" => "Fan Controller", "Sensors" => 9, "MainV" => 8),		
			"0039-11-07-A" => array("Function" => "Power Controller", "Sensors" => 9, "MainV" => 8),
			"0039-11-08-A" => array("Function" => "Water Level Controller", "Sensors" => 5, "MainV" => 4),
			"0039-20-05-C" => array("Function" => "Water Level Controller", "Sensors" => 5, "MainV" => 4),

		);

		var $caldata = array(
			"DEFAULT" => array(1.79, 16, 1.79, 16, 1.79, 16, 1.79, 16, 16),
			"0039-20-04-C" => array(3.58, 32, 3.58, 32, 3.58, 32, 3.58, 32, 32),
			"0039-20-05-C" => array(3.58, 32, 3.58, 32, 32),
			);
		var $labels = array(
			"DEFAULT" => array("Out1 Current", "Out1 Voltage", "Out2 Current", "Out2 Voltage", "Out3 Current","Out3 Voltage", "Out4 Current", "Out4 Voltage", "Main Voltage"),
			"0039-20-05-C" => array("Out3 Current","Out3 Voltage", "Out4 Current", "Out4 Voltage", "Main Voltage"),
			);
		var $units = array(
			"DEFAULT" => array("A", "V", "A", "V", "A", "V", "A", "V", "V"),
			"0039-20-05-C" => array("A", "V", "A", "V", "V"),
			);
		var $cols = array("FET0pMode" => "FET 0 Mode", 
								"FET1pMode" => "FET 1 Mode", 
								"FET2pMode" => "FET 2 Mode", 
								"FET3pMode" => "FET 3 Mode", 
								"NumSensors" => "# Sensors",
								);

		var $configvars = array(
			"FET0Mode" => array(
				"Name" => "FET0 Mode", 
				"Input" => array(
					'type' => 'select',
					'attrib' => array('0' => 'Digital', 2 => 'Analog - Voltage', 3 => 'Analog - Current', 1 => 'Analog - Hi Z'),
				),
				"Start" => 4, 
				"Length" => 1,
				"Shift" => 0,
				"Mask" => 0x03,
			),
			"FET0" => array(
				"Name" => "FET0 Set Point", 
				"Input" => array(
					'type' => 'text',
					'attrib' => array('size' => 5, 'maxlength' => 5),
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
				"Start" => 5, 
				"Length" => 1, 
			),
			"FET1Mode" => array(
				"Name" => "FET1 Mode", 
				"Input" => array(
					'type' => 'select',
					'attrib' => array(0 => 'Digital', 2 => 'Analog - Voltage', 3 => 'Analog - Current', 1 => 'Analog - Hi Z'),
				),
				"Start" => 4, 
				"Length" => 1, 
				"Shift" => 2,
				"Mask" => 0x03,
			),
			"FET1" => array(
				"Name" => "FET1 Set Point", 
				"Input" => array(
					'type' => 'text',
					'attrib' => array('size' => 5, 'maxlength' => 5),
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
				"Start" => 6,
				"Length" => 1, 
			),
			"FET2Mode" => array(
				"Name" => "FET2 Mode", 
				"Input" => array(
					'type' => 'select',
					'attrib' => array(0 => 'Digital', 2 => 'Analog - Voltage', 3 => 'Analog - Current', 1 => 'Analog - Hi Z'),
				),
				"Start" => 4, 
				"Length" => 1, 
				"Shift" => 4,
				"Mask" => 0x03,
			),
			"FET2" => array(
				"Name" => "FET2 Set Point", 
				"Input" => array(
					'type' => 'text',
					'attrib' => array('size' => 5, 'maxlength' => 5),
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
				"Start" => 7,
				"Length" => 1, 
			),
			"FET3Mode" => array(
				"Name" => "FET3 Mode", 
				"Input" => array(
					'type' => 'select',
					'attrib' => array(0 => 'Digital', 2 => 'Analog - Voltage', 3 => 'Analog - Current', 1 => 'Analog - Hi Z'),
				),
				"Start" => 4, 
				"Length" => 1,
				"Shift" => 6,
				"Mask" => 0x3,
			),
			"FET3" => array(
				"Name" => "FET3 Set Point", 
				"Input" => array(
					'type' => 'text',
					'attrib' => array('size' => 5, 'maxlength' => 5),
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
				"Start" => 8,
				"Length" => 1, 
			),

		);

		function ReadMem($Info) {
		
			switch($Info["MemType"]) {
				case EEPROM:
					$Type = e00391102B_EEPROM_READ;
					break;
				case SRAM:
				default:
					$Type = e00391102B_SRAM_READ;
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
			$Rec['StatusOld'] = $Rec['Status'];
			if (empty($Rec['RawData'])) {
				$Rec["Status"] = 'BAD';
				return $Rec;
			} else {
				$Rec["Status"] = 'GOOD';
			}
			if (isset($Rec["Data8"]) && ($Rec["Data8"] == 0)) $Rec["Status"] = BAD;
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
		
			return($Rec);	
		}

		function InterpConfig($Info) {

            $Info['HWName'] = $this->HWName;
			if ($this->devices[$Info["FWPartNum"]][$Info["HWPartNum"]] == "BAD") {
				$Info["NumSensors"] = $this->config[$Info["FWPartNum"]]["Sensors"];	
				$Info["Function"] = "Incompatible Hardware";			
			} else {
				$Info["NumSensors"] = $this->config[$Info["FWPartNum"]]["Sensors"];	
				$Info["Function"] = $this->config[$Info["FWPartNum"]]["Function"];
			}
            $Info['DriverInfo'] = substr($Info["RawSetup"], e00391102B_TC);
			$Info["ActiveSensors"] = $Info["NumSensors"];
			$Info["Setup"] = hexdec(substr($Info["RawSetup"], e00391201_SETUP, 2));
			for($i = 0; $i < 4; $i++) {
				$mode = (($Info["Setup"]>>($i*2)) & 3);
				$Info["FET".$i."Mode"] = $mode;
				$Info["FET".$i."pMode"] = $this->modes[$mode];
			}					

			$Info["FET0"] = hexdec(substr($Info["RawSetup"], e00391201_FET0, 2));
			$Info["FET1"] = hexdec(substr($Info["RawSetup"], e00391201_FET1, 2));
			$Info["FET2"] = hexdec(substr($Info["RawSetup"], e00391201_FET2, 2));
			$Info["FET3"] = hexdec(substr($Info["RawSetup"], e00391201_FET3, 2));
			$Info["FET0Mult"] = hexdec(substr($Info["RawSetup"], e00391201_FET0_MULT, 2));
			$Info["FET1Mult"] = hexdec(substr($Info["RawSetup"], e00391201_FET1_MULT, 2));
			$Info["FET2Mult"] = hexdec(substr($Info["RawSetup"], e00391201_FET2_MULT, 2));
			$Info["FET3Mult"] = hexdec(substr($Info["RawSetup"], e00391201_FET3_MULT, 2));
			
			if (isset($this->labels[$Info["FWPartNum"]])) {
				$Info["Labels"] = $this->labels[$Info["FWPartNum"]];
			} else {
				$Info["Labels"] = $this->labels["DEFAULT"];			
			}
			if (isset($this->units[$Info["FWPartNum"]])) {
				$Info["Units"] = $this->units[$Info["FWPartNum"]];
			} else {
				$Info["Units"] = $this->units["DEFAULT"];			
			}
			if (isset($this->deflocation[$Info["FWPartNum"]])) {
				$Info["Location"] = $this->deflocation[$Info["FWPartNum"]];
			} else {
				$Info["Location"] = $this->deflocation['DEFAULT'];			
			}

			return($Info);
		}


	
		function InterpSensors($Info, $Packets) {

			$Info = $this->InterpConfig($Info);
		
			$ret = array();
			foreach($Packets as $data) {
				if (isset($data['RawData'])) {
					$data = $this->checkDataArray($data);
	//				$data = $this->InterpConfig($data);
					$return = $data;
					$index = 0; 
					$return["ReplyTime"] = $data["ReplyTime"];
					$return["RawData"] = $data["RawData"];
					$return["DataIndex"] = $data["Data"][$index++];
		         $return["ActiveSensors"] = $Info["ActiveSensors"];
					$return['sendCommand'] = $data['sendCommand'];
					$return['NumSensors'] = $Info['NumSensors'];
		         $return["Driver"] = $Info["Driver"];
		         
					if (!isset($data["Date"])) {
						$return["Date"] = date("Y-m-d H:i:s");
					} else {
						$return["Date"] = $data["Date"];
					}
					$return["DeviceKey"] = $Info["DeviceKey"];
					if (is_array($data["Data"])) {
						for ($key = 0; $key < $Info["NumSensors"]; $key++) {
							$return["raw"][$key] = $data["Data"][$index++];
							$return["raw"][$key] += $data["Data"][$index++] << 8;
						}
						
					}	
					if (is_array($return["raw"])) {
						
						/* This sets up what we should see from the double poll averager */
						foreach($return["raw"] as $rawkey => $rawval) {
							if (isset($this->caldata[$Info["FWPartNum"]])) {
								$caldata = $this->caldata[$Info["FWPartNum"]][$rawkey];
							} else {
								$caldata = $this->caldata["DEFAULT"][$rawkey];
							}
							$return["cal"][$rawkey] = (($rawval / 0xFFFF) * 2.56) * $caldata;
						}
						/* This changes the voltage across the FET into the output voltage */
						/* Vo = Vmain - Vf */
						foreach($return["cal"] as $calkey => $calval) {
							if (($calkey == 1) || ($calkey == 3) || ($calkey == 5) || ($calkey == 7)) {
							
								$MainV = $return["cal"][$this->config[$Info["FWPartNum"]]["MainV"]];
								$tdata = $MainV - $calval;
							} else {
								$tdata = $calval;
							}
							//$data = number_format($data, 3);
							$return["cal"][$calkey] = $calval;
	//						$return[$calkey] = $tdata;
							$return["Data".$calkey] = $tdata;
		
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
		function e00391201 (&$driver) {
//			$this->eDEFAULT($servers, $db, $options);
            parent::eDEFAULT($driver);
			$this->packet =& $driver->packet;
		}
	}
}


?>
