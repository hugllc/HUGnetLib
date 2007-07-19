<?php
/**
	$Id$

	@file device.inc.php
	@brief Class for manipulating the device database
	
	

	
*/

/**
	@brief Class for talking with HUGNet endpoints
*/

class device {
    var $table = "devices";
    var $primaryCol = "DeviceKey";
    
    function device(&$driver) {
        $this->db = &$driver->db;
        $this->_driver = &$driver;
    }
    
    function selectDevice($name=NULL, $selected=NULL, $GatewayKey=NULL) {
    
        $query = "SELECT DeviceKey, DeviceID FROM devices WHERE";
        if (is_null($GatewayKey)) {
            $query .= " GatewayKey<>'0'";            
        } else {
            $query .= " GatewayKey='".$GatewayKey."'";            
        }
        $rs = $this->db->Execute($query);
        if (is_null($name)) {
            $res = $rs->GetAssoc();
            return $res;
        } else {
            return $rs->GetMenu($name, $selected);
        }
    }    
    
    function getDevice($id, $type="KEY") {
        if (empty($id)) return array();
	    if (isset($this->_devCache[$id])) return($this->_devCache[$id]);

        switch (trim(strtoupper($type))) {
        case "ID":
            $query = "select * from devices where DeviceID='".$id."'";            
            break;
        case "NAME":
            $query = "select * from devices where DeviceName='".$id."'";            
            break;
        case "KEY":
        default:
            $query = "select * from devices where DeviceKey='".$id."'";
            break;
        }
        $devInfo = $this->db->getArray($query);
        if (is_array($devInfo)) {
           	$devInfo = $devInfo[0];
            $devInfo = $this->_driver->DriverInfo($devInfo);
            $query = "select * from ".$this->_driver->getLocationTable($devInfo)." where DeviceKey='".$id."'";

            $loc = $this->db->getArray($query);
//            $devInfo["Location"] = ;
            if (is_array($loc[0])) {
//                $devInfo['Location'] = $loc[0];
                foreach($loc[0] as $key => $tLoc) {
                    $key = trim($key);
                    if (strtolower(substr($key, 0, 3)) == "loc") {
                        if (!empty($tLoc)) {
                            $devInfo["Location"][$key] = $tLoc;
                            $nKey = (int) substr($key, 3);
                            $devInfo["Location"][$nKey] = $tLoc;
                        }
                    }
                }
            }
            $query = "select * from calibration where DeviceKey='".$id."' ORDER BY StartDate DESC LIMIT 0,1";

            $cal = $this->db->getArray($query);
            $devInfo["Calibration"] = array();
            if (is_array($cal[0])) {
                $devInfo["Calibration"] = $this->getCalibration($devInfo, $cal[0]['RawCalibration']);
            }

            $query = "select * from gateways where GatewayKey='".$devInfo['GatewayKey']."'";

            $gw = $this->db->getArray($query);
            if (is_array($gw)) {
                $devInfo['Gateway'] = $gw[0];
            }
            $this->_devCache[$id] = $devInfo;
        }
	    return($devInfo);
	}

