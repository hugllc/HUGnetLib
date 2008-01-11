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
/** The base for all database classes */
require_once HUGNET_INCLUDE_PATH."/base/DbBase.php";

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
class Plog extends DbBase
{
    /** @var string Database table to use */
    protected $table = "PacketLog";
    /** The number of columns */
    private $_columns = 14;
    /** @var int Some kind of index */
    private $index = 1;
    /** @var mixed The file to find the SQLite database in */
    protected $file = null;
    /** @var mixed The description of the critical error that just happened. */ 
    public $criticalError = false;

    /**
     * Returns the query needed to create the packet log
     *
     * @param string $table The table to use in the query
     *
     * @return string
      */
    function createTable($table="")
    {
        if (empty($table)) $table = $this->table;
        $query = " CREATE TABLE IF NOT EXISTS '".$table."' (
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
        $this->query($query);
        $this->getColumns();
        return $query;
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
        if (!isset($info[$this->id])) $info[$this->id] = $this->index++;
        return parent::add($info, true);
    }
    

    /**
     * Converts a packet array into an array for inserting into the 
     * packet log tables in the database.
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
        $Info['ReplyTime']  = (float) $Packet['ReplyTime'];
        $Info["GatewayKey"] = $Gateway["GatewayKey"];
        $Info["RawData"]    = $Packet["RawData"];
        if (!empty($Packet["Time"])) {
            $Info["Date"] = date("Y-m-d H:i:s", $Packet["Time"]);
        } else {
            $Info["Date"] = date("Y-m-d H:i:s");        
        }
        $Info["PacketTo"]    = $Packet["To"];
        $Info["PacketFrom"]  = $Packet["From"];
        $Info["Command"]     = $Packet["Command"];
        $Info["sendCommand"] = isset($Packet["sendCommand"]) ? $Packet["sendCommand"] : '  ';
        $Info ["Type"]       = $Type;
        return $Info;
    }

}

?>
