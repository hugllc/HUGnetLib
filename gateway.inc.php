<?php
/**
	$Id: gateway.inc.php 659 2007-02-21 23:07:09Z prices $

	@file gateway.inc.php
	@brief Class for manipulating the gateway database information

	
	
*/


/**
	@brief Database interface class for gateways
	
	This class started out as both a database interface class
	and a class for talking with gateways.  That has changed
	and it is now only the database interface class.  Use ep_socket
	and EPacket for talking with gateways.
*/
class gateway {
	var $table = "gateways";				//!< The database table to use
	var $id = "GatewayKey";	 //!< This is the Field name for the key of the record

	/**
		@brief Constructor
		@param $servers Array The servers to use.
		@param $db String The database to use
		@param $options the database options to use.
	*/
	function gateway(&$driver) 
	{
		$this->db = &$driver->db;
		$this->packet = &$driver->packet;
	}

	/**
		@brief Try to automatically find out which gateway to use
		@param $verbose Boolean Whether to send output to the terminal or not
		@return FALSE on failure, Array of gateway information on success
	*/
	function Find($verbose = FALSE) {
		$return = FALSE;
		if (function_exists("posix_uname")) {
			if ($verbose) print "Trying to figure out which gateway to use...\r\n";
			$stuff = posix_uname();
			// Lookup up a gateway based on our host name
			if ($verbose) print "Looking for ".$stuff['nodename']."...\r\n";
/*			$this->db->addWhereSearch("GatewayIP", gethostbyname($stuff["nodename"]));*/
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
		@private
		@brief Changes seconds into YDHMS
		@param $seconds Float The number of seconds
		@param $digits Integer The number of digits after the decimal point in the returned seconds
		@return String The number of years, days, hours, minutes, and seconds in the original number of seconds.

		This is for uptime displays.
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
		@private
		@brief Changes number of bytes into human readable numbers using K, M, G, T, Etc
		@param $bytes Integer the original number of bytes
		@param $digits Integer The number places to the right of the decimal point to show
		@return String The number of bytes human readable.
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

    function get($key) {
        $ret = $this->db->getArray("SELECT * from ".$this->table." where ".$this->id." = '".$key."'");
        if (is_array($ret)) $ret = $ret[0];
        return $ret;
    }

}

class gatewayCache {
	var $table = "gateways";				//!< The database table to use
	var $primaryCol = "GatewayKey";	 //!< This is the Field name for the key of the record

    var $fields = array(
            "GatewayKey" => "int(11)",
	        "GatewayIP" => "varcar(15)",
	        "GatewayName" => "varcar(30)",
	        "GatewayLocation" => "varchar(64)",
	        "database" => "varchar(64)",
	        "FirmwareStatus" => "varchar(16)",
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
        $query = "CREATE TABLE `gateways` (
                  `GatewayKey` int(11) NOT NULL auto_increment,
                  `GatewayIP` varchar(15) NOT NULL default '',
                  `GatewayName` varchar(30) NOT NULL default '',
                  `GatewayLocation` varchar(64) NOT NULL default '',
                  `database` varchar(64) NOT NULL default '',
                  `FirmwareStatus` varchar(16) NOT NULL default 'RELEASE',
                  PRIMARY KEY  (`GatewayKey`),
                );
                    ";
        $ret = $this->_sqlite->query($query);
        $ret = $this->_sqlite->query('CREATE UNIQUE INDEX `GatewayIP` ON `'.$this->table.'` (`GatewayIP`)');
        $ret = $this->_sqlite->query('CREATE UNIQUE INDEX `GatewayName` ON `'.$this->table.'` (`GatewayName`)');
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
        if (isset($info['GatewayName']) 
                && isset($info['GatewayIP']) 
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
        if (isset($info['GatewayKey'])) {
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
