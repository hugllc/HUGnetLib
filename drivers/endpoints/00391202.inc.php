<?php
/**
	$Id$
	@file drivers/endpoints/00391201.inc.php
	@brief Driver for 0039-12-XX hardware
	
	
	
*/
if (!class_exists("e00391202")) {

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
define("e00391202_RELAY0", 44);
define("e00391202_RELAY1", 46);
define("e00391202_SENSORS", 0);

	$this->add_generic(array("Name" => "e00391202", "Type" => "driver", "Class" => "e00391202", "deviceJOIN" => "e00391200_location"));



	/**
		@brief Driver for the 0039-12 endpoint board and select firmwares
	*/
	class e00391202 extends eDEFAULT{
        var $HWName = "0039-12 Endpoint";

        var $average_table = "e00391200_average";
        var $history_table = "e00391200_history";

		var $devices = array(
			"0039-20-09-C" => array(
				"0039-12-02-B" => "DEFAULT",
				"0039-12-01-B" => "DEFAULT",
			),

		);
		
		var $deflocation = array(
			"0039-20-09-C" => array('Relay 1', 'Relay 2',),		
		);

		var $config = array(
			"0039-20-09-C" => array("Function" => "Relay Board", "Sensors" => 0, "MainV" => 4),
		);

		var $caldata = array(
//			"DEFAULT" => array(1.79, 16, 1.79, 16, 1.79, 16, 1.79, 16, 16),
			);
		var $labels = array(
			"DEFAULT" => array("Relay 1", "Relay 2",),
			);
		var $units = array(
//			"DEFAULT" => array("A", "V", "A", "V", "A", "V", "A", "V", "V"),
//			"0039-20-05-C" => array("A", "V", "A", "V", "V"),
			);
		var $cols = array("NumSensors" => "# Sensors",
								);



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
			$Info["ActiveSensors"] = $Info["NumSensors"];

			$Info["RELAY0"] = hexdec(substr($Info["RawSetup"], e00391202_RELAY0, 2));
			$Info["RELAY1"] = hexdec(substr($Info["RawSetup"], e00391202_RELAY1, 2));
			
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
		function e00391202 (&$driver) {
			$this->packet =& $driver->packet;
		}
	}
}

?>
