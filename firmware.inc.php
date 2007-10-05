<php
/*
HUGnetLib is a library of HUGnet code
Copyright (C) 2007 Hunt Utilities Group, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 3
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
?>
<?php
/**
	$Id$

	@file firmware.inc.php
	@brief Class for talking with endpoints

	
*/



/**
	@brief Class for storing and retrieving firmware.

	This class implements the packet structure for talking with endpoints.
*/
class firmware {
    var $table = "firmware";		//!< The table to use
	var $id = "FirmwareKey";	 //!< This is the Field name for the key of the record
    var $cache = array();
    var $cacheTimeout = 86400;    // Cache times out every day.

	/**
		@brief Constructor
		@param $db String The database to use
		@param $servers Array The servers to use.
		@param $options the database options to use.

	*/
	function firmware(&$db) {
        $this->db = &$db;
	}


	/**
		@brief Changes an SREC source into a raw memory buffer
		@param $srec String The S record to change.
		@param $MemBufferSize Int the total available space in the memory buffer
		@param $PageSize Int the total number of bytes in 1 page of memory.  0 means no pages
		@param $MemBufferEmpty String This is what a byte looks like when it is erased.
			The default is for flash memory (FF);
		@return The raw memory buffer	
	*/
	function InterpSREC($srec, $MemBufferSize, $PageSize=0, $MemBufferEmpty="FF") {
		$MemBuffer = str_repeat($MemBufferEmpty, $MemBufferSize);
	
		if (!is_array($srec)) {
			$srec = explode("\n", $srec);
		}
		if (is_array($srec)) {
			foreach($srec as $rec) {
				switch(substr($rec, 0, 2)) {
					case "S1":
						$size = hexdec(substr($rec, 2, 2));
						$size -= 3;
						$addr = hexdec(substr($rec, 4, 4));
						$data = substr($rec, 8, ($size*2));
						$csum = hexdec(substr($rec, (8+($size*2)), 2));					
						break;
					case "S9":
					case "S1":
					default:
						// Ignore it.
						$data = FALSE;
						$addr = FALSE;
						$size = FALSE;
						$csum = FALSE;
						break;
				}
				if ($data != FALSE) {
					$MemBuffer = substr_replace($MemBuffer, $data, ($addr*2), ($size*2));
				}
			}
		}
		if ($PageSize > 0) {
			$MemBuffer = str_split($MemBuffer, ($PageSize*2));
			$MemBuffer = array_reverse($MemBuffer);
			foreach($MemBuffer as $pnum => $page) {
	//			$page = str_replace($MemBufferEmpty, "", $page);
				while (strlen($page) > 0) {
					if (substr($page, 0, 2) == $MemBufferEmpty) {
						$page = substr($page, 2);
					} else {
						break;
					}
				}
				if (empty($page)) {
					unset($MemBuffer[$pnum]);
				} else {
					break;
				}
			}
			$MemBuffer = array_reverse($MemBuffer);
		} else {
			while (substr($MemBuffer, (strlen($MemBuffer)-2), 2) == $MemBufferEmpty) {
				$MemBuffer = substr($MemBuffer, 0, (strlen($MemBuffer)-2));
			}
		}
		return($MemBuffer);
	}


    /**
	
	
	*/
	function GetLatestFirmware($FWPartNum, $Status=NULL, $All=FALSE) {
        /*
		$this->reset();
		$this->addWhere("FWPartNum='".$FWPartNum."'");
		$this->addWhere("FirmwareStatus='".$Status."'");
		$ret = $this->getAll();
        */

        if ($ret = $this->cache($FWPartNum)) return $ret;
        
        $query = "SELECT * FROM ".$this->table
                ." WHERE "
                ." FWPartNum='".$FWPartNum."' ";
        if ($Status !== NULL) {
            $query .= " AND "
                    ." FirmwareStatus='".$Status."' ";
        } else if (!$All) {
            $query .= " AND "
                    ." FirmwareStatus<>'BAD' ";
        }
        $query .= " ORDER BY Date DESC "
                ." LIMIT 0,1 ";
        $ret = $this->db->getArray($query);
        $this->cache($FWPartNum, $ret[0]);
		return $ret[0];
	}

	/**
	
	
	*/
	function GetFirmwareFor($HWPartNum, $Status=NULL) {
        $HWPartNum = substr($HWPartNum, 0, 7);

        if ($ret = $this->cache($HWPartNum)) return $ret;
        $query = "SELECT * FROM ".$this->table
                ." WHERE "
                ." HWPartNum='".$HWPartNum."' ";
        if ($Status !== NULL) {
            $query .= " AND "
                    ." FirmwareStatus='".$Status."' ";
        }
        $query .= " ORDER BY FWPartNum DESC, Date DESC ";
        $ret = $this->db->getArray($query);
        $this->cache($HWPartNum, $ret);
		return $ret;
	}

	/**
	
	
	*/
	function GetFirmware($FWPartNum, $version=NULL, $Status=NULL) {

        if ($ret = $this->cache($FWPartNum)) return $ret;

        $query = "SELECT * FROM ".$this->table
                ." WHERE "
                ." FWPartNum='".$FWPartNum."' ";
        if ($version !== NULL) {
            $query .= " AND "
                    ." FirmwareVersion='".$version."' ";
        }
        if ($Status !== NULL) {
            $query .= " AND "
                    ." FirmwareStatus='".$Status."' ";
        }
        $query .= " ORDER BY FWPartNum DESC, Date DESC ";
        $ret = $this->db->getArray($query);
        $this->cache($FWPartNum, $ret);
		return $ret;
	}

	/**
	
	
	*/
	function get($FWKey) {
        if ($ret = $this->cache($FWKey)) return $ret;

        $query = "SELECT * FROM ".$this->table
                ." WHERE "
                ." FirmwareKey=".(int)$FWKey." ";
        $ret = $this->db->getArray($query);
        $this->cache($FWKey, $ret[0]);
		return $ret[0];
	}

    function cache($key, $save=FALSE) {
        if ($save === FALSE) {
            if ((time() - $this->cacheDate[$key]) > $this->cacheTimeout) return FALSE;
            if (is_array($this->cache[$key])) {
                return $this->cache[$key];
            } else {
                foreach($this->cache as $val) {
                    if ($val['FirmwareKey'] == $key) return $val;
                    if ($val['FWPartNum'] == $key) return $val;
                    if ($val['HWPartNum'] == $key) return $val;
                }
                return FALSE;
            }
        } else {
            if (is_array($save) && (count($save) > 0)) {
                $this->cache[$key] = $save;
            }
            // Reset the cache time whether we got data or not.  That way if
            // we lose connection to the database we will just continue to use
            // the cached value.
            $this->cacheDate[$key] = time();
        }
    }

}

?>
