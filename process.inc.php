<?php

/**
	$Id$

	@file process.inc.php
	@brief Class for making sure that processes don't overlap

	
*/

/**
	A class for controlling processes
*/
class process {
    var $table = "process";			//!< The name of the table to use
	var $primaryCol = "ProcessKey";	 //!< This is the Field name for the key of the record
    var $statsTable = 'procStats';
    var $statPeriodic = array(
            'Daily' => 'Y-m-d',
            'Monthly' => 'Y-m',
            'Yearly' => 'Y',
        );

    var $file = NULL;
    var $FileOnly = FALSE;

	/**
		@private
		@brief constructor
		@param $servers Array The array of db servers to use
		@param $db String The database to use.
		@param $block String The type of blocking.  Defaults to "NORMAL".
		@param $name String THe name of the process.  Defaults to the scripts name.
	*/
    function __construct($file = NULL) {
        if (!is_null($file)) {
            $this->file = $file;
        } else {
            $this->file = get_temp_dir()."/".HUGNET_LOCAL_DATABASE;
        }
        if (!is_string($file)) $file = "/tmp/HUGnetLocal";
        if (!is_long($mode)) $mode = 0666;
        if ($error == NULL) $error =& $this->lastError;
		$this->getMyInfo();
//        $this->_sqlite = new SQLiteDatabase($file, $mode, $error);
        $this->_sqlite = new PDO("sqlite:".$file.".sq3");
        $this->createTable();
    }
    
	/**
		@private	
		@brief Sets up all the information about the current process.
		@param $block String Type of blocking.  Default "NORMAL"
		@param $name String The program name.  Automatically found if left out.	
	*/
	function getMyInfo($block="NORMAL", $name = FALSE) {
		$stuff = posix_uname();
		$this->me["Host"] = $stuff["nodename"];
		$this->me["OS"] = $stuff["sysname"];
		$this->me["PID"] = getmypid();
		if ($name === FALSE) {
			$this->me["Program"] = basename($_SERVER["SCRIPT_NAME"]);
		} else {
			$this->me["Program"] = $name;
		}
		$this->me["File"] = get_temp_dir()."/".trim($this->me["Program"]).".pid";
		$this->me["Block"] = $block;
		$this->me["Started"] = date("Y-m-d H:i:s");
		
	}

	/**
		@private	
		@brief Check to see if a process is running on the local machine.
		@param $PID Integer The process ID of the process to check.
		@return TRUE if the process is running, FALSE otherwise
	*/	
	function CheckProcess($PID) {
		return(posix_getpgid($PID));
	}

	/**
		@private	
		@brief Checks all of the defined processes to see if they are running.
		
		Checks all defined processes.  If they are not running it makes sure they
		are dead, then deletes them from the database, or deletes the PID file.	
	*/
	function CheckAll() {
		if ($this->FileOnly === FALSE) {
//			$this->reset();
//			$res = $this->getAll();
            $res = array();
			$KillTime = $this->KillTime * 60;
			if ($KillTime < 600) $KillTime = 600;
			$setTime = time() - $KillTime;
			foreach($res as $key => $val) {
				if ($val["Host"] != $this->me["Host"]) {
					print "[".$this->me["PID"]."] Checking ".$val["Program"]." @ ".$val["Host"]." ";
					if (($setTime > strtotime($val["LastCheckin"])) 
						&&($setTime > strtotime($val["LastChecked"]))
						&&($setTime > strtotime($val["Started"]))				
						) {
						if ($this->remove($val["ProcessKey"])) {
							print " Deleted ";
						} else {
							print " Delete Failed ";
						}
					} else {
						print " Okay ";
					}
					print "\r\n";
				}
			}
	
//			$this->reset();
//			$this->setWhere("Host='".$this->me["Host"]."'");
//			$res = $this->getAll();
            $res = array();
			foreach ($res as $key => $val) {
				print "[".$this->me["PID"]."] Checking ".$val["Program"]." (".$val["PID"].") ";
				if ($this->CheckProcess($val["PID"])) {
					print " Okay ";
					$val["LastChecked"] = date("Y-m-d H:i:s");
					$this->save($val);
				} else {
					posix_kill($val["PID"], SIGKILL);
					print " Killed ";
					
					if ($this->remove($val["ProcessKey"])) print " deleted stale process information ";
				}
				print "\r\n";
			}
		}
	}
	
