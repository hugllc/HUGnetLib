<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** The base for all database classes */
require_once HUGNET_INCLUDE_PATH."/base/DbBase.php";
/**
 * Class for talking with HUGNet endpoints
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Device extends DbBase
{
    /** The table devices are stored in */
    var $table = "devices";
    /** The key used for the table */
    var $id = "DeviceKey";
    /** The number of columns */
    private $_columns = 26;

    /** The table the analysis output is stored in */
    var $analysis_table = "analysis";
    /** How many times the poll interval has to pass before we show an error on it      */
    var $PollWarningIntervals = 2;        

    /**
     * This function sets up the driver object, and the database object.  The
     * database object is taken from the driver object.
     *
     * @param object &$driver This should be an object of class driver
     * @param string $table   The database table to use
     * @param string $id      The 'id' column to use
     * @param bool   $verbose Whether to be verbose or not
     */
    function __construct(&$driver = null, $table=null, $id=null, $verbose=false) 
    {
        if (is_object($driver)) {
            $this->_driver = &$driver;
            parent::__construct($driver->db, $table, $id, $verbose);
        } else {
            if (is_string($driver)) $this->file = $driver;
            parent::__construct($this->file, $table, $id, $verbose);
        }
    }

    /**
     * This returns an array setup for a HTML select list using the adodb
     * function 'GetMenu'
     *
     * @param string $name       The name of the select list
     * @param mixed  $selected   The entry that is currently selected
     * @param int    $GatewayKey The key to use if only one gateway is to be selected
     *
     * @return mixed
     */    
    function selectDevice($name=null, $selected=null, $GatewayKey=null) 
    {
        $data  = array();
        $query = "SELECT DeviceKey, DeviceID FROM devices WHERE";
        if (is_null($GatewayKey)) {
            $data[] = 0;
            $query .= " GatewayKey <> ?";            
        } else {
            $data[] = $GatewayKey;
            $query .= " GatewayKey= ? ";            
        }
        return $this->query($query, $data);
    }    
    
    /**
     * Gets the database record of a devices.  It can be fed the DeviceID, DeviceName,
     * or DeviceKey, depending on the type paramater.  This also gets gateway information
     * as well as calibration information on the device.
     *
     * @param mixed $id   This is either the DeviceID, DeviceName or DeviceKey
     * @param int   $type The type of the 'id' parameter.  It is "ID" for DeviceID,
     *         "NAME" for DeviceName or "KEY" for DeviceKey.  "KEY" is the default.
     *
     * @return array
     */
    function getDevice($id, $type="KEY") 
    {
        if (empty($id)) return array();

        switch (trim(strtoupper($type))) {
        case "ID":
            $field = "DeviceID";            
            break;
        case "NAME":
            $field = "DeviceName";            
            break;
        case "KEY":
        default:
            $field = "DeviceKey";
            break;
        }
        $devInfo = $this->getWhere($field." = ? ", array($id));
        if (is_array($devInfo)) {
            $devInfo = $devInfo[0];
            $devInfo = $this->_driver->DriverInfo($devInfo);

            $query = "select * from calibration where DeviceKey='".$id."' ORDER BY StartDate DESC LIMIT 0,1";

            $cal                    = $this->query($query);
            $devInfo["Calibration"] = array();
            if (is_array($cal[0])) {
                $devInfo["Calibration"] = $this->getCalibration($devInfo, $cal[0]['RawCalibration']);
            }

            $query = "select * from gateways where GatewayKey='".$devInfo['GatewayKey']."'";

            $gw = $this->query($query);
            if (is_array($gw)) {
                $devInfo['Gateway'] = $gw[0];
            }
            $devInfo["params"] = $this->decodeParams($devInfo["params"]);

        }
        return $devInfo;
    }

    /**
     * Runs a function using the correct driver for the endpoint
     *
     * @param array $Packet Array of information about the device 
     *                    with the data from the incoming packet
     * @param bool  $force  Force the update even if the serial number 
     *                    and hardware part number don't match
     *
     * @return mixed
     */
    function updateDevice($Packet, $force=false)
    {

        $DeviceID   = null;
        $GatewayKey = null;
        foreach ($Packet as $key => $val) {
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

        $res = $this->getDevice($DeviceID, 'ID');
        if (!is_array($res)) $res = array();
        foreach ($Packet as $key => $val) {
            if (is_array($val)) $Packet[$key] = array_merge($res, $val);
        }

        // interpConfig takes an array of packets and returns
        // a single array of configuration data.
        $ep     = $this->_driver->interpConfig($Packet);
        $return = true;

        if (is_array($ep)) {
            if (!empty($ep['SerialNum'])) {
                if (($force === false) && !empty($ep['DeviceKey'])) {
                    if (($res["SerialNum"] != $ep["SerialNum"]) && isset($ep['SerialNum'])) {
                        if (($res["HWPartNum"] != $ep["HWPartNum"]) && isset($ep['HWPartNum'])) {
                            // This is not for the correct endpoint
                            return(false);
                        }
                    }
                }

            } else {
                unset($ep['SerialNum']);
            }
            
            
            if (empty($ep['DeviceKey']) 
                || !isset($ep['LastConfig']) 
                || (strtotime($res["LastConfig"]) < strtotime($ep["LastConfig"]))
            ) {

                // This makes sure that the gateway key gets set, as it might have changed.
                if (!is_null($GatewayKey)) $ep['GatewayKey'] = $GatewayKey;
                if (!empty($ep['DeviceKey'])) {
                    unset($ep['params']);
                    $return = $this->update($ep);
                    //$return = $this->db->AutoExecute('devices', $ep, 'UPDATE', 'DeviceKey='.$res['DeviceKey']);
                } else {
                    if (!empty($ep["HWPartNum"])) {
                        if (!empty($ep["FWPartNum"])) {
                            if (!empty($ep["SerialNum"])) {
                                unset($ep['DeviceKey']);
                                unset($ep['params']);
                                //$return = $this->db->AutoExecute('devices', $ep, 'INSERT');
                                $return = $this->insert($ep);
                            }
                        }
                    }
                }
            }                    
        }
        return $return;                    
    }

    /**
     * This sets the device paramters in the database.  The device parameters
     * are stored as string (text) in the database.  This routine takes in the
     * parameter array, encodes it, then stores it in the database.
     *
     * @param int   $DeviceKey The key for the device to be stored
     * @param array $params    The parameter array to be stored.
     *
     * @return mixed
     *
     * @uses device::encodeParams
     */
    function setParams($DeviceKey, $params) 
    {
        if (is_array($params)) $params = device::encodeParams($params);
        $info = array(
            "DeviceKey" => $DeviceKey,
            "params"    => $this->encodeParams($params),
        );
        return $this->update($info);
    }

    /**
     * Checks to see if this is a controller.
     *
     * @param array &$info This is a device information array
     *
     * @return bool
     */
    function isController(&$info)
    {
        return method_exists($this->_driver->drivers[$info['Driver']], "checkProgram");
    }

    /**
     *  Encodes the parameter array and returns it as a string
     *
     * @param array &$params the parameter array to encode
     *
     * @return string
     */
    function encodeParams(&$params) 
    {
        if (is_array($params)) {
            $params = serialize($params);
            $params = base64_encode($params);
        } else if (!is_string($params)) {
            $params = "";
        }
        return $params;
    }

    /**
     *  Decodes the parameter string and returns it as a array
     *
     * @param string &$params the parameter array to decode
     *
     * @return array
     */
    function decodeParams(&$params) 
    {
        if (is_string($params)) {
            $params = base64_decode($params);
            $params = unserialize($params);
        } else if (!is_array($params)) {
            $params = array();
        }
        return $params;    
    }

    /**
     * Queries health information from the database.
     * 
     * @param string $where Extra where clause for the SQL
     * @param int    $days  The number of days back to go
     * @param mixed  $start The start date of the health report
     *
     * @return array The array of health information
     * 
     * @todo This should be moved to the device class
     */
    function health($where, $days = 7, $start=null) 
    {

        if ($start === null) {
            $start = time();
        } else if (is_string($start)) {
            $start = strtotime($start);
        }
        $end = $start - (86400 * $days);
        $cquery = "SELECT COUNT(DeviceKey) as count FROM ".$this->table." ";
        $cquery .= " WHERE PollInterval > 0 ";
        if (!empty($where)) $cquery .= " AND ".$where;
        $res   = $this->query($cquery);
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

        $res = $this->query($query);
        if (isset($res[0])) $res = $res[0];
        return $res;
    }



    /**
     * Sends out an all call so all boards respond.
     *
     * Returns a style based on the condition of the endpoint.  Useful for displaying
     * a list of endpoints and quickly seeing which ones have problems.
     *
     * @param array $Info Infomation about the device to get stylesheet information for
     *
     * @return string The return should be put inside of style="" css tags in your HTML
     */
    function diagnose($Info) 
    {
        $problem = array();
        if ($Info["PollInterval"] > 0) {
            $timelag = time() - strtotime($Info["LastPoll"]);
            $pollhistory = (strtotime($Info["LastPoll"]) - strtotime($Info["LastHistory"]));
            if ($pollhistory < 0) $pollhistory = (-1)*$pollhistory;
            
            if (($timelag > ($this->PollWarningIntervals*60*$Info["PollInterval"]))) {
                $problem[] = "Last Poll ".$this->get_ydhms($timelag)." ago\n";
            }
            if ($pollhistory > 1800) {
                $problem[] = "History ".$this->get_ydhms($pollhistory)." old\n";
            }
            if ($Info['ActiveSensors'] == 0) {
                $problem[] = "No Active Sensors\n";
            }
        }
        return($problem);        
    }
    /**
     * Creates the database table
     *
     * @return mixed
      */
    function createTable() 
    {
        $query = "CREATE TABLE IF NOT EXISTS `".$this->table."` (
                      `DeviceKey` int(11) NOT null,
                      `DeviceID` varchar(6) NOT null default '',
                      `DeviceName` varchar(128) NOT null default '',
                      `SerialNum` bigint(20) NOT null default '0',
                      `HWPartNum` varchar(12) NOT null default '',
                      `FWPartNum` varchar(12) NOT null default '',
                      `FWVersion` varchar(8) NOT null default '',
                      `RawSetup` varchar(128) NOT null default '',
                      `Active` varchar(4) NOT null default 'YES',
                      `GatewayKey` int(11) NOT null default '0',
                      `ControllerKey` int(11) NOT null default '0',
                      `ControllerIndex` tinyint(4) NOT null default '0',
                      `DeviceLocation` varchar(64) NOT null default '',
                      `DeviceJob` varchar(64) NOT null default '',
                      `Driver` varchar(32) NOT null default '',
                      `PollInterval` mediumint(9) NOT null default '0',
                      `ActiveSensors` smallint(6) NOT null default '0',
                      `DeviceGroup` varchar(6) NOT null default '',
                      `BoredomThreshold` tinyint(4) NOT null default '0',
                      `LastConfig` datetime NOT null default '0000-00-00 00:00:00',
                      `LastPoll` datetime NOT null default '0000-00-00 00:00:00',
                      `LastHistory` datetime NOT null default '0000-00-00 00:00:00',
                      `LastAnalysis` datetime NOT null default '0000-00-00 00:00:00',
                      `MinAverage` varcar(16) NOT null default '15MIN',
                      `CurrentGatewayKey` int(11) NOT null default '0',
                      `params` text NOT null,
                      PRIMARY KEY  (`DeviceKey`)
                    );
                    ";
                    
        $ret = $this->query($query);
        $ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `SerialNum` ON `'.$this->table.'` (`SerialNum`)');
        $ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `DeviceID` ON `'.$this->table.'` (`DeviceID`,`GatewayKey`)');
        $this->getColumns();
        return $ret;
    }

}




?>
