<?php
/**
	$Id: firmware.inc.php 673 2007-03-01 02:59:11Z prices $

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
		return($ret[0]);
	}

	/**
	
	
	*/
	function GetFirmwareFor($HWPartNum, $Status=NULL) {
        $HWPartNum = substr($HWPartNum, 0, 7);

        $query = "SELECT * FROM ".$this->table
                ." WHERE "
                ." HWPartNum='".$HWPartNum."' ";
        if ($Status !== NULL) {
            $query .= " AND "
                    ." FirmwareStatus='".$Status."' ";
        }
        $query .= " ORDER BY FWPartNum DESC, Date DESC ";
        $ret = $this->db->getArray($query);
		return $ret;
	}

	/**
	
	
	*/
	function GetFirmware($FWPartNum, $version=NULL, $Status=NULL) {

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
		return $ret;
	}

	/**
	
	
	*/
	function get($FWKey) {

        $query = "SELECT * FROM ".$this->table
                ." WHERE "
                ." FirmwareKey=".(int)$FWKey." ";
        $ret = $this->db->getArray($query);
		return $ret[0];
	}


}

?>
