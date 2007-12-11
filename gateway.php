<?php
/**
 * Class to keep track of gateways.
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
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package HUGnetLib
 * @subpackage Gateways
 * @copyright 2007 Hunt Utilities Group, LLC
 * @author Scott Price <prices@hugllc.com>
 * @version SVN: $Id$    
 *
 */


/**
 * Database interface class for gateways
    
    This class started out as both a database interface class
    and a class for talking with gateways.  That has changed
    and it is now only the database interface class.  Use ep_socket
    and EPacket for talking with gateways.
*/
class gateway {
    var $table = "gateways";                //!< The database table to use
    var $id = "GatewayKey";     //!< This is the Field name for the key of the record

    /**
     * Constructor
     * @param object $driver This is a object of class driver
     * @see driver
     */
    function gateway(&$driver) 
    {
        $this->db = &$driver->db;
        $this->packet = &$driver->packet;
    }

    /**
     * Try to automatically find out which gateway to use
     * @param bool $verbose Whether to send output to the terminal or not
     * @return false on failure, Array of gateway information on success
     */
    function Find($verbose = false) {
        if (!is_object($this->db)) return false;
        $return = false;
        if (function_exists("posix_uname")) {
            if ($verbose) print "Trying to figure out which gateway to use...\r\n";
            $stuff = posix_uname();
            // Lookup up a gateway based on our host name
            if ($verbose) print "Looking for ".$stuff['nodename']."...\r\n";
/*            $this->db->addWhereSearch("GatewayIP", gethostbyname($stuff["nodename"]));*/
            $res = $this->db->getArray('select  * from '.$this->table.' where '.
                                    "GatewayIP='".gethostbyname($stuff["nodename"])."'"
                                    );
            if (isset($res[0])) {
                // We found one.  Set it up and warn the user.
                $return = $res[0];
                if ($verbose) print "Using ".$res[0]["GatewayName"].".  I hope that is what you wanted.\r\n";
            }
        }
        return($return);
    }

    /**
     * Changes seconds into YDHMS
     * @param $seconds Float The number of seconds
     * @param $digits Integer The number of digits after the decimal point in the returned seconds
     * @return String The number of years, days, hours, minutes, and seconds in the original number of seconds.
     *
     * This is for uptime displays.
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

    /**
     * Changes number of bytes into human readable numbers using K, M, G, T, Etc
     * @param $bytes Integer the original number of bytes
     * @param $digits Integer The number places to the right of the decimal point to show
     * @return String The number of bytes human readable.
     */
    function get_bytes($bytes, $digits=2) {
        
        $labels = array("", " k", " M", " G", " T", " P");
        
        $index == 0;
        while ($bytes > 1024) {
                $bytes = $bytes/1024;
                $index ++;
        }
        $bytes = number_format($bytes, $digits);
        $bytes .= $labels[$index]." bytes";
        return($bytes);
    }
    /**
     * Get a gateway
     *
     * @param int $key The GatewayKey for th gateway to get
     * @return array The information about the gateway
      */
    function get($key) {
        if (!is_object($this->db)) return false;
        $ret = $this->db->getArray("SELECT * from ".$this->table." where ".$this->id." = '".$key."'");
        if (is_array($ret)) $ret = $ret[0];
        return $ret;
    }

    /**
     * Get all gateways
     *
     * @return array An array of gateway information arrays
      */
    function getAll() {
        if (!is_object($this->db)) return false;
        $ret = $this->db->getArray("SELECT * from ".$this->table."");
        return $ret;
    }

}

class gatewayCache {
    var $table = "gateways";                //!< The database table to use
    var $primaryCol = "GatewayKey";     //!< This is the Field name for the key of the record

    var $fields = array(
            "GatewayKey" => "int(11)",
            "GatewayIP" => "varcar(15)",
            "GatewayName" => "varcar(30)",
            "GatewayLocation" => "varchar(64)",
            "database" => "varchar(64)",
            "FirmwareStatus" => "varchar(16)",
        );
    /**
     * Constructor
     * @param string $file The file name to store the database in    
     * @param int $mode The octal mode to set the file to.
     * @param string $error A variable to store errors in.
     */
    function __construct($file = null, $mode = 0666, $error = null) {
        if ($error == null) $error =& $this->lastError;
        
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
            foreach ($columns as $col) {
                $this->fields[$col['name']] = $col['type'];
            }
        }
    }
    
    /**
     *
      */
    function createTable() {
        $query = "CREATE TABLE `gateways` (
                  `GatewayKey` int(11) NOT null auto_increment,
                  `GatewayIP` varchar(15) NOT null default '',
                  `GatewayName` varchar(30) NOT null default '',
                  `GatewayLocation` varchar(64) NOT null default '',
                  `database` varchar(64) NOT null default '',
                  `FirmwareStatus` varchar(16) NOT null default 'RELEASE',
                  PRIMARY KEY  (`GatewayKey`),
                );
                    ";
        $ret = $this->_sqlite->query($query);
        $ret = $this->_sqlite->query('CREATE UNIQUE INDEX `GatewayIP` ON `'.$this->table.'` (`GatewayIP`)');
        $ret = $this->_sqlite->query('CREATE UNIQUE INDEX `GatewayName` ON `'.$this->table.'` (`GatewayName`)');
        return $ret;
    }

    /**
     *
      */
    function addArray($InfoArray) {
        if (is_array($InfoArray)) {
            foreach ($InfoArray as $info) {
                $this->add($info);
            }
        }
    }
    
    /**
     *
      */
    function add($info) {    
        if (isset($info['GatewayName']) 
                && isset($info['GatewayIP']) 
                )
        {
            $div = "";
            $fields = "";
            $values = "";
            foreach ($this->fields as $key => $val) {
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
            return false;
        }
    }

    /**
     *
      */
    function update($info) {    
        if (isset($info['GatewayKey'])) {
            $div = "";
            $fields = "";
            $values = "";
            foreach ($this->fields as $key => $val) {
                if (isset($info[$key])) {
                    $fields .= $div.$key;
                    $values .= $div.$this->_sqlite->quote($info[$key]);
                    $div = ", ";
                }
            }


            $query = " UPDATE '".$this->table."' SET (".$fields.") VALUES (".$values.") WHERE ".$this->id."=".$info['DeviceKey'];
            return $this->_sqlite->query($query);
        } else {
            return false;
        }
    }


    /**
     *
      */
    function getAll() {
        $query = " SELECT * FROM '".$this->table."'; ";
        $ret = $this->_sqlite->query($query);
        if (is_object($ret)) $ret = $ret->fetchAll(PDO::FETCH_ASSOC);
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
            foreach ($info as $key => $val) {
                $where .= $div.$key."='".$val."'";
                $div = " AND ";
            }
            if (empty($where)) return false;

            $query = " DELETE FROM '".$this->table."' WHERE ".$where;
            $ret = $this->_sqlite->query($query);
            if (is_object($ret)) $ret = true;
            return $ret;
        } else {
            return false;
        }
    
    }
    
}


?>
