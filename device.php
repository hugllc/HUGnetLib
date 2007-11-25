<?php
/**
 *   Classes for dealing with devices
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

/**
	@brief Class for talking with HUGNet endpoints
*/

class device {
    /** The table devices are stored in */
    var $table = "devices";
    /** The key used for the table */
    var $primaryCol = "DeviceKey";

    /** The table the analysis output is stored in */
    var $analysis_table = "analysis";
    /** How many times the poll interval has to pass before we show an error on it     */
    var $PollWarningIntervals = 2;        

    /**
     * This function sets up the driver object, and the database object.  The
     * database object is taken from the driver object.
     *
     * @param object $driver  This should be an object of class driver
    */
    function __construct(&$driver) {
        $this->db = &$driver->db;
        $this->_driver = &$driver;
    }

    public function update($key, $stuff) {
        if (!is_array($stuff)) return FALSE;
        $sep = "";
        $set = "";
        foreach($stuff as $field => $value) {
            $set .= $sep.$field." = ".$this->db->qstr($value)." ";
            $sep = ", ";
        }
        if (empty($set)) return FALSE;
        $query = " UPDATE ".$this->table.
                 " SET " . $set .
                 " WHERE " .
                 " DeviceKey=".$key;
        if ($this->db->Execute($query)) {
            return TRUE;
        } else {
            return FALSE;
        }

    } 

    /**
     * This returns an array setup for a HTML select list using the adodb
     * function 'GetMenu'
     *
     * @param string $name The name of the select list
     * @param mixed $selected The entry that is currently selected
     * @param int $GatewayKey The key to use if only one gateway is to be selected
     */    
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
    