	/**
		@brief Runs a function using the correct driver for the endpoint
		@param $Packet Array Array of information about the device with the data from the incoming packet
		@param $force Boolean Force the update even if the serial number and hardware part number don't match
	*/
	function UpdateDevice($Packet, $force=FALSE){


        $DeviceID = NULL;
        $GatewayKey = NULL;
		foreach($Packet as $key => $val) {
			if (isset($val["PacketFrom"])) {
				$Packet[$key]['DeviceID'] = $val["PacketFrom"];
			} else if (isset($val["from"])) {		
				$Packet[$key]['DeviceID'] = $val["from"];
			} else if (isset($val["From"])) {		
				$Packet[$key]['DeviceID'] = $val["From"];
			} else if (isset($val["DeviceID"])) {
				$Packet[$key]['DeviceID'] = $val["DeviceID"];
   			}
            if (is_null($DeviceID)) $DeviceID = $Packet[$key]['DeviceID'];
            if (is_null($GatewayKey) && !empty($val['GatewayKey'])) $GatewayKey = $val['GatewayKey'];
        }

/*
        $query = "SELECT * FROM devices WHERE DeviceID='".$DeviceID."'";
        $res = $this->db->getArray($query);
        if (is_array($res[0])) $res = $res[0];
*/
        $res = $this->getDevice($DeviceID, 'ID');
        if (!is_array($res)) $res = array();
		foreach($Packet as $key => $val) {
            if (is_array($val)) $Packet[$key] = array_merge($res, $val);
        }

        // InterpConfig takes an array of packets and returns
        // a single array of configuration data.
		$ep = $this->_driver->InterpConfig($Packet);
		$return = TRUE;

		if (is_array($ep)) {
			if (!empty($ep['SerialNum'])) {
    			if (($force === FALSE) && !empty($ep['DeviceKey'])) {
    				if (($res["SerialNum"] != $ep["SerialNum"]) && isset($ep['SerialNum'])) {
    					if (($res["HWPartNum"] != $ep["HWPartNum"]) && isset($ep['HWPartNum'])) {
    					// This is not for the correct endpoint
    						return(FALSE);
    					}
    				}
    			}

            } else {
                unset($ep['SerialNum']);
            }
            
            
//   			    $ep = array_merge($res, $ep);
			if (empty($ep['DeviceKey']) 
			    || !isset($ep['LastConfig']) 
			    || (strtotime($res["LastConfig"]) < strtotime($ep["LastConfig"]))
			) {
/*
                if (isset($ep['NumSensors'])) {
    				if (!isset($ep['ActiveSensors']) || ($ep['ActiveSensors'] > $ep['NumSensors'])) {
	    				$ep['ActiveSensors'] = $ep['NumSensors'];
	    			}
                }
*/
                // This makes sure that the gateway key gets set, as it might have changed.
                if (!is_null($GatewayKey)) $ep['GatewayKey'] = $GatewayKey;
				if (!empty($ep['DeviceKey'])) {
					$return = $this->db->AutoExecute('devices', $ep, 'UPDATE', 'DeviceKey='.$res['DeviceKey']);
				} else {
				    if (!empty($ep["HWPartNum"])) {
    				    if (!empty($ep["FWPartNum"])) {
        				    if (!empty($ep["SerialNum"])) {
                                unset($ep['DeviceKey']);
            					$return = $this->db->AutoExecute('devices', $ep, 'INSERT');
                            }
                        }
                    }
				}
            }				    
			if (!$return) {
				print "Error (".$this->db->MetaErrorNo.") ".$this->db->MetaErrorMsg."\n";
				$this->Errno = $this->db->MetaErrorNo;
				$this->Error = $this->db->MetaErrorMsg;
			}
    		if (isset($ep['Driver']) && ($ep['Driver'] != "eDEFAULT")) {
//    		    if (!isset($ep['Driver'])) $ep['Driver'] = $res['Driver'];
    			$return = $this->_driver->RunFunction($ep, 'updateConfig');
            }
		}
		return($return);					
	}

}


class deviceCache {
	var $table = "devices";				//!< The database table to use
	var $primaryCol = "DeviceKey";	 //!< This is the Field name for the key of the record

    var $fields = array(
        	"DeviceKey" => "int(11)",
            "DeviceID" => "varchar(6)",
            "DeviceName" => "varchar(128)",
            "SerialNum" => "bigint(20)",
            "HWPartNum" => "varchar(12)",
            "FWPartNum" => "varchar(12)",
            "FWVersion" => "varchar(8)",
            "RawSetup" => "varchar(128)",
            "Active" => "varchar(4)",
            "GatewayKey" => "int(11)",
            "ControllerKey" => "int(11)",
            "ControllerIndex" => "tinyint(4)",
            "DeviceLocation" => "varchar(64)",
            "DeviceJob" => "varchar(64)",
            "Driver" => "varchar(32)",
            "PollInterval" => "mediumint(9)",
            "ActiveSensors" => "smallint(6)",
            "DeviceGroup" => "varchar(6)",
            "BoredomThreshold" => "tinyint(4)",
            "LastConfig" => "datetime",
            "LastPoll" => "datetime",
            "LastHistory" => "datetime",
            "LastAnalysis" => "datetime",
            "MinAverage" => "varcar(16)",
            "CurrentGatewayKey" => "int(11)",
        );
	/**
		@brief Constructor
		@param $servers The servers to use.  Set to "" to use the default servers	
		@param $db String The database to use
		@param $options the database options to use.
	*/

    function __construct($file = NULL, $mode = 0666, $error = NULL) {
        if ($error == NULL) $error =& $this->lastError;
        
        if (!is_null($file)) {
            $this->file = $file;
        } else {
            $this->file = get_temp_dir()."/".HUGNET_LOCAL_DATABASE;
        }
        if (!is_string($file)) $file = "/tmp/HUGnetLocal";
//        $this->_sqlite = new SQLiteDatabase($file, $mode, $error);
        $this->_sqlite = new PDO("sqlite:".$file.".sq3");
        $this->createTable();
        $ret = $this->_sqlite->query("PRAGMA table_info(".$this->table.")");
        if (is_object($ret)) $columns = $ret->fetchAll(PDO::FETCH_ASSOC);
        if (is_array($columns)) {
            foreach($columns as $col) {
                $this->fields[$col['name']] = $col['type'];
            }
        }
    }
    
