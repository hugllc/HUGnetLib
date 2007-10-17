<?php
/**
 *   Driver for the 0039-12 endpoints with the FET daughter board.
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
 *   @subpackage Endpoints
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$    
 *
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
			'0039-20-04-C' => array("Fan 1", "Fan 1", "Fan 2", "Fan 2", "Fan 3", "Fan 3", "Fan 4", "Fan 4", "Main"),
			'DEFAULT' => array("Out 1 Current", "Out 1 Voltage", "Out 2 Current", "Out 2 Voltage", "Out 3 Current", "Out 3 Voltage", "Out 4 Current", "Out 4 Voltage", "Main Voltage"),
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
		var $types = array(
			"DEFAULT" => array(0x50, 0x40, 0x50, 0x40, 0x50, 0x40, 0x50, 0x40, 0x40),
        );
		var $sensorTypes = array(
            "DEFAULT" => array("FETBoard", "FETBoard", "FETBoard", "FETBoard", "FETBoard", "FETBoard", "FETBoard", "FETBoard", "FETBoard"),
        );

		var $cols = array("FET0pMode" => "FET 0 Mode", 
								"FET1pMode" => "FET 1 Mode", 
								"FET2pMode" => "FET 2 Mode", 
								"FET3pMode" => "FET 3 Mode", 
								"NumSensors" => "# Sensors",
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


		function CheckRecord($Info, &$Rec) {
		    if (isset($Rec['Status'])) {
    			$Rec['StatusOld'] = $Rec['Status'];
            }
			if (empty($Rec['RawData'])) {
				$Rec["Status"] = 'BAD';
				return;
		    }
            if ($Rec['Status'] == "NEW") $Rec['Status'] = "GOOD";    		
		    
			if (isset($Rec["Data8"]) && ($Rec["Data8"] == 0)) $Rec["Status"] = BAD;
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
		}

		function InterpConfig(&$Info) {

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

            $Info['params'] = device::decodeParams($Info['params']);
    
            $Info["Types"] = (isset($this->types[$Info["FWPartNum"]])) ? $this->types[$Info["FWPartNum"]] : $this->types["DEFAULT"];
            for($i = 0; $i < $Info['ActiveSensors']; $i++) {
                $Info["unitType"][$i] = $this->driver->sensors->getUnitType($Info["Types"][$i], $Info['params']['sensorType'][$i]);
                $Info["Labels"][$i] = $Info['unitType'][$i]; //$this->driver->sensors->getUnitType($Info["Types"][$i], $Info['params']['sensorType'][$i]);
    	        $Info["Units"][$i] = $this->driver->sensors->getUnits($Info["Types"][$i], $Info['params']['sensorType'][$i]);	
    		    $Info["dType"][$i] = $this->driver->sensors->getUnitDefMode($Info["Types"][$i], $Info['params']['sensorType'][$i], $Info["Units"][$i]);	
                $Info["doTotal"][$i] = $this->driver->sensors->doTotal($Info["Types"][$i], $Info['params']['sensorType'][$i]);
            }			
			if (isset($this->labels[$Info["FWPartNum"]])) {
				$Info["Labels"] = $this->labels[$Info["FWPartNum"]];
			} else {
				$Info["Labels"] = $this->labels["DEFAULT"];			
			}
			return($Info);
		}


	
		function InterpSensors($Info, $Packets) {

			$Info = $this->InterpConfig($Info);
		
			$ret = array();
			foreach($Packets as $data) {
			    $data = $this->checkDataArray($data);
				if (isset($data['RawData'])) {
				    $index = 0;
            		$data['NumSensors'] = $Info['NumSensors'];
            		$data["ActiveSensors"] = $Info["ActiveSensors"];
            		$data["Driver"] = $Info["Driver"];
            		$data["DeviceKey"] = $Info["DeviceKey"];
            		$data["Types"] = $Info["Types"];
            		$data["DataIndex"] = $data["Data"][$index++];
            		$data["TimeConstant"] = 1;
		         
					if (is_array($data["Data"])) {
						for ($key = 0; $key < $Info["NumSensors"]; $key++) {
							$data["raw"][$key] = $data["Data"][$index++];
							$data["raw"][$key] += $data["Data"][$index++] << 8;
						}
						
					}	
    				$this->driver->sensors->decodeData($Info, $data);
                    // This changes the voltage across the FET into the output voltage
                    // Vo = Vmain - Vf 
                    $data["Data1"] = $data['data'][1] = $data["Data8"] -  $data["Data1"];
                    $data["Data3"] = $data['data'][3] = $data["Data8"] -  $data["Data3"];
                    $data["Data5"] = $data['data'][5] = $data["Data8"] -  $data["Data5"];
                    $data["Data7"] = $data['data'][7] = $data["Data8"] -  $data["Data7"];

                    // Check everything
                    $this->checkRecord($Info, $data);
    				$ret[] = $data;

				}
			}
			return $ret;
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
