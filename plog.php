<?php
/**
 *   Packet logging code.
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
 *   @subpackage PacketLogging
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$    
 *
 */

class plog {

    private $table = "PacketLog";
    private $index = 1;
    private $file = NULL;
    public $criticalError = FALSE;

    function __construct($name = NULL, $file=NULL) {
        if (!is_null($file)) {
            $this->file = $file;
        } else {
            $this->file = HUGNET_LOCAL_DATABASE;
        }
                
        if (is_writable($this->file)) {
            $this->_sqlite = new PDO("sqlite:".$this->file);
            if (!empty($name)) {
                $this->table = $name;
            }
    
            @$this->createPacketLog();

            $this->getID();
        } else {
            $this->criticalError = "Database Not Writable!";
        }
       
    }

    function getID() {
        if (!is_object($this->_sqlite)) return FALSE;
        $query = "SELECT MAX(id) as id from '".$this->table."'";    
        $ret = $this->_sqlite->query($query, PDO::FETCH_ASSOC);
        if (is_object($ret)) {
            $ret = $ret->fetchAll(PDO::FETCH_ASSOC);       
        }
        $newID  = (isset($ret[0]['id'])) ? (int) $ret[0]['id'] : 1 ;
        return $newID + 1;
    }
    
    function createPacketLogQuery($table="")     {
        if (empty($table)) $table = $this->table;
        $query = " CREATE TABLE '".$table."' (
                      'id' int(11) NOT NULL,
                      'DeviceKey' int(11) NOT NULL default '0',
                      'GatewayKey' int(11) NOT NULL default '0',
                      'Date' datetime NOT NULL default '0000-00-00 00:00:00',
                      'Command' varchar(2) NOT NULL default '',
                      'sendCommand' varchar(2) NOT NULL default '',
                      'PacketFrom' varchar(6) NOT NULL default '',
                      'PacketTo' varchar(6) NOT NULL default '',
                      'RawData' text NOT NULL default '',
                      'sentRawData' text NOT NULL default '',
                      'Type' varchar(32) NOT NULL default 'UNSOLICITED',
                      'Status' varchar(32) NOT NULL default 'NEW',
                      'ReplyTime' float NOT NULL default '0',
                      'Checked' int(11) NOT NULL default '0',
                      PRIMARY KEY  ('id')
                    );
                    ";
        return $query;
    }
    function createPacketLog() {
        if (!is_object($this->_sqlite)) return FALSE;
        $query = $this->createPacketLogQuery();        
        $ret = @$this->_sqlite->query($query);
        return $ret;
    }

    function get($where, $limit=0, $start=0) {
        if (!is_object($this->_sqlite)) return FALSE;

        $query = "SELECT * FROM '".$this->table."' WHERE ".$where;
        if ($limit > 0) $query .= " limit ".$start.", ".$limit;
        $res = $this->_sqlite->query($query);
        if (is_object($res)) {
            $ret = $res->fetchAll(PDO::FETCH_ASSOC);
            return($ret);
        } else {
            return FALSE;
        }
    }

    function getOne($where = NULL) {
        if (!is_object($this->_sqlite)) return FALSE;

        $query = "SELECT * FROM '".$this->table."' ";
        if (!empty($where)) $query .= " WHERE ".$where;

        $res = $this->_sqlite->query($query);
        if (is_object($res)) {
            $ret = $res->fetch(PDO::FETCH_ASSOC);

            if (isset($ret)) {
                return $ret;
            } else {
                return FALSE;
            }
        }
    }



    function add($info) {    
        if (!is_object($this->_sqlite)) return FALSE;
        if (isset($info['PacketFrom']) 
                && isset($info['PacketFrom']) 
                && !empty($info['GatewayKey']) 
                && !empty($info['Date']) 
                && isset($info['Command']) 
                && !empty($info['sendCommand'])
                )
        {

            $div = "";
            $fields = "";
            $values = "";
            $doId = TRUE;
            foreach($info as $key => $val) {
                if (!is_null($val)) {
                    $fields .= $div.$key;
                    $values .= $div."'".$val."'";
                    $div = ", ";
                    if ($key == "id") $doId = FALSE;
                }
            }
            if ($doId) {
                $fields .= $div."id";
                $values .= $div."'".$this->index."'";
                $this->index++;
            }
            $query = " REPLACE INTO '".$this->table."' (".$fields.") VALUES (".$values.")";
            $ret = $this->_sqlite->query($query);
            return $ret;


        } else {
            return FALSE;
        }
    }



    function getAll($limit=0, $start=0) {
        return $this->get(1, $limit, $start);
    }

    function remove($info) {
        if (!is_object($this->_sqlite)) return FALSE;
        if (is_array($info) && isset($info['id']))
        {
/*
            $div = "";
            $where = "";
            foreach($info as $key => $val) {
                $where .= $div.$key."='".$val."'";
                $div = " AND ";
            }
            if (empty($where)) return FALSE;
*/
            $where = " id=".$info['id'];
            $query = " DELETE FROM '".$this->table."' WHERE ".$where;
            $ret = $this->_sqlite->query($query);
            if (is_object($ret)) $ret = TRUE;
            return $ret;
        } else {
            return FALSE;
        }
    
    }

    /**
     * Converts a packet array into an array for inserting into the packet log tables in the database.
     * @param $Packet Array The packet that came in.
     * @param $Gateway Integer the gateway key of the gateway this packet came from
     * @param $Type String They type of packet it is.
     */
    public static function packetLogSetup($Packet, $Gateway, $Type="") {
        //$this->device->lookup($Packet["from"], "DeviceID");
//        $Info = $this->device->lookup[0];
        $Info = array();
        if (isset($Packet["DeviceKey"])) {
            $Info["DeviceKey"] = $Packet["DeviceKey"];
        } else if (isset($Gateway["DeviceKey"])) {
            $Info["DeviceKey"] = $Gateway["DeviceKey"];
        }
        $Info['ReplyTime'] = isset($Packet['ReplyTime']) ? $Packet['ReplyTime'] : 0 ;
        $Info["GatewayKey"]= $Gateway["GatewayKey"];
        $Info["RawData"] = $Packet["RawData"];
        $Info["Date"] = date("Y-m-d H:i:s", $Packet["Time"]);
        $Info["PacketFrom"] = $Packet["From"];
        $Info["Command"] = $Packet["Command"];
        $Info["sendCommand"] = isset($Packet["sendCommand"]) ? $Packet["sendCommand"] : '  ';
        if (!empty($Type)) {
            $Info["Type"] = $Type;
        } else {
            $Info["Type"] = "UNSOLICITED";        
        }

        return $Info;
    }

}

?>
