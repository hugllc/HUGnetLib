<?php
/**
	$Id$
	@file drivers/endpoints/00392100.inc.php
	@brief Driver for the 0039-21-00 Controller boards
	
	
	
*/
require_once HUGNET_INCLUDE_PATH.'/firmware.inc.php';

if (!class_exists("e00392100")) {


/** Reads the downstream unit serial numbers */
define('PACKET_READDOWNSTREAMSN_COMMAND', '56');
/** Reads the downstream unit serial numbers */
define('PACKET_READPACKETSTATS_COMMAND', '57');
/** Reads the downstream unit serial numbers */
define('PACKET_HUGNETPOWER_COMMAND','60');

$this->add_generic(array("Name" => "e00392100", "Type" => "driver", "Class" => "e00392100", "deviceJOIN" => ""));



    /**
    	@brief Driver for the 0039-21 controller board
    */
    class e00392100 extends eDEFAULT{
    
        var $HWName = "Controller Board";
    
        var $average_table = "e00392100_average";
        var $history_table = "e00392100_history";
    
    	var $devices = array(
    		"DEFAULT" => array(
    			"0039-21-01-A" => "DEFAULT",
    		),
    	);
    	var $labels = array(
    		"DEFAULT" => array("HUGnet1 Voltage", "HUGnet1 Current", "FET Temp", "HUGnet2 Voltage", "HUGnet2 Current", "FET Temp"),
    	);
    	var $units = array(
    		"DEFAULT" => array("V", "A", "&#176;C", "V", "A", "&#176;C"),
    	);
    
    	var $deflocation = array(
    		'DEFAULT' => array("HUGnet 1", "HUGnet 2"),
    	);
    
    	var $config = array(
    		"DEFAULT" => array("Function" => "HUGnet Controller", "Sensors" => 6, "SensorLength" => 33),		
    	);
    		
    	var $cols = array(
    		"NumSensors" => "# Sensors",
    	);
    
    	function ReadConfig($Info) {
     		$packet = array(
    		    0 => array(
    	    		"To" => $Info["DeviceID"],
    	    		"Command" => PACKET_COMMAND_GETSETUP,
    	    	),
    		    1 => array(
    	    		"To" => $Info["DeviceID"],
    	    		"Command" => PACKET_COMMAND_GETCALIBRATION,
    	    	),
    	    );
    		switch ($Info['FWPartNum']) 
    		{
    			case '0039-20-06-C':
    			case '0039-20-01-C':
    				$packet[] = array(
    					"To" => $Info["DeviceID"],
    					"Command" => PACKET_READDOWNSTREAMSN_COMMAND,
    				);
    				$packet[] = array(
    					"To" => $Info["DeviceID"],
    					"Command" => PACKET_HUGNETPOWER_COMMAND,
    				);
    				break;
    			default:
    				break;
    		};
    		$Packets = $this->packet->SendPacket($Info, $packet);
    		return($Packets);
    	}
    
    	function CheckRecord($Info, $Rec) {
    		$Rec['StatusOld'] = $Rec['Status'];
    		if (empty($Rec['RawData'])) {
    			$Rec["Status"] = 'BAD';
    			return $Rec;
    		} else {
    			$Rec["Status"] = 'GOOD';
    		}
    		for($key = 0; $key < $Rec['ActiveSensors']; $key++) {
    			switch ($key) {
    				case 5:		// These thermistors only go from -40 to +150
    				case 2:		// These thermistors only go from -40 to +150
    
    					if (($Rec['Data'.$key] > 150) || ($Rec['Data'.$key] < -40)) {
    						$Bad++;
    						$Rec['Data'.$key] = NULL;
    					}
    					break;
    				default:
    					break;
    			}
    		}
    		
    		$zero = TRUE;
    		for($i = 0; $i < $Rec['NumSensors']; $i ++) {
    			if (!is_numeric($Rec['Data'.$i])) {
    				$Rec['Data'.$key] = NULL;
    			} else if (!empty($Rec['Data'.$i])) {
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
    
    	function ReadSensors($Info) {
    		$packet[0] = array(
    			"Command" => eDEFAULT_SENSOR_READ,
    			"To" => $Info["DeviceID"],
    		);
    		switch ($Info['FWPartNum']) 
    		{
    			case '0039-20-06-C':
    			case '0039-20-01-C':
    				$packet[1] = array(
    					"To" => $Info["DeviceID"],
    					"Command" => PACKET_READPACKETSTATS_COMMAND,
    				);
    				break;
    			default:
    				break;
    		};
    
    		$Info['sendCommand'] = PACKET_SEND_COMMAND;
//    		$Info = $this->InterpConfig($Info);
    
    		$Packets = $this->packet->SendPacket($Info, $packet);
    		if (is_array($Packets)) {
    			$return = $this->InterpSensors($Info, $Packets);
    //			$return = $Packets;
    		} else {
    			$return = FALSE;
    		}
    		return($return);
    	}
    
    
    	
    /*	
    	function GetInfo($Info) {
    		$Info["Location"] = $this->deflocation;
    
    		If (isset($this->config[$Info["FWPartNum"]])) {
    			$Info["NumSensors"] = $this->config[$Info["FWPartNum"]]["Sensors"];	
    			$Info["Function"] = $this->config[$Info["FWPartNum"]]["Function"];
    		} else {
    			$Info["NumSensors"] = $this->config["DEFAULT"]["Sensors"];	
    			$Info["Function"] = $this->config["DEFAULT"]["Function"];
    		}
    		
    		if ($Info['FWPartNum'] == '0039-20-06-C') {
    			$Info['mcu'] = array();
    			$Info['mcu']["SRAM"] = hexdec(substr($Info["RawSetup"], 44, 4));
    			$Info['mcu']["E2"] = hexdec(substr($Info["RawSetup"], 48, 4));
    			$Info['mcu']["FLASH"] = hexdec(substr($Info["RawSetup"], 52, 6));
    			$Info['mcu']["FLASHPAGE"] = hexdec(substr($Info["RawSetup"], 58, 4));
    			if ($Info['mcu']["FLASHPAGE"] == 0) $Info['mcu']["FLASHPAGE"] = 128;
    			$Info['mcu']["PAGES"] = $Info['mcu']["FLASH"]/$Info['mcu']["FLASHPAGE"];
    			$Info["CRC"] = strtoupper(substr($Info["RawSetup"], 62, 4));
    			$Info['bootLoader'] = TRUE;
    		} else {
    			$Info['bootLoader'] = FALSE;
    		}
    		
    		return($Info);
    	}
    */
    	function InterpConfig($Info) {
    		$return = array();
    
            $packet = $Info;
            $packet['HWName'] = $this->HWName;
			$packet["Location"] = $this->deflocation;
	        $packet["PacketTimeout"] = 2;
			if (isset($this->config[$packet["FWPartNum"]])) {
				$packet["NumSensors"] = $this->config[$packet["FWPartNum"]]["Sensors"];	
				$packet["Function"] = $this->config[$packet["FWPartNum"]]["Function"];
			} else {
				$packet["NumSensors"] = $this->config["DEFAULT"]["Sensors"];	
				$packet["Function"] = $this->config["DEFAULT"]["Function"];
			}

			$packet['ActiveSensors'] = $packet["NumSensors"];
			
			if ($packet['FWPartNum'] == '0039-20-06-C') {
				$packet['mcu'] = array();
				$packet['mcu']["SRAM"] = hexdec(substr($packet["RawSetup"], 44, 4));
				$packet['mcu']["E2"] = hexdec(substr($packet["RawSetup"], 48, 4));
				$packet['mcu']["FLASH"] = hexdec(substr($packet["RawSetup"], 52, 6));
				$packet['mcu']["FLASHPAGE"] = hexdec(substr($packet["RawSetup"], 58, 4));
				if ($packet['mcu']["FLASHPAGE"] == 0) $packet['mcu']["FLASHPAGE"] = 128;
				$packet['mcu']["PAGES"] = $packet['mcu']["FLASH"]/$packet['mcu']["FLASHPAGE"];
				$packet["CRC"] = strtoupper(substr($packet["RawSetup"], 62, 4));
				$packet['bootLoader'] = TRUE;
			} else {
				$packet['bootLoader'] = FALSE;
			}

			if (isset($this->labels[$Info["FWPartNum"]])) {
				$packet["Labels"] = $this->labels[$Info["FWPartNum"]];
			} else {
				$packet["Labels"] = $this->labels["DEFAULT"];			
			}
			if (isset($this->units[$Info["FWPartNum"]])) {
				$packet["Units"] = $this->units[$Info["FWPartNum"]];
			} else {
				$packet["Units"] = $this->units["DEFAULT"];			
			}
			if (isset($this->deflocation[$Info["FWPartNum"]])) {
				$packet["Location"] = $this->deflocation[$Info["FWPartNum"]];
			} else {
				$packet["Location"] = $this->deflocation['DEFAULT'];			
			}

            if (is_array($Info['RawData'])) {
                foreach($Info['RawData'] as $sendCommand => $RawData) {

            		switch($sendCommand) {
            			case PACKET_READDOWNSTREAMSN_COMMAND:
            				$index = 0;
            				$RawData = trim($RawData);
            
            				$strings[0] = substr($RawData, 0, (strlen($RawData)/2));
            				$strings[1] = substr($RawData, (strlen($RawData)/2));
                            $packet['subDevices'] = array();
            				foreach($strings as $str) {
            					for($i = 0; $i < strlen($str); $i += 6) {

            						$id = substr($str, $i, 6);
            						if ((strlen($id) == 6) && ($id != '000000')) {
            							$packet['subDevices'][$index][] = $id;
            						}
            					}
            					$index++;
            				}
            				break;		
            			case PACKET_HUGNETPOWER_COMMAND:		
            				$packet['HUGnetPower'][0] = (hexdec(substr($RawData, 0, 2)) == 0) ? 0 : 1;
            				$packet['HUGnetPower'][1] = (hexdec(substr($RawData, 2, 2)) == 0) ? 0 : 1;
            				break;
            			case PACKET_CONFIG_COMMAND:
            			default:            			
            				break;		
            		}
                }
            }
    		return($packet);
    	}
    	
    	function updateConfig($Info) {
    		$return = TRUE;
			if (is_array($Info['subDevices'])) {
				foreach($Info['subDevices'] as $index => $devList) {
                    $where = implode("' OR DeviceID='", $devList);
                    $where = " DeviceID='".$where."'";
                    $query = "UPDATE devices SET ControllerKey=".$Info['DeviceKey'].", ControllerIndex=".$index." WHERE ".$where;
		            $return = $this->driver->db->query($query);
                    
/*
					foreach($devList as $dev) {
                        $res = $this->driver->getDevice($dev, "ID");
						if (is_array($res)) {
							$update = array(
								'ControllerKey' => $Info['DeviceKey'],
								'ControllerIndex' => $index,
							);
		            	    $return = $this->driver->db->AutoExecute($this->driver->device_table, $update, 'UPDATE', 'DeviceKey='.$res['DeviceKey']);
						}
					}
*/
				}
				$update = array(
					'ControllerKey' => 0,
					'ControllerIndex' => 0,
				);
        	    $return = $this->driver->db->AutoExecute($this->driver->device_table, $update, 'UPDATE', 'DeviceKey='.$Info['DeviceKey']);
				
			}
    	
    		return($return);
    	}
    	
    	
    	function saveSensorData($Info, $Packets) {
    
    		foreach($Packets as $packet) {
    			if (isset($packet['DataIndex'])) {
    				if (($packet["Status"] == "GOOD")){						
    				    $return = $this->driver->db->AutoExecute($this->history_table, $packet, 'INSERT');
    				} else {
    					$return = FALSE;
    				}
    			}
    		}
    		return($return);
    	}
    
    	
    	function InterpSensors($Info, $Packets) {
    		$return = array();
    
    		foreach($Packets as $packet) {
    			if (isset($packet['RawData'])) {
    				$ret = $packet;
    				$packet = $this->checkDataArray($packet);
    	//			$ret["RawData"] = $packet["RawData"];
    	//			$ret['sendCommand'] = $packet['sendCommand'];
    				$ret["Driver"] = get_class($this);
    				if (!isset($packet["Date"])) {
    					$ret["Date"] = date("Y-m-d H:i:s");
    				} else {
    					$ret["Date"] = $packet["Date"];
    				}
    				$ret["DeviceKey"] = $Info["DeviceKey"];
    	
    				switch($packet['sendCommand']) {
    					case PACKET_READPACKETSTATS_COMMAND:
    						$loc = 0;
    						for($index = 0; $index < 3; $index++) {
    							$ret['Stats'][$index]['PacketRX'] = $packet['Data'][$loc++];
    							$ret['Stats'][$index]['PacketRX'] += $packet['Data'][$loc++] * 0x100;
    							$ret['Stats'][$index]['PacketTX'] = $packet['Data'][$loc++];
    							$ret['Stats'][$index]['PacketTX'] += $packet['Data'][$loc++] * 0x100;
    							$ret['Stats'][$index]['PacketTimeout'] = $packet['Data'][$loc++];
    							$ret['Stats'][$index]['PacketTimeout'] += $packet['Data'][$loc++] * 0x100;
    							$ret['Stats'][$index]['PacketNoBuffer'] = $packet['Data'][$loc++];
    							$ret['Stats'][$index]['PacketNoBuffer'] += $packet['Data'][$loc++] * 0x100;
    							$ret['Stats'][$index]['PacketBadCSum'] = $packet['Data'][$loc++];
    							$ret['Stats'][$index]['PacketBadCSum'] += $packet['Data'][$loc++] * 0x100;
    							$ret['Stats'][$index]['PacketSent'] = $packet['Data'][$loc++];
    							$ret['Stats'][$index]['PacketSent'] += $packet['Data'][$loc++] * 0x100;
    							$ret['Stats'][$index]['PacketGateway'] = $packet['Data'][$loc++];
    							$ret['Stats'][$index]['PacketGateway'] += $packet['Data'][$loc++] * 0x100;
    							$ret['Stats'][$index]['PacketStartTX1'] = $packet['Data'][$loc++];
    							$ret['Stats'][$index]['PacketStartTX1'] += $packet['Data'][$loc++] * 0x100;
    							$ret['Stats'][$index]['PacketStartTX2'] = $packet['Data'][$loc++];
    							$ret['Stats'][$index]['PacketStartTX2'] += $packet['Data'][$loc++] * 0x100;
    							$ret['Stats'][$index]['PacketBadIface'] = $packet['Data'][$loc++];
    							$ret['Stats'][$index]['PacketBadIface'] += $packet['Data'][$loc++] * 0x100;
    							$ret['Stats'][$index]['ByteRX'] = $packet['Data'][$loc++];
    							$ret['Stats'][$index]['ByteRX'] += $packet['Data'][$loc++] * 0x100;
    							$ret['Stats'][$index]['ByteRX'] += $packet['Data'][$loc++] * 0x10000;
    							$ret['Stats'][$index]['ByteRX'] += $packet['Data'][$loc++] * 0x1000000;
    							$ret['Stats'][$index]['ByteTX'] = $packet['Data'][$loc++];
    							$ret['Stats'][$index]['ByteTX'] += $packet['Data'][$loc++] * 0x100;
    							$ret['Stats'][$index]['ByteTX'] += $packet['Data'][$loc++] * 0x10000;
    							$ret['Stats'][$index]['ByteTX'] += $packet['Data'][$loc++] * 0x1000000;
    							$ret['Stats'][$index]['ByteTX2'] = $packet['Data'][$loc++];
    							$ret['Stats'][$index]['ByteTX2'] += $packet['Data'][$loc++] * 0x100;
    							$ret['Stats'][$index]['ByteTX2'] += $packet['Data'][$loc++] * 0x10000;
    							$ret['Stats'][$index]['ByteTX2'] += $packet['Data'][$loc++] * 0x1000000;
    						}
    						break;		
    					default:
    						$index = 0; 
    						$ret["ActiveSensors"] = $Info["ActiveSensors"];
    						$ret["NumSensors"] = $Info["NumSensors"];
    						$ret["DataIndex"] = $packet["Data"][$index++];
    						for ($key = 0; $index < count($packet['Data']); $key++) {
    							$ret["raw"][$key] = $packet["Data"][$index++];
    							$ret["raw"][$key] += $packet["Data"][$index++] << 8;
    						}
    						/*
    							Input 0: HUGnet2 Current
    							Input 1: HUGnet2 Temp
    							Input 2: HUGnet2 Voltage Low
    							Input 3: HUGnet2 Voltage High
    							Input 4: HUGnet1 Voltage High
    							Input 5: HUGnet1 Voltage Low
    							Input 6: HUGnet1 Temp
    							Input 7: HUGnet1 Current
    							
    							Output 0: HUGnet1 Voltage
    							Output 1: HUGnet1 Current
    							Output 2: HUGnet1 Temp
    							Output 3: HUGnet2 Voltage
    							Output 4: HUGnet2 Current
    							Output 5: HUGnet2 Temp
    						*/		
    						$vLow = $this->V->getDividerVoltage($ret['raw'][5], 180, 27, 1);
    						$vHigh = $this->V->getDividerVoltage($ret['raw'][4], 180, 27, 1);
    						$ret["Data0"] = $vHigh - $vLow;
    						$gain = 1+(180/20); // Non inverting amplifier Rf = 180, Rs = 20
    						$ret["Data1"] = $this->I->getCurrent($ret['raw'][7], 0.5, $gain, 1);
    						$ret["Data2"] = $this->R->BCTherm2381_640_66103($ret['raw'][6], 1, 10);
//    						$ohms = $this->R->getResistance($ret['raw'][6], 1, 100);
//    						$ret["Data2"] = $this->R->getReading($ohms, 0);
    
    
    						$vLow = $this->V->getDividerVoltage($ret['raw'][2], 180, 27, 1);
    						$vHigh = $this->V->getDividerVoltage($ret['raw'][3], 180, 27, 1);
    						$ret["Data3"] = $vHigh - $vLow;
    						$gain = 1+(180/20); // Non inverting amplifier Rf = 180, Rs = 20
    						$ret["Data4"] = $this->I->getCurrent($ret['raw'][0], 0.5, $gain, 1);
    						$ret["Data5"] = $this->R->BCTherm2381_640_66103($ret['raw'][1], 1, 10);


                            $ret["unitType"] = array("Voltage", "Current", "Temperature", "Voltage", "Current", "Temperature");
                            $ret["Units"] = array("V", "A", "&#176; C", "V", "A", "&#176; C");
                            
//    						$ohms = $this->R->getResistance($ret['raw'][1], 1, 100);
//    						$ret["Data5"] = $this->R->getReading($ohms, 0);
    						break;

    				}
    				$ret = $this->CheckRecord($Info, $ret);
    				$return[] = $ret;
    			}
    		}	
    		
    		return($return);
    	}
    
    	/**
    		@private
    		@brief Programs a page of flash
    		@param $Info Array Infomation about the device to use			
    		@return Array of MCU information on success, FALSE on failure 
    		@todo Move to the 00392100 driver
    	*/
    	function GetMCUInfo($Info) {
//    		$this->packet->Connect($Info);
//    		$pkt["To"] = $Info["DeviceID"];
//    		$pkt["Command"] = "5C";

//    		$retpkt = $this->packet->SendPacket($Info, array($pkt));
            $retpkt = $this->ReadConfig($Info);
    		$config = $this->driver->InterpConfig($retpkt);

    		$mcu = FALSE;
    		if (is_array($config['mcu'])) {
    			$mcu = $config['mcu'];
    		}
    //		$config = $this->getInfo($config);
    		return $mcu;
    	}
    
    	/**
    		@private
    		@brief Programs a page of flash
    		@param $Info Array Infomation about the device to use			
    		@param $Addr Integer The start address of this block
    		@param $Val String The data to program into E2 as a hex string
    		@return TRUE on success, FALSE on failure 
    		@note Due to the nature of flash, $Val must contain the data for
    			a whole page of flash.
    		@todo Move to the 00392100 driver
    	*/
    	function ProgramFlashPage($Info, $Addr, $Val) {
    //		$this->packet->socket->CheckConnectAll();
    		$this->packet->Connect($Info);
    		$pkt["To"] = $Info["DeviceID"];
    		$pkt["Command"] = "1C";
    		$pkt["Data"] = str_pad(dechex(($Addr>>8) & 0xFF), 2, "0", STR_PAD_LEFT);
    		$pkt["Data"] .= str_pad(dechex($Addr & 0xFF), 2, "0", STR_PAD_LEFT);
    
    		$pkt["Data"] .= $Val;
    		
    		$retpkt = $this->packet->SendPacket($Info, array($pkt));
    		$retpkt = $retpkt[0];
    		if (strtoupper(trim($retpkt["RawData"])) == strtoupper(trim($Val))) {
    			$return = TRUE;
    		} else {
    			$return = FALSE;
    		}
    		return($return);
    	}
    
    	/**
    		@private
    		@brief Programs a block of E2
    		@param $Info Array Infomation about the device to use			
    		@param $Addr Integer The start address of this block
    		@param $Val String The data to program into E2 as a hex string
    		@return TRUE on success, FALSE on failure 
    		@todo Move to the 00392100 driver
    	*/
    	function ProgramE2Page($Info, $Addr, $Val) {
    //		$this->packet->socket->CheckConnectAll();
    		$this->packet->Connect($Info);
    		$pkt["To"] = $Info["DeviceID"];
    		$pkt["Command"] = "1A";
    
    		// Protect the first 10 bytes of E2
    		if ($Addr == 0) {
    			$Addr = 0xA;
    			$Val = substr($Val, 20);
    		}
    
    		$pkt["Data"] = str_pad(dechex(($Addr>>8) & 0xFF), 2, "0", STR_PAD_LEFT);
    		$pkt["Data"] .= str_pad(dechex($Addr & 0xFF), 2, "0", STR_PAD_LEFT);
    
    		$pkt["Data"] .= $Val;
    		
    		$retpkt = $this->packet->SendPacket($Info, array($pkt));
    		$retpkt = $retpkt[0];
    		if (strtoupper(trim($retpkt["RawData"])) == strtoupper(trim($Val))) {
    			$return = TRUE;
    		} else {
    			$return = FALSE;
    		}
    		return($return);
    	}
    
    	/**
    		@private
    		@brief Gets the CRC of the data
    		@param $Info Array Infomation about the device to use			
    		@return The CRC on success, FALSE on failure 
    		@todo Move to the 00392100 driver
    	*/
    	function getApplicationCRC($Info) {
    		$this->packet->Connect($Info);
    		$pkt["To"] = $Info["DeviceID"];
    		$pkt["Command"] = "06";
    
    		$retpkt = $this->packet->SendPacket($Info, array($pkt));
    		$retpkt = $retpkt[0];
    		if (is_array($retpkt)) {
    			$return = $retpkt["RawData"];
    		} else {
    			$return = FALSE;
    		}
    		return($return);
    	}
    
    	/**
    		@private
    		@brief Gets the CRC of the data
    		@param $Info Array Infomation about the device to use			
    		@return The CRC on success, FALSE on failure 
    		@todo Move to the 00392100 driver
    	*/
    	function setApplicationCRC($Info) {
    		$this->packet->Connect($Info);
    		$pkt["To"] = $Info["DeviceID"];
    		$pkt["Command"] = "07";
    
    		$retpkt = $this->packet->SendPacket($Info, array($pkt));
    		$retpkt = $retpkt[0];
    		if (is_array($retpkt)) {
    			$return = $retpkt["RawData"];
    		} else {
    			$return = FALSE;
    		}
    		return($return);
    	}
    
    
    	/**
    		@brief Runs the application
    		@param $Info Array Infomation about the device to use			
    		@return TRUE on success, FALSE on failure
    		@todo Move to the 00392100 driver
    	*/
    	function RunApplication($Info) {
    		$this->packet->Connect($Info);
    		$pkt["To"] = $Info["DeviceID"];
    		$pkt["Command"] = "08";
    
    		$retpkt = $this->packet->SendPacket($Info, array($pkt), FALSE);
    		$retpkt = $retpkt[0];
    		return($retpkt);
    	}
    
    	/**
    		@brief Runs the bootloader
    		@param $Info Array Infomation about the device to use			
    		@return Reply Packet on success, FALSE on failure
    		@todo Move to the 00392100 driver
    	*/
    	function RunBootloader($Info) {
    		$RetT = $this->packet->ReplyTimeout;
    		if ($RetT < 10) $this->packet->ReplyTimeout = 10;
    		$this->packet->Connect($Info);

    		$pkt["To"] = $Info["DeviceID"];
    		$pkt["Command"] = "09";
    		$retpkt = $this->packet->SendPacket($Info, array($pkt));
    		$retpkt = $retpkt[0];
    		$this->ReplyTimeout = $RetT;
    		
    		return($retpkt);
    	}
    
        function checkProgram($Info, $pkts, $update=FALSE) {
    
            $dInfo = $this->driver->InterpConfig($pkts);
    
            $return = FALSE;
            if ($dInfo['bootLoader'] || $update){
                print "\r\nGetting the latest firmware... ";
                $res = $this->firmware->GetLatestFirmware('0039-20-01-C');
                print " found v".$res['FirmwareVersion']."\r\n";

                if (!$dInfo['bootLoader']) {
                    if ($this->CompareFWVersion($dInfo['FWVersion'], $res['FirmwareVersion']) < 0) {
                        print "Board is running ".$dInfo['FWVersion']."\r\n";
                        print "Crashing the running program\r\n";
                        $this->RunBootLoader($dInfo);
                    } else {
                        $update=FALSE;                        
                    }
                }
                if ($dInfo['bootLoader'] || $update) {
                    $return = $this->loadProgram($dInfo, $dInfo, $res[0]['FirmwareKey']);
                } else {
                    $return = TRUE;
                }
            } else {
                $return = TRUE;
            }
            return $return;
        }

   
    	function loadProgram($Info, $gw=NULL, $FirmwareKey) {
    //		$this->firmware->reset();

    		$fw = $this->firmware->get($FirmwareKey);
    		if (!is_array($fw)) {
                $fw = $this->firmware->GetLatestFirmware('0039-20-01-C');
            }
    //		print "<pre>";
            
            print "\r\nProgramming the device\r\n";

    		if (isset($Info['mcu'])) {
    			$mcu = $Info['mcu'];
    		} else {
    			print "Getting MCU Info... ";
    			flush();
    			$mcu = $this->GetMCUInfo($Info);
    			print " Done\r\n";
    			flush();
    		}



    		if (is_array($mcu)) {
    			$prog = $this->firmware->InterpSREC($fw["FirmwareCode"], $mcu["FLASH"], $mcu["FLASHPAGE"]);
    			print "\r\n";
    			print "V = Verified\r\n";
    			print "F = Failed\r\n";
    			print "\r\n";
    			print "Flash Memory: (0x".dechex(count($prog))." pages)\r\n";
    			print "Page   0123456789ABCDEF\r\n";
    			flush();
    			$oldPTimeout = $this->packet->ReplyTimeout;
    //			$this->packet->ReplyTimeout = 10;
    			foreach($prog as $pnum => $page) {
    				if (($pnum % 16) == 0) {
    					print '0x'.str_pad(dechex($pnum), 4, "0", STR_PAD_LEFT).' ';	
    				}
    				flush();
    				$addr = ($pnum * $mcu["FLASHPAGE"]);
    				$tries = 0;
    				do
    				{
    					$return = $this->ProgramFlashPage($Info, $addr, $page);
    				} while (($return === FALSE) && ($tries++ < 5));
    				if ($return) {
    					print "V";
    				} else {
    					print "F";
    				}
    				if ((($pnum+1) % 16) == 0) print "\r\n";		
    				flush();
    				if ($return === FALSE) break;
    			}
    			print "\r\n\r\n";
    			if ($return !== FALSE) {
    				$e2 = $this->firmware->InterpSREC($fw["FirmwareData"], $mcu["E2"], 128);
    				print "E2 Memory: (0x".dechex(count($e2))." pages)\r\n";
    				print "Page   0123456789ABCDEF\r\n";
    				flush();
    				foreach($e2 as $pnum => $page) {
    					if (($pnum % 16) == 0) {
    						print '0x'.str_pad(dechex($pnum), 4, "0", STR_PAD_LEFT).' ';	
    					}
    					flush();
    					$addr = ($pnum * 128);
    					$tries = 0;
    					do
    					{
    						$return = $this->ProgramE2Page($Info, $addr, $page);
    					} while (($return === FALSE) && ($tries++ < 5));
    					
    					if ($return) {
    						print "V";
    					} else {
    						print "F";
    						$return = FALSE;
    					}
    					if ((($pnum+1) % 16) == 0) print "\r\n";		
    					if ($return === FALSE) break;
    					flush();
    				}
    			}
    			$this->packet->ReplyTimeout = $oldPTimeout;
    			print "\r\n\r\n";
    			if ($return !== FALSE) {
    				print "Getting CRC: ";
    				flush();
    				$AppCRC = $this->setApplicationCRC($Info);
    				if ($AppCRC !== FALSE) {
        				$AppCRC = $this->getApplicationCRC($Info);
    					print $AppCRC."\r\n";
    					print "Running Program\r\n";	
    					flush();
    					$this->runApplication($Info);
    				} else {
    					print " Failed\r\n";
    				}
    			} else {
    				print " Failed<br>\r\n ";
    			} 
    		} else {
    			print " Failed<br>\r\n";		
    		}
    //		print "</pre>";
    		flush();
    //		$this->packet->UnlockAll();
    
    
    //		$this->packet->socket->Close();
    		return $return;
    	}
    	/**
    		@brief Runs the application
    		@param $Info Array Infomation about the device to use			
    		@return TRUE on success, FALSE on failure
    		@todo Move to the 00392100 driver
    	*/
    	function getPower($Info) {
    		$this->packet->Connect($Info);
    		$pkt["To"] = $Info["DeviceID"];
    		$pkt["Command"] = "60";
    
    		$retpkt = $this->packet->SendPacket($Info, $pkt);
    
    		return($retpkt);
    	}
    
    	/**
    		@brief Runs the application
    		@param $Info Array Infomation about the device to use			
    		@return TRUE on success, FALSE on failure
    		@todo Move to the 00392100 driver
    	*/
    	function setPower($Info, $hugnet0=1, $hugnet1=1) {
/*
    		$hugnet0 = ($hugnet0 == 0) ? '00' : '01';
    		$hugnet1 = ($hugnet1 == 0) ? '00' : '01';
    
    		$this->packet->Connect($Info);
    		$pkt["To"] = $Info["DeviceID"];
    		$pkt["Command"] = "60";
    		$pkt["Data"] = $hugnet0.$hugnet1;
    
    		$retpkt = $this->packet->SendPacket($Info, $pkt);
    
    		return($retpkt);
*/
    	}
    
    	
    	/**
    		@brief Constructor
    		@param $db String The database to use
    		@param $servers Array The servers to use.
    		@param $options the database options to use.
    	*/
    	function e00392100 (&$driver) {
    		
    		$this->driver =& $driver;
    		$this->packet =& $driver->packet;
    		$this->firmware = new firmware($driver->db);
    		$this->R = new resistiveSensor(65536, 65536, 1<<6, 1023);
    		$this->V = new voltageSensor(65536, 65536, 1<<6, 1023, 5);
    		$this->I = new currentSensor(65536, 65536, 1<<6, 1023, 5);
    	}
    
    
    
    }
}	
?>
