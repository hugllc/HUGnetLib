<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
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
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** The base for all database classes */
require_once HUGNET_INCLUDE_PATH."/base/HUGnetDB.php";
require_once HUGNET_INCLUDE_PATH."/devInfo.php";
/**
 * Class for talking with HUGNet endpoints
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Device extends HUGnetDB implements HUGnetDBInterface
{
    /** The table devices are stored in */
    var $table = "devices";
    /** The key used for the table */
    var $id = "DeviceKey";
    /** The number of columns */
    private $_columns = 26;

    /** The table the analysis output is stored in */
    var $analysis_table = "analysis";
    /** How many times the poll interval has to pass before we show an error on it */
    var $PollWarningIntervals = 2;
    /**
     * Gets all rows from the database
     *
     * @param string $where   Where clause
     * @param array  $data    Data for query
     * @param int    $limit   The maximum number of rows to return (0 to return all)
     * @param int    $start   The row offset to start returning records at
     * @param string $orderby The order by clause.  Must include "ORDER BY"
     *
     * @return array
     */
    public function getWhere(
        $where,
        $data    = array(),
        $limit   = 0,
        $start   = 0,
        $orderby = ""
    ) {
        $data = parent::getWhere($where, $data, $limit, $start, $orderby);
        foreach ($data as $key => $val) {
            $data[$key]["params"] = self::decodeParams($val["params"]);
        }
        return $data;
    }
    /**
     * This returns an array setup for a HTML select list using the adodb
     * function 'GetMenu'
     *
     * @param string $name       The name of the select list
     * @param mixed  $selected   The entry that is currently selected
     * @param int    $GatewayKey The key to use if only one gateway is to be selected
     * @param int    $active     If only active endpoints should be selected
     *
     * @return mixed
     */
    function selectDevice($name=null, $selected=null, $GatewayKey=null, $active=1)
    {
        $data  = array();
        $query = "SELECT DeviceKey, DeviceID, DeviceName FROM devices WHERE";
        if (empty($GatewayKey)) {
            $data[] = 0;
            $query .= " GatewayKey <> ?";
        } else {
            $data[] = $GatewayKey;
            $query .= " GatewayKey = ? ";
        }
        $active = (int) $active;
        if (!empty($active)) {
            $data[] = 1;
            $query .= "AND active = ? ";
        }
        $query .= " ORDER BY DeviceName";
        $rows   = $this->query($query, $data, true);
        $ret    = array();
        foreach ($rows as $row) {
            $ret[$row["DeviceKey"]] = $row["DeviceID"]." ".$row["DeviceName"];
        }
        return $ret;
    }

    /**
     * Gets the database record of a devices.  It can be fed the DeviceID,
     * DeviceName, or DeviceKey, depending on the type paramater.  This also
     * gets gateway information as well as calibration information on the device.
     *
     * @param mixed $id   This is either the DeviceID, DeviceName or DeviceKey
     * @param int   $type The type of the 'id' parameter.  It is "ID" for DeviceID,
     *         "NAME" for DeviceName or "KEY" for DeviceKey.  "KEY" is the default.
     *
     * @return array
     */
    function getDevice($id, $type="KEY")
    {
        if (empty($id)) {
            return array();
        }
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
            $devInfo           = $devInfo[0];
            $devInfo["params"] = $this->decodeParams($devInfo["params"]);
        }
        return $devInfo;
    }

    /**
     * Runs a function using the correct driver for the endpoint
     *
     * @param array $DevInfo Array of information about the device
     *                    with the data from the incoming packet
     * @param bool  $force   Force the update even if the serial number
     *                    and hardware part number don't match
     *
     * @return mixed
     */
    function updateDevice($DevInfo, $force=false)
    {
        if (!is_array($DevInfo)) {
            return false;
        }
        if (empty($DevInfo["DeviceID"])) {
            return false;
        }
        if (empty($DevInfo["HWPartNum"])) {
            return false;
        }
        if (empty($DevInfo["SerialNum"])) {
            return false;
        }
        $DeviceID = devInfo::sn2DeviceID($DevInfo["SerialNum"]);
        if (strtoupper($DevInfo["DeviceID"]) != $DeviceID) {
            return false;
        }
        unset($DevInfo['params']);
        $res = $this->getDevice($DevInfo["DeviceID"], 'ID');

        if (empty($res["DeviceKey"])) {
            if (empty($DevInfo["FWPartNum"])) {
                return false;
            }
            unset($DevInfo['DeviceKey']);
            return $this->add($DevInfo);
        }

        if (empty($DevInfo['LastConfig'])) {
            return false;
        }
        if ($res["HWPartNum"] != $DevInfo["HWPartNum"]) {
            return false;
        }
        if (strtotime($res["LastConfig"]) > strtotime($DevInfo["LastConfig"])) {
            return false;
        }
        // @codeCoverageIgnoreStart
        if (empty($DevInfo['SerialNum'])) {
            unset($DevInfo['SerialNum']);
        }
        // @codeCoverageIgnoreEnd
        $DevInfo["DeviceKey"] = $res["DeviceKey"];
        return $this->update($DevInfo);
    }

    /**
     * Updates a row in the database.
     *
     * This function MUST be overwritten by child classes
     *
     * @param array $info The row in array form.
     *
     * @return mixed
     */
    public function update($info)
    {
        if (array_key_exists("params", $info)) {
            self::encodeParams($info["params"]);
        }
        return parent::update($info);
    }
    /**
     * Adds an row to the database
     *
     * @param array $info    The row in array form
     * @param bool  $replace If true it replaces the "INSERT"
     *                       keyword with "REPLACE".  Not all
     *                       databases support "REPLACE".
     *
     * @return bool
     */
    public function add($info, $replace = false)
    {
        if (array_key_exists("params", $info)) {
            self::encodeParams($info["params"]);
        }
        return parent::add($info, $replace);
    }
    /**
     * Adds an row to the database
     *
     * @param array $info    The row in array form
     * @param bool  $replace If true it replaces the "INSERT"
     *                       keyword with "REPLACE".  Not all
     *                       databases support "REPLACE".
     *
     * @return bool
     */
    public function addVirtual($info, $replace = false)
    {
        if ($replace === false) {
            $info["SerialNum"] = $this->getPrevID("SerialNum");
        }
        $info["DeviceID"]   = devInfo::sn2DeviceID($info["SerialNum"]);
        $info["HWPartNum"]  = "VIRTUAL";
        $info["FWPartNum"]  = "VIRTUAL";
        $info["FWVersion"]  = "VIRTUAL";
        $info["Driver"]     = "eVIRTUAL";
        $info["GatewayKey"] = -1;
        $info["Active"]     = 1;

        if (self::add($info, $replace)) {
            return $info["SerialNum"];
        }
        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
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
        $info = array(
            "DeviceKey" => $DeviceKey,
            "params"    => self::encodeParams($params),
        );
        return $this->update($info);
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
        }
        if (!is_string($params)) {
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
        }
        if (!is_array($params)) {
            $params = array();
        }
        return $params;
    }



    /**
     * Sends out an all call so all boards respond.
     *
     * Returns a style based on the condition of the endpoint.  Useful for displaying
     * a list of endpoints and quickly seeing which ones have problems.
     *
     * @param array $Info Infomation about the device to get stylesheet
     *                    information for
     * @param int   $time The time to use
     *
     * @return string The return should be put inside of style="" css tags in
     *                your HTML
     */
    function diagnose($Info, $time = null)
    {
        $problem = array();
        // @codeCoverageIgnoreStart
        if (empty($time)) {
            $time = time();
        }
        // @codeCoverageIgnoreEnd
        if ($Info["PollInterval"] <= 0) {
            return array();
        }
        $timelag     = $time - strtotime($Info["LastPoll"]);
        $pollTime    = strtotime($Info["LastPoll"]);
        $histTime    = strtotime($Info["LastHistory"]);
        $pollhistory = ($pollTime - $histTime);
        if ($pollhistory < 0) {
            $pollhistory = (-1)*$pollhistory;
        }
        if (($timelag > ($this->PollWarningIntervals*60*$Info["PollInterval"]))) {
            $problem[] = "Last Poll ".devInfo::getYdhms($timelag)." ago\n";
        }
        if ($pollhistory > 1800) {
            $problem[] = "History ".devInfo::getYdhms($pollhistory)." old\n";
        }
        if (empty($Info['ActiveSensors'])) {
            $problem[] = "No Active Sensors\n";
        }
        return $problem;
    }

    /**
     * Creates the database table
     *
     * @return mixed
      */
    function createTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS `".$this->table."` (
                      `DeviceKey` int(11) NOT null auto_increment,
                      `DeviceID` varchar(6) NOT null default '',
                      `DeviceName` varchar(128) NOT null default '',
                      `SerialNum` bigint(20) NOT null default '0',
                      `HWPartNum` varchar(12) NOT null default '',
                      `FWPartNum` varchar(12) NOT null default '',
                      `FWVersion` varchar(8) NOT null default '',
                      `RawSetup` varchar(128) NOT null default '',
                      `Active` tinyint(4) NOT NULL default '1',
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
                      `params` text NOT null default '',
                      PRIMARY KEY  (`DeviceKey`)
                   );
                    ";

        $query = $this->cleanSql($query);
        $ret   = $this->query($query);
        $ret   = $this->query(
            'CREATE UNIQUE INDEX IF NOT EXISTS `SerialNum` ON `'
            .$this->table.'` (`SerialNum`)'
        );
        $ret   = $this->query(
            'CREATE UNIQUE INDEX IF NOT EXISTS `DeviceID` ON `'
            .$this->table.'` (`DeviceID`,`GatewayKey`)'
        );
        $this->getColumns();
        return $ret;
    }

}




?>