    function createTable() {
        $query = "CREATE TABLE `".$this->table."` (
                      `DeviceKey` int(11) NOT NULL,
                      `DeviceID` varchar(6) NOT NULL default '',
                      `DeviceName` varchar(128) NOT NULL default '',
                      `SerialNum` bigint(20) NOT NULL default '0',
                      `HWPartNum` varchar(12) NOT NULL default '',
                      `FWPartNum` varchar(12) NOT NULL default '',
                      `FWVersion` varchar(8) NOT NULL default '',
                      `RawSetup` varchar(128) NOT NULL default '',
                      `Active` varchar(4) NOT NULL default 'YES',
                      `GatewayKey` int(11) NOT NULL default '0',
                      `ControllerKey` int(11) NOT NULL default '0',
                      `ControllerIndex` tinyint(4) NOT NULL default '0',
                      `DeviceLocation` varchar(64) NOT NULL default '',
                      `DeviceJob` varchar(64) NOT NULL default '',
                      `Driver` varchar(32) NOT NULL default '',
                      `PollInterval` mediumint(9) NOT NULL default '0',
                      `ActiveSensors` smallint(6) NOT NULL default '0',
                      `DeviceGroup` varchar(6) NOT NULL default '',
                      `BoredomThreshold` tinyint(4) NOT NULL default '0',
                      `LastConfig` datetime NOT NULL default '0000-00-00 00:00:00',
                      `LastPoll` datetime NOT NULL default '0000-00-00 00:00:00',
                      `LastHistory` datetime NOT NULL default '0000-00-00 00:00:00',
                      `LastAnalysis` datetime NOT NULL default '0000-00-00 00:00:00',
                      `MinAverage` varcar(16) NOT NULL default '15MIN',
                      `CurrentGatewayKey` int(11) NOT NULL default '0',
                      PRIMARY KEY  (`DeviceKey`)
                    );
                    ";
        $ret = $this->_sqlite->query($query);
        $ret = $this->_sqlite->query('CREATE UNIQUE INDEX `SerialNum` ON `'.$this->table.'` (`SerialNum`)');
        $ret = $this->_sqlite->query('CREATE UNIQUE INDEX `DeviceID` ON `'.$this->table.'` (`DeviceID`,`GatewayKey`)');
        return $ret;
    }

    function addArray($InfoArray) {
        if (is_array($InfoArray)) {
            foreach($InfoArray as $info) {
                $this->add($info);
            }
        }
    }
    
    function add($info) {    
        if (isset($info['DeviceID']) 
                && isset($info['GatewayKey']) 
                && isset($info['SerialNum']) 
                )
        {
            $div = "";
            $fields = "";
            $values = "";
            foreach($this->fields as $key => $val) {
                if (isset($info[$key])) {
                    $fields .= $div.$key;
                    $values .= $div.$this->_sqlite->quote($info[$key]);
                    $div = ", ";
                }
            }


            $query = " REPLACE INTO '".$this->table."' (".$fields.") VALUES (".$values.")";
            $ret = $this->_sqlite->query($query);
            return $ret;

        } else {
            return FALSE;
        }
    }

    function update($info) {    
        if (isset($info['DeviceKey'])) {
            $div = "";
            $fields = "";
            $values = "";
            foreach($this->fields as $key => $val) {
                if (isset($info[$key])) {
                    $fields .= $div.$key;
                    $values .= $div.$this->_sqlite->quote($info[$key]);
                    $div = ", ";
                }
            }


            $query = " UPDATE '".$this->table."' SET (".$fields.") VALUES (".$values.") WHERE ".$this->id."=".$info['DeviceKey'];
            return $this->_sqlite->query($query);
        } else {
            return FALSE;
        }
    }


    function getAll() {
        $query = " SELECT * FROM '".$this->table."'; ";
        $ret = $this->_sqlite->query($query);
        if (is_object($ret)) $ret = $ret->fetchAll(PDO::FETCH_ASSOC);
        return $ret;
    }

    function query($query) {
        $ret = $this->_sqlite->query($query);
        if (is_object($ret)) $ret = $ret->fetchAll(PDO::FETCH_ASSOC);
        return $ret;
    }
    function remove($info) {
        if (is_array($info))
        {
            $div = "";
            $where = "";
            foreach($info as $key => $val) {
                $where .= $div.$key."='".$val."'";
                $div = " AND ";
            }
            if (empty($where)) return FALSE;

            $query = " DELETE FROM '".$this->table."' WHERE ".$where;
            $ret = $this->_sqlite->query($query);
            if (is_object($ret)) $ret = TRUE;
            return $ret;
        } else {
            return FALSE;
        }
    
    }
	
}


?>