	/**
		@brief Registers this process if it is not blocked.
		@param $verbose Boolean Whether to spew a whole bunch of output out.
		@return TRUE if registered.  FALSE if blocked.
	*/
	function Register($verbose=TRUE) {
        $this->me['LastCheckin'] = date("Y-m-d H:i:s");
        $this->dbRegistered = TRUE;
		if ($this->FileOnly === FALSE) $this->dbRegister();
		$this->fileRegister();
        $this->Registered = $this->dbRegistered && $this->fileRegistered;
		return $this->Registered;
	}

	/**
		@brief Checks in with the database so that it won't get killed
		@todo make it so this can't be an infinite loop.

		This function will continously try to check in by calling process::FastCheckin().
		
	*/
	function Checkin() {
		while(FALSE == $this->FastCheckin()) {
			sleep(60);
		}
	}
	
	/**
		@brief Updates its information in the database
		@return TRUE on success, FALSE on failure
		
		If the information in the database is not updated periodically the process will lose
		its registration and maybe get killed.  This tries once and if it fails it returns FALSE.	
	*/
	function FastCheckin() {
		if ($this->FileOnly === FALSE) {
			if ($this->Registered) {
				$info = $this->me;
				$info["LastCheckin"] = date("Y-m-d H:i:s");		
				$res = $this->save($this->me);
				
				if ($res === FALSE) {
					$return = $this->dbRegister();
				} else {
					$return = TRUE;
				}
	
			} else {
				$return = $this->Register(TRUE);
			}
		} else {
			$return = TRUE;	
		}

		return($return);
	}

	/**
		@private	
		@brief Registers with the database.
		@return TRUE if successful, FALSE if failed
	*/
	function dbRegister() {
		if (($this->FileOnly === FALSE) && $this->CheckDB()) {

			$info = $this->me;
			$info["LastCheckin"] = date("Y-m-d H:i:s");
//			$this->me["ProcessKey"] = $this->save($info);
            $query = "INSERT INTO '".$this->table."' "
                    ." (PID, Program, Started, LastCheckin, Block) "
                    ." VALUES ("
                    .(int) $this->me['PID']
                    .", ".$this->_sqlite->quote($this->me['Program'])." "
                    .", ".$this->_sqlite->quote($this->me['Started'])." "
                    .", ".$this->_sqlite->quote($this->me['LastCheckin'])." "
                    .", ".$this->_sqlite->quote($this->me['Block'])." "
                    .")";
            $this->dbRegistered = $this->_sqlite->query($query);
            
            return $this->dbRegistered;
		} else {
			return(TRUE);	
		}
	}
	
    function dbUnregister($PID = NULL) {
        if (is_null($PID)) {
            $PID = $this->me["PID"];
            $me = TRUE;
        } else {
            $me = FALSE;
        }
        $query = "DELETE FROM '".$this->table."' "
                ." WHERE PID=".$this->_sqlite->quote($PID);
        $return = $this->_sqlite->query($query);            
        if ($me) $this->Registered = !$return;
        return $return;
    }
	/**
		@brief Unregisters this process.	
	*/
	function Unregister() {
    	$this->dbUnregister();
    	$this->fileUnregister();
/*
	    $query = " REPLACE INTO ";		
		if ($this->_sqlite->query($query)) {
			$this->Registered = FALSE;
			return(TRUE);
		} else {
			return(FALSE);
		}
*/
	}

    
    function createTable() {

        $query = "CREATE TABLE '".$this->table."' (
                      'PID' int(11) NOT NULL default '0',
                      'Program' varchar(128) NOT NULL default '',
                      'Started' datetime NOT NULL default '0000-00-00 00:00:00',
                      'LastCheckin' datetime NOT NULL default '0000-00-00 00:00:00',
                      'Block' varchar(32) NOT NULL default 'NORMAL',
                      PRIMARY KEY  ('PID')
                    );
                    ";
        $this->_sqlite->query($query);

