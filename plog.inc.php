<?php
/**
	$Id$
*/

class plog {

    var $table = "PacketLog";
    var $index = 1;
    var $file = NULL;

    function __construct($name = NULL, $file=NULL) {
        if (!is_null($file)) {
            $this->file = $file;
        } else {
            $this->file = get_temp_dir()."/".HUGNET_LOCAL_DATABASE;
        }
//        $this->_sqlite = new SQLiteDatabase($this->file, $mode, $error);
        $this->_sqlite = new PDO("sqlite:".$this->file.".sq3");
        if (!empty($name)) {
            $this->table = $name;
        }

        @$this->createPacketLog();
       
        $this->getID();
    }

    function getID() {
        $query = "SELECT MAX(id) as id from '".$this->table."'";    
        $ret = $this->_sqlite->query($query, PDO::FETCH_ASSOC);
        if (is_object($ret)) {
            $ret = $ret->fetchAll(PDO::FETCH_ASSOC);       
        }
        $newID  = (isset($ret[0]['id'])) ? (int) $ret[0]['id'] : 1 ;
        return $newID + 1;
    }
    
    function createPacketLog() {
        
        $query = " CREATE TABLE '".$this->table."' (
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
        return @$this->_sqlite->query($query);
    }

    function get($where, $limit=0, $start=0) {
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
            return $this->_sqlite->query($query);



        } else {
            return FALSE;
        }
    }



    function getAll($limit=0, $start=0) {
        return $this->get(1, $limit, $start);
    }

    function remove($info) {
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

}

?>