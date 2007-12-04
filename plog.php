<?php
/**
 * Packet logging code.
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
 * @subpackage PacketLogging
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/**
 * This class logs packets into the database
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage PacketLogging
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Plog
{
    /** @var string Database table to use */
    private $table = "PacketLog";
    /** @var int Some kind of index */
    private $index = 1;
    /** @var mixed The file to find the SQLite database in */
    private $file = null;
    /** @var mixed The description of the critical error that just happened. */ 
    public $criticalError = false;

    /**
     * Constructor
     *
     * @param string $name The name of the table to use.
     * @param string $file The name of the file to use.
     */
    function __construct($name = null, $file=null) 
    {
        if (!empty($file)) {
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

    /**
     * Gets the next ID to use from the table
     *
     * @return int
     */
    function getID() 
    {
        if (!is_object($this->_sqlite)) return false;
        $query = "SELECT MAX(id) as id from '".$this->table."'";    
        $ret   = $this->_sqlite->query($query, PDO::FETCH_ASSOC);
        if (is_object($ret)) {
            $ret = $ret->fetchAll(PDO::FETCH_ASSOC);       
        }
        $newID = (isset($ret[0]['id'])) ? (int) $ret[0]['id'] : 1 ;
        return $newID + 1;
    }
    
    /**
     * Returns the query needed to create the packet log
     *
     * @param string $table The table to use in the query
     *
     * @return string
     */
    function createPacketLogQuery($table="")
    {
        if (empty($table)) $table = $this->table;
        $query = " CREATE TABLE '".$table."' (
                      'id' int(11) NOT null,
                      'DeviceKey' int(11) NOT null default '0',
                      'GatewayKey' int(11) NOT null default '0',
                      'Date' datetime NOT null default '0000-00-00 00:00:00',
                      'Command' varchar(2) NOT null default '',
                      'sendCommand' varchar(2) NOT null default '',
                      'PacketFrom' varchar(6) NOT null default '',
                      'PacketTo' varchar(6) NOT null default '',
                      'RawData' text NOT null default '',
                      'sentRawData' text NOT null default '',
                      'Type' varchar(32) NOT null default 'UNSOLICITED',
                      'Status' varchar(32) NOT null default 'NEW',
                      'ReplyTime' float NOT null default '0',
                      'Checked' int(11) NOT null default '0',
                      PRIMARY KEY  ('id')
                    );
                    ";
        return $query;
    }

    /**
     * Creates the packet log table.
     *
     * @return mixed
     */
    function createPacketLog() 
    {
        if (!is_object($this->_sqlite)) return false;
        $query = $this->createPacketLogQuery();        
        $ret   = @$this->_sqlite->query($query);
        return $ret;
    }

    /**
     * Returns the rows the where statement finds
     *
     * @param string $where a valid SQL where statement
     * @param int    $limit The max number of rows to return
     * @param int    $start The number of the entry to start on
     *
     * @return mixed
     */
    function get($where, $limit=0, $start=0) 
    {
        if (!is_object($this->_sqlite)) return false;

        $query = "SELECT * FROM '".$this->table."' WHERE ".$where;
        if ($limit > 0) $query .= " limit ".$start.", ".$limit;
        $res = $this->_sqlite->query($query);
        if (is_object($res)) {
            $ret = $res->fetchAll(PDO::FETCH_ASSOC);
            return $ret;
        } else {
            return false;
        }
    }

    /**
     * Returns the first row the where statement finds
     *
     * @param string $where a valid SQL where statement
     *
     * @return mixed
     */
    function getOne($where = null) 
    {
        if (!is_object($this->_sqlite)) return false;

        $query = "SELECT * FROM '".$this->table."' ";
        if (!empty($where)) $query .= " WHERE ".$where;

        $res = $this->_sqlite->query($query);
        if (is_object($res)) {
            $ret = $res->fetch(PDO::FETCH_ASSOC);

            if (isset($ret)) {
                return $ret;
            } else {
                return false;
            }
        }
    }


    /**
     * Returns the first row the where statement finds
     *
     * @param array $info The row to insert into the database
     *
     * @return mixed
     */
    function add($info) 
    {    
        if (!is_object($this->_sqlite)) return false;
        if (isset($info['PacketFrom']) 
                && isset($info['PacketFrom']) 
                && !empty($info['GatewayKey']) 
                && !empty($info['Date']) 
                && isset($info['Command']) 
                && !empty($info['sendCommand'])
                ) {

            $div    = "";
            $fields = "";
            $values = "";
            $doId   = true;
            foreach ($info as $key => $val) {
                if (!is_null($val)) {
                    $fields .= $div.$key;
                    $values .= $div."'".$val."'";
                    $div = ", ";
                    if ($key == "id") $doId = false;
                }
            }
            if ($doId) {
                $fields .= $div."id";
                $values .= $div."'".$this->index."'";
                $this->index++;
            }
            $query = " REPLACE INTO '".$this->table."' (".$fields.") VALUES (".$values.")";
            $ret   = $this->_sqlite->query($query);
            return $ret;


        } else {
            return false;
        }
    }



    /**
     * Returns all of the rows from te database
     *
     * @param int $limit The max number of rows to return
     * @param int $start The number of the entry to start on
     *
     * @return mixed
     */
    function getAll($limit=0, $start=0) 
    {
        return $this->get(1, $limit, $start);
    }

    /**
     * Removes a row from the database
     *
     * @param array $info The row to insert into the database
     *
     * @return mixed
     */
    function remove($info)
    {
        if (!is_object($this->_sqlite)) return false;
        if (is_array($info) && isset($info['id'])) {
            $where = " id=".$info['id'];
            $query = " DELETE FROM '".$this->table."' WHERE ".$where;
            $ret   = $this->_sqlite->query($query);
            if (is_object($ret)) $ret = true;
            return $ret;
        } else {
            return false;
        }
    
    }

    /**
     * Converts a packet array into an array for inserting into the packet log tables in the database.
     *
     * @param array  $Packet  The packet that came in.
     * @param int    $Gateway The gateway key of the gateway this packet came from
     * @param string $Type    They type of packet it is.
     *
     * @return array
     */
    public static function packetLogSetup($Packet, $Gateway, $Type="") 
    {
        if (empty($Type)) $Type = "UNSOLICITED";
        $Info = array();
        if (isset($Packet["DeviceKey"])) {
            $Info["DeviceKey"] = $Packet["DeviceKey"];
        } else if (isset($Gateway["DeviceKey"])) {
            $Info["DeviceKey"] = $Gateway["DeviceKey"];
        }
        $Info['ReplyTime']   = (float) $Packet['ReplyTime'];
        $Info["GatewayKey"]  = $Gateway["GatewayKey"];
        $Info["RawData"]     = $Packet["RawData"];
        $Info["Date"]        = date("Y-m-d H:i:s", $Packet["Time"]);
        $Info["PacketFrom"]  = $Packet["From"];
        $Info["Command"]     = $Packet["Command"];
        $Info["sendCommand"] = isset($Packet["sendCommand"]) ? $Packet["sendCommand"] : '  ';
        $Info ["Type"]       = $Type;
        return $Info;
    }

}

?>