        $query = "CREATE TABLE '".$this->statsTable."' (
                      `PID` int(11) NOT NULL,
                      `Program` varchar(32) NOT NULL,
                      `stype` varchar(32) NOT NULL,
                      `sdate` varchar(32) NOT NULL,
                      `sname` varchar(128) NOT NULL,
                      `svalue` text NOT NULL,
                      PRIMARY KEY  (`PID`,`Program`,`stype`,`sdate`,`sname`)
                      );
                    ";
        $this->_sqlite->query($query);
        
        
    }

	/**
		@brief Checks to see if we are registered
		@param $dieonfailure Boolean Whether to die if failed to register.  Defaults to FALSE
		@param $verbose Boolean If set to TRUE It prints output for what it is doing.  Defaults to TRUE
		@todo Make this do something other than just print stuff if $dieonfailure is FALSE
	*/
	function CheckRegistered($dieonfailure=FALSE, $verbose=TRUE) {
		if ($this->Registered == FALSE) {
			if ($verbose) print "[".$this->me["PID"]."] Registration Failed\r\n";
			if ($dieonfailure) die();
		} else {
			if ($verbose) print "[".$this->me["PID"]."] Registered as ".$this->me["Program"]." @ ".$this->me["Host"]."\r\n";
		}
	}


	/**
		@brief Checks to see if we are unregistered
		@param $dieonfailure Boolean Whether to die if failed to unregister.  Defaults to FALSE
		@param $verbose Boolean If set to TRUE It prints output for what it is doing.  Defaults to TRUE
		@todo Make this do something other than just print stuff if $dieonfailure is FALSE
	*/
	function CheckUnregistered($dieonfailure=FALSE, $verbose=TRUE) {
		if ($this->Registered == TRUE) {
			if ($verbose) print "[".$this->me["PID"]."] Unregistration Failed\r\n";
			if ($dieonfailure) die();
		} else {
			if ($verbose) print "[".$this->me["PID"]."] Unregistered ".$this->me["Program"]." @ ".$this->me["Host"]."\r\n";
		}
	}

	/**
		@private
		@brief Creates a PID file
		@return TRUE on success, FALSE on failure
	*/
	function fileRegister() {
	    if ($this->checkFile()) {

    		$file = fopen($this->me["File"], 'w');
    		if ($file !== FALSE) {
    			fwrite($file, $this->me["PID"]);
    			fclose($file);
    			$this->fileRegistered = TRUE;
    		} else {
    			$this->fileRegistered = FALSE;
    		}
		} else {
			$this->fileRegistered = FALSE;
        } 
		return $this->fileRegistered;
	}

	/**
		@private
		@brief Deletes the PID file
	*/
	function fileUnregister() {
		if (file_exists($this->me["File"])) unlink($this->me["File"]);
	}

	/**
		@private
		@brief Checks each PID in the PID file if they exist.
		@return TRUE if we can run, FALSE if we are blocked.		

		This function checks the PIDs in the PID file to make sure they are
		all still running.
	*/
	function CheckFile() {
		$return = TRUE;
		if (file_exists($this->me["File"])) {
			foreach (file($this->me["File"]) as $key => $val) {
				if (is_numeric($val) && !empty($val)) {
					print "[".$this->me["PID"]."] Checking ".$val." from ".$this->me["File"];
					if ($this->CheckProcess($val)) {
						print " Okay ";
						$return = FALSE;
					} else {
						posix_kill($val, SIGKILL);
						print " Killed ";
						$this->fileUnregister();
					}
				}
				print "\r\n";
			}
		}
		return($return);
	}
	/**
		@private
		@brief Checks each PID in the PID file if they exist.
		@return TRUE if we can run, FALSE if we are blocked.		

		This function checks the PIDs in the PID file to make sure they are
		all still running.
	*/
	function CheckDB() {
		$return = TRUE;
        $query = "SELECT * FROM '".$this->table."' "
                 ." WHERE "
                ." Program='".$this->me['Program']."' ";

        $ret = $this->_sqlite->query($query, PDO::FETCH_ASSOC);
        if (is_object($ret)) {
            $rows = $ret->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as $row) {
				print "[".$this->me["PID"]."] Checking ".$row["PID"]." from Database";
				if ($this->CheckProcess($row["PID"])) {
					print " Okay ";
					$return = FALSE;
				} else {
					posix_kill($row['PID'], SIGKILL);
					print " Killed ";
                    $this->dbUnregister($row['PID']);                    

				}
  				print "\r\n";
            }
		}
		return($return);
	}

    function incStat($stat) {

        $this->incField('totals', $stat);
        foreach($this->statPeriodic as $type => $format) {
            $this->incField($type, $stat, date($format));        
        }
//        $this->incField('daily', $stat, date("Y-m-d"));
//        $this->incField('monthly', $stat,date("Y-m"));
//        $this->incField('yearly', $stat,date("Y"));
    }


    function incField($type, $name, $date="now") {
        $value = $this->getMyStat($name, $date, $type);
        $value++;
        $this->setStat($name, $value, $date, $type);
    }

    function getMyStat($name, $date="now", $type="stat") {
        return $this->getStat($name, $this->me['Program'], $date, $type, TRUE);
    }
    function getStat($name, $Program, $date="now", $type="stat", $PID=FALSE) {
        $query = "SELECT * FROM '".$this->statsTable."' "
                 ." WHERE "
                ." Program='".$Program."' "
                ." AND stype='".$type."' "
                ." AND sdate='".$date."' "
                ." AND sname='".$name."' ";
        if ($PID)$query .= " AND PID=".$this->me['PID'];
        $ret = $this->_sqlite->query($query, PDO::FETCH_ASSOC);
        if (is_object($ret)) {
            $row = $ret->fetch(PDO::FETCH_ASSOC);
        } else {
            var_dump($this->_sqlite->errorInfo());
            $row['svalue'] = 0;
        }
        return $row['svalue'];    
    }

    function setStat($name, $value, $date="now", $type="stat") {
        $this->_setStat($name, $value, $date, $type);
        $this->_setStat('StatDate', date("Y-m-d H:i:s"));
    }

    function _setStat($name, $value, $date="now", $type="stat") {

        $query = "REPLACE INTO '".$this->statsTable."' "
                ." (PID, Program, stype, sdate, sname, svalue) "
                ." VALUES ("
                .$this->me['PID']
                .", ".$this->_sqlite->quote($this->me['Program'])." "
                .", ".$this->_sqlite->quote($type)." "
                .", ".$this->_sqlite->quote($date)." "
                .", ".$this->_sqlite->quote($name)." "
                .", ".$this->_sqlite->quote($value)." "
                .")";
       $this->_sqlite->query($query);
    }

    function clearStats() {
        $query = "DELETE FROM '".$this->statsTable."' "
                ." WHERE Program='".$this->me['Program']."' ";
        $this->_sqlite->query($query);
    }

    function getPeriodicStats($Program) {
        $query = "SELECT * FROM '".$this->statsTable."' "
                ." WHERE "
                ." Program='".$Program."' "
                ." AND (";
        $sep = "";
        foreach($this->statPeriodic as $key => $value) {
            $query .= $sep."stype='".$key."' ";
            $sep = " OR ";
        }
        $query .= ") ORDER BY sdate desc";

        $ret = $this->_sqlite->query($query, PDO::FETCH_ASSOC);
        $return = array();

        if (is_object($ret)) {
            $rows = $ret->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as $row) {
                $return[$row['stype']][$row['sdate']][$row['sname']] = $row['svalue'];
            }
        }
        return $return;

    }

    function getTotalStats($Program) {
        $query = "SELECT * FROM '".$this->statsTable."' "
                ." WHERE "
                ." Program='".$Program."' "
                ." AND "
                ." stype='totals' ";

        $ret = $this->_sqlite->query($query, PDO::FETCH_ASSOC);
        $return = array();
        if (is_object($ret)) {
            $rows = $ret->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as $row) {
                $return[$row['sname']] = $row['svalue'];
            }

        }
        return $return;

    }

}

?>