    /**
     * Gets the database record of a devices.  It can be fed the DeviceID, DeviceName,
     * or DeviceKey, depending on the type paramater.  This also gets gateway information
     * as well as calibration information on the device.
     *
     * @param mixed $id This is either the DeviceID, DeviceName or DeviceKey
     * @param int $type The type of the 'id' parameter.  It is "ID" for DeviceID,
     *    "NAME" for DeviceName or "KEY" for DeviceKey.  "KEY" is the default.
     */
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
            $devInfo["params"] = $this->decodeParams($devInfo["params"]);

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
                    unset($ep['params']);
					$return = $this->db->AutoExecute('devices', $ep, 'UPDATE', 'DeviceKey='.$res['DeviceKey']);
				} else {
				    if (!empty($ep["HWPartNum"])) {
    				    if (!empty($ep["FWPartNum"])) {
        				    if (!empty($ep["SerialNum"])) {
                                unset($ep['DeviceKey']);
                                unset($ep['params']);
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

    /**
     * This sets the device paramters in the database.  The device parameters
     * are stored as string (text) in the database.  This routine takes in the
     * parameter array, encodes it, then stores it in the database.
     *
     * @uses device::encodeParams
     *
     * @param int $DeviceKey The key for the device to be stored
     * @param array $params The parameter array to be stored.
     */
	function setParams($DeviceKey, $params) {
        if (is_array($params)) $params = device::encodeParams($params);
	    $params = $this->db->qstr($this->encodeParams($params));
        $return = $this->db->Execute("UPDATE ".$this->table." SET params = ".$params." WHERE DeviceKey=".$DeviceKey);
		if ($return === FALSE) {
			$this->Errno = $this->db->MetaError();
			$this->Error = $this->db->MetaErrorMsg($this->Errno);
		}
        return $return;
    }

    /**
     * Checks to see if this is a controller.
     *
     * @param array $info This is a device information array
     */
    function isController(&$info) {
        return method_exists($this->_driver->drivers[$info['Driver']], "checkProgram");
    }

    /**
     *
     */
    function encodeParams(&$params) {
        if (is_array($params)) {
            $params = serialize($params);
            $params = base64_encode($params);
        } else if (!is_string($params)) {
            $params = "";
        }
        return $params;
    }

    /**
     *
     */
    function decodeParams(&$params) {
        if (is_string($params)) {
            $params = base64_decode($params);
            $params = unserialize($params);
        } else if (!is_array($params)) {
            $params = array();
        }
        return $params;    
    }

    /**
     *   Queries health information from the database.
     *   
     ** @todo This should be moved to the device class
     *   
     ** @param string $where Extra where clause for the SQL
     ** @param int $days The number of days back to go
     ** @param string|int $start The start date of the health report
     ** @return array The array of health information
      */
    function health($where, $days = 7, $start=NULL) {

        if ($start === NULL) {
            $start = time();
        } else if (is_string($start)) {
            $start = strtotime($start);
        }
        $end = $start - (86400 * $days);
        $cquery = "SELECT COUNT(DeviceKey) as count FROM ".$this->table." ";
        $cquery .= " WHERE PollInterval > 0 ";
        if (!empty($where)) $cquery .= " AND ".$where;
        $res = $this->db->getArray($cquery);
        $count = $res[0]['count'];
        if (empty($count)) $count = 1;
        
        $query = " SELECT " .
                 "  ROUND(AVG(AverageReplyTime), 2) as ReplyTime " .
                 ", ROUND(STD(AverageReplyTime), 2) as ReplyTimeSTD " .
                 ", ROUND(MIN(AverageReplyTime), 2) as ReplyTimeMIN " .
                 ", ROUND(MAX(AverageReplyTime), 2) as ReplyTimeMAX " .
                 ", ROUND(AVG(AveragePollTime), 2) as PollInterval " . 
                 ", ROUND(STD(AveragePollTime), 2) as PollIntervalSTD " . 
                 ", ROUND(MIN(AveragePollTime), 2) as PollIntervalMIN " . 
                 ", ROUND(MAX(AveragePollTime), 2) as PollIntervalMAX " . 
                 ", ROUND(AVG(PollInterval), 2) as PollIntervalSET " . 
                 ", ROUND(AVG(PollInterval/AveragePollTime), 2) as PollDensity " . 
                 ", ROUND(STD(PollInterval/AveragePollTime), 2) as PollDensitySTD " . 
                 ", ROUND(MIN(PollInterval/AveragePollTime), 2) as PollDensityMIN " . 
                 ", ROUND(MAX(PollInterval/AveragePollTime), 2) as PollDensityMAX " . 
                 ", '1.0' as PollDensitySET " . 
                 ", SUM(Powerups) as Powerups " .
                 ", SUM(Reconfigs) as Reconfigs " .
                 ", ROUND(SUM(Polls) / ".$days.") as DailyPolls ".
                 ", ROUND((1440 / AVG(PollInterval)) * ".$count.") as DailyPollsSET ".
                 " ";
        $query .= " FROM " . $this->analysis_table;

        $query .= " LEFT JOIN " . $this->table . " ON " . 
                 $this->table . ".DeviceKey=" . $this->analysis_table . ".DeviceKey ";

        $query .= " WHERE " .
                  $this->analysis_table . ".Date <= ".$this->db->qstr(date("Y-m-d H:i:s", $start)).
                  " AND " .
                  $this->analysis_table . ".Date >= ".$this->db->qstr(date("Y-m-d H:i:s", $end));
    
        if (!empty($where)) $query .= " AND ".$where;

        $res = $this->db->getArray($query);
        if (isset($res[0])) $res = $res[0];
        return $res;
    }



    /**
     * Sends out an all call so all boards respond.
     * @param $Info Array Infomation about the device to get stylesheet information for
     * @return The return should be put inside of style="" css tags in your HTML
    
        Returns a style based on the condition of the endpoint.  Useful for displaying
        a list of endpoints and quickly seeing which ones have problems.
     */
    function Diagnose($Info) {
        $problem = array();
        if ($Info["PollInterval"] > 0) {
            $timelag = time() - strtotime($Info["LastPoll"]);
            $pollhistory = (strtotime($Info["LastPoll"]) - strtotime($Info["LastHistory"]));
            if ($pollhistory < 0) $pollhistory = (-1)*$pollhistory;
            
            if (($timelag > ($this->PollWarningIntervals*60*$Info["PollInterval"]))){
                $problem[] = "Last Poll ".$this->get_ydhms($timelag)." ago\n";
            }
            if ($pollhistory > 1800) {
                $problem[] = "History ".$this->get_ydhms($pollhistory)." old\n";
            }
            if (($Info["GatewayKey"] != $Info["CurrentGatewayKey"]) && ($Info["CurrentGatewayKey"] != 0)) {
//                $problem[] = "Polling on backup gateway\n";
            }
            if ($Info['ActiveSensors'] == 0) {
                $problem[] = "No Active Sensors\n";
            }
        }
        return($problem);        
    }
    /**
     * Puts seconds into a human readable Hours minutes seconds
     *
     * @param float $seconds The number of seconds
     * @param int $digits the number of places after the decimal point to have on the seconds
     * @return string
     */
    function get_ydhms ($seconds, $digits=0) {
        $years = (int)($seconds/60/60/24/365.25);
        $seconds -= $years*60*60*24*365.25;
        $days = (int)($seconds/60/60/24);
        $seconds -= $days*60*60*24;
        $hours = (int)($seconds/60/60);
        $seconds -= $hours*60*60;
        $minutes = (int)($seconds/60);
        $seconds -= $minutes*60;
        $seconds = number_format($seconds, $digits);

        $return = "";
        if ($years > 0) $return .= $years."Y ";
        if ($days > 0) $return .= $days."d ";
        if ($hours > 0) $return .= $hours."h ";
        if ($minutes > 0) $return .= $minutes."m ";
        $return .= $seconds."s";
        return($return);
    }

}


/**
 *
 */
class deviceCache {
	var $table = "devices";				//!< The database table to use
	var $primaryCol = "DeviceKey";	 //!< This is the Field name for the key of the record

    /**
     *
     */
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
            "params" => "text",
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
            $this->file = HUGNET_LOCAL_DATABASE;
        }
        if (!is_string($this->file)) $this->file = "/tmp/HUGnetLocal";
//        $this->_sqlite = new SQLiteDatabase($file, $mode, $error);
        $this->_sqlite = new PDO("sqlite:".$this->file);
        $this->createTable();
        $ret = $this->_sqlite->query("PRAGMA table_info(".$this->table.")");
        if (is_object($ret)) $columns = $ret->fetchAll(PDO::FETCH_ASSOC);
        if (is_array($columns)) {
            foreach($columns as $col) {
                $this->fields[$col['name']] = $col['type'];
            }
        }
    }
    
    /**
     *
     */
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
                      `params` text NOT NULL,
                      PRIMARY KEY  (`DeviceKey`)
                    );
                    ";
        $ret = $this->_sqlite->query($query);
        $ret = $this->_sqlite->query('CREATE UNIQUE INDEX `SerialNum` ON `'.$this->table.'` (`SerialNum`)');
        $ret = $this->_sqlite->query('CREATE UNIQUE INDEX `DeviceID` ON `'.$this->table.'` (`DeviceID`,`GatewayKey`)');
        return $ret;
    }

    /**
     *
     */
    function addArray($InfoArray) {
        if (is_array($InfoArray)) {
            foreach($InfoArray as $info) {
                $this->add($info);
            }
        }
    }
    
    /**
     *
     */
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
                    if ($key == "params") {
                        $info[$key] = device::encodeParams($info[$key]);
                        $values .= $div.$this->_sqlite->quote($info[$key]);
                    } else {
                        $values .= $div.$this->_sqlite->quote($info[$key]);
                    }
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

    /**
     *
     */
    function update($info) {    
        if (isset($info['DeviceKey'])) {
            $div = "";
            $fields = "";
            $values = "";
            foreach($this->fields as $key => $val) {
                if (isset($info[$key])) {
                    $fields .= $div.$key;
                    if ($key == "params") {
                        $info[$key] = device::encodeParams($info[$key]);
                        $values .= $div.$this->_sqlite->quote($info[$key]);
                    } else {
                        $values .= $div.$this->_sqlite->quote($info[$key]);
                    }
                    $div = ", ";
                }
            }


            $query = " UPDATE '".$this->table."' SET (".$fields.") VALUES (".$values.") WHERE ".$this->id."=".$info['DeviceKey'];
            return $this->_sqlite->query($query);
        } else {
            return FALSE;
        }
    }


    /**
     *
     */
    function getAll() {
        $query = " SELECT * FROM '".$this->table."'; ";
        $ret = $this->_sqlite->query($query);
        if (is_object($ret)) $ret = $ret->fetchAll(PDO::FETCH_ASSOC);
        foreach($ret as $key => $val) {
            $ret[$key]["params"] = device::decodeParams($ret[$key]["params"]);
        }
        return $ret;
    }

    /**
     *
     */
    function query($query) {
        $ret = $this->_sqlite->query($query);
        if (is_object($ret)) $ret = $ret->fetchAll(PDO::FETCH_ASSOC);
        return $ret;
    }

    /**
     *
     */
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
