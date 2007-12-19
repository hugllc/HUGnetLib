<?php
/**
 * Unix process information and manipulation
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
 * @category   UnixProcess
 * @package    HUGnetLib
 * @subpackage UnixProcess
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

/**
 * A class for controlling processes
 *
 * @category   UnixProcess
 * @package    HUGnetLib
 * @subpackage UnixProcess
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class process
{
    /** Database table to use */
    var $table = "process";
    /** Database id to use
    var $id = "ProcessKey";
    /** Stats table to use */
    var $statsTable = 'procStats';
    /** Stats period date formats */
    var $statPeriodic = array(
            'Daily' => 'Y-m-d',
            'Monthly' => 'Y-m',
            'Yearly' => 'Y',
        );
    /** file to use */
    var $file = null;
    /** file only */
    var $FileOnly = false;


    /**
     * constructor
     *
     * @param string $file The name of the file to use.  /tmp/HUGnetLocal will be used as the default.
     */
    function __construct($file = null) 
    {
        if (!is_null($file)) {
            $this->file = $file;
        } else {
            $this->file = HUGNET_LOCAL_DATABASE;
        }
        if (!is_string($this->file)) $this->file = "/tmp/HUGnetLocal";
        if (!is_long($mode)) $mode = 0666;
        if ($error == null) $error =& $this->lastError;
        $this->getMyInfo();

        $this->_sqlite = new PDO("sqlite:".$this->file);
        $this->createTable();
    }
    
    /**
     * Sets up all the information about the current process.
     *
     * @param string $block Type of blocking.  Default "NORMAL"
     * @param string $name The program name.  Automatically found if left out.
     *
     * @return none 
     */
    function getMyInfo($block="NORMAL", $name = false) 
    {
        $stuff = posix_uname();
        $this->me["Host"] = $stuff["nodename"];
        $this->me["Domain"] = $stuff["domainname"];
        $this->me["OS"] = $stuff["sysname"];
        $this->me["PID"] = getmypid();
        if ($name === false) {
            $this->me["Program"] = basename($_SERVER["SCRIPT_NAME"]);
        } else {
            $this->me["Program"] = $name;
        }
        $this->me["File"] = dirname($this->file)."/".trim($this->me["Program"]).".pid";
        $this->me["Block"] = $block;
        $this->me["Started"] = date("Y-m-d H:i:s");
        
    }

    /**
     * Check to see if a process is running on the local machine.
     *
     * @param $PID Integer The process ID of the process to check.
     *
     * @return bool true if the process is running, false otherwise
    */    
    function CheckProcess($PID) 
    {
        return(posix_getpgid($PID));
    }

    /**
     * Checks all of the defined processes to see if they are running.
     * 
     * Checks all defined processes.  If they are not running it makes sure they
     * are dead, then deletes them from the database, or deletes the PID file.    
     *
     * @return none
     */
    function CheckAll() {
        if ($this->FileOnly === false) {
            $res = array();
            $KillTime = $this->KillTime * 60;
            if ($KillTime < 600) $KillTime = 600;
            $setTime = time() - $KillTime;
            foreach ($res as $key => $val) {
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
     * Registers this process if it is not blocked.
     *
     * @param bool $verbose Whether to spew a whole bunch of output out.
     *
     * @return bool true if registered.  false if blocked.
     */
    function Register($verbose=true) 
    {
        $this->me['LastCheckin'] = date("Y-m-d H:i:s");
        $this->dbRegistered = true;
        if ($this->FileOnly === false) $this->dbRegister();
        $this->fileRegister();
        $this->Registered = $this->dbRegistered && $this->fileRegistered;
        return $this->Registered;
    }

    /**
     * Checks in with the database so that it won't get killed
     *
     * This function will continously try to check in by calling process::FastCheckin().
     * 
     * @return none
     *
     * @todo make it so this can't be an infinite loop.
     */
    function Checkin() 
    {
        while(false == $this->FastCheckin()) {
            sleep(60);
        }
    }
    
    /**
     * Updates its information in the database
     * 
     * If the information in the database is not updated periodically the process will lose
     * its registration and maybe get killed.  This tries once and if it fails it returns false.    
     *
     * @return bool true on success, false on failure
     */
    function FastCheckin() 
    {
        if ($this->FileOnly === false) {
            if ($this->Registered) {
                $info = $this->me;
                $info["LastCheckin"] = date("Y-m-d H:i:s");        
                $res = $this->save($this->me);
                
                if ($res === false) {
                    $return = $this->dbRegister();
                } else {
                    $return = true;
                }
    
            } else {
                $return = $this->Register(true);
            }
        } else {
            $return = true;    
        }

        return($return);
    }

    /**
     * Registers with the database.
     *
     * @return bool true if successful, false if failed
     */
    function dbRegister() 
    {
        if (($this->FileOnly === false) && $this->CheckDB()) {

            $info = $this->me;
            $info["LastCheckin"] = date("Y-m-d H:i:s");
//            $this->me["ProcessKey"] = $this->save($info);
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
            return(true);    
        }
    }
    
    /**
     * Registers with the database.
     *
     * @param int $PID The process id
     *
     * @return bool true if successful, false if failed
     */
    function dbUnregister($PID = null) 
    {
        if (is_null($PID)) {
            $PID = $this->me["PID"];
            $me = true;
        } else {
            $me = false;
        }
        $query = "DELETE FROM '".$this->table."' "
                ." WHERE PID=".$this->_sqlite->quote($PID);
        $return = $this->_sqlite->query($query);            
        if ($me) $this->Registered = !$return;
        return $return;
    }
    /**
     * Unregisters this process.
     *
     * @return none
     */
    function Unregister() 
    {
        $this->dbUnregister();
        $this->fileUnregister();
    }

    
    /**
     * Creates the SQLite DB table
      */
    function createTable() 
    {

        $query = "CREATE TABLE '".$this->table."' (
                      'PID' int(11) NOT null default '0',
                      'Program' varchar(128) NOT null default '',
                      'Started' datetime NOT null default '0000-00-00 00:00:00',
                      'LastCheckin' datetime NOT null default '0000-00-00 00:00:00',
                      'Block' varchar(32) NOT null default 'NORMAL',
                      PRIMARY KEY  ('PID')
                    );
                    ";
        $this->_sqlite->query($query);

        $query = "CREATE TABLE '".$this->statsTable."' (
                      `PID` int(11) NOT null,
                      `Program` varchar(32) NOT null,
                      `stype` varchar(32) NOT null,
                      `sdate` varchar(32) NOT null,
                      `sname` varchar(128) NOT null,
                      `svalue` text NOT null,
                      PRIMARY KEY  (`PID`,`Program`,`stype`,`sdate`,`sname`)
                      );
                    ";
        $this->_sqlite->query($query);        
    }

    /**
     * Checks to see if we are registered
     *
     * @param bool $dieonfailure Whether to die if failed to register.  Defaults to false
     * @param bool $verbose If set to true It prints output for what it is doing.  Defaults to true
     *
     * @return none
     *
     * @todo Make this do something other than just print stuff if $dieonfailure is false
     */
    function CheckRegistered($dieonfailure=false, $verbose=true) 
    {
        if ($this->Registered == false) {
            if ($verbose) print "[".$this->me["PID"]."] Registration Failed\r\n";
            if ($dieonfailure) die();
        } else {
            if ($verbose) print "[".$this->me["PID"]."] Registered as ".$this->me["Program"]." @ ".$this->me["Host"]."\r\n";
        }
    }


    /**
     * Checks to see if we are unregistered
     *
     * @param bool $dieonfailure Whether to die if failed to unregister.  Defaults to false
     * @param bool $verbose If set to true It prints output for what it is doing.  Defaults to true
     *
     * @return none
     *
     * @todo Make this do something other than just print stuff if $dieonfailure is false
     */
    function CheckUnregistered($dieonfailure=false, $verbose=true) 
    {
        if ($this->Registered == true) {
            if ($verbose) print "[".$this->me["PID"]."] Unregistration Failed\r\n";
            if ($dieonfailure) die();
        } else {
            if ($verbose) print "[".$this->me["PID"]."] Unregistered ".$this->me["Program"]." @ ".$this->me["Host"]."\r\n";
        }
    }

    /**
     * Creates a PID file
     *
     * @return bool true on success, false on failure
     */
    function fileRegister() 
    {
        if ($this->checkFile()) {

            $file = fopen($this->me["File"], 'w');
            if ($file !== false) {
                fwrite($file, $this->me["PID"]);
                fclose($file);
                $this->fileRegistered = true;
            } else {
                $this->fileRegistered = false;
            }
        } else {
            $this->fileRegistered = false;
        } 
        return $this->fileRegistered;
    }

    /**
     * Deletes the PID file
     *
     * @return none
     */
    function fileUnregister() 
    {
        if (file_exists($this->me["File"])) unlink($this->me["File"]);
    }

    /**
     * Checks each PID in the PID file if they exist.
     *
     * This function checks the PIDs in the PID file to make sure they are
     * all still running.
     *
     * @return bool true if we can run, false if we are blocked.     
     */
    function CheckFile() 
    {
        $return = true;
        if (file_exists($this->me["File"])) {
            foreach (file($this->me["File"]) as $key => $val) {
                if (is_numeric($val) && !empty($val)) {
                    print "[".$this->me["PID"]."] Checking ".$val." from ".$this->me["File"];
                    if ($this->CheckProcess($val)) {
                        print " Okay ";
                        $return = false;
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
     * Checks each PID in the PID file if they exist.
     *
     * This function checks the PIDs in the PID file to make sure they are
     * all still running.
     *
     * @return bool true if we can run, false if we are blocked.        
     */
    function CheckDB() 
    {
        $return = true;
        $query = "SELECT * FROM '".$this->table."' "
                 ." WHERE "
                ." Program='".$this->me['Program']."' ";

        $ret = $this->_sqlite->query($query, PDO::FETCH_ASSOC);
        if (is_object($ret)) {
            $rows = $ret->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                print "[".$this->me["PID"]."] Checking ".$row["PID"]." from Database";
                if ($this->CheckProcess($row["PID"])) {
                    print " Okay ";
                    $return = false;
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

    /**
     * Increments stats in the database
     *
     * @return none
     */
    function incStat($stat) 
    {

        $this->incField('totals', $stat);
        foreach ($this->statPeriodic as $type => $format) {
            $this->incField($type, $stat, date($format));        
        }
    }


    /**
     * Increments fields in the database
     *
     * @return none
     */
    function incField($type, $name, $date="now") 
    {
        $value = $this->getMyStat($name, $date, $type);
        $value++;
        $this->setStat($name, $value, $date, $type);
    }

    /**
     * Retrieves a statistic
     *
     * @param string $name The name of the statistic to get
     * @param string $date The date of the statistic to get
     * @param string $type The type of statistic to get
     *
     * @return mixed The statistic in question
     */
    function getMyStat($name, $date="now", $type="stat") 
    {
        return $this->getStat($name, $this->me['Program'], $date, $type, true);
    }
    
    /**
     * Retrieves a statistic
     *
     * @param string $name    The name of the statistic to get
     * @param string $Program The name of the program
     * @param string $date    The date of the statistic to get
     * @param string $type    The type of statistic to get
     * @param int    $PID     The process id
     *
     * @return mixed The statistic in question
     */
    function getStat($name, $Program, $date="now", $type="stat", $PID=false) 
    {
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

    /**
     * Saves a statistic
     *
     * @param string $name The name of the statistic to get
     * @param mixed  $name The name of the statistic to get
     * @param string $date The date of the statistic to get
     * @param string $type The type of statistic to get
     *
     * @return mixed The statistic in question
     */
    function setStat($name, $value, $date="now", $type="stat") 
    {
        $this->_setStat($name, $value, $date, $type);
        $this->_setStat('StatDate', date("Y-m-d H:i:s"));
    }

    /**
     * Saves a statistic
     *
     * @param string $name The name of the statistic to get
     * @param mixed  $name The name of the statistic to get
     * @param string $date The date of the statistic to get
     * @param string $type The type of statistic to get
     *
     * @return mixed The statistic in question
     */
    function _setStat($name, $value, $date="now", $type="stat") 
    {

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

    /**
     * Clears all statistics
     *
     * @return none
     */
    function clearStats() 
    {
        $query = "DELETE FROM '".$this->statsTable."' "
                ." WHERE Program='".$this->me['Program']."' ";
        $this->_sqlite->query($query);
    }

    /**
     * gets all statistics for a program
     *
     * @param string $Program The name of the program to get stats for.
     *
     * @return array An array of statistics.
     */
    function getPeriodicStats($Program) 
    {
        $query = "SELECT * FROM '".$this->statsTable."' "
                ." WHERE "
                ." Program='".$Program."' "
                ." AND (";
        $sep = "";
        foreach ($this->statPeriodic as $key => $value) {
            $query .= $sep."stype='".$key."' ";
            $sep = " OR ";
        }
        $query .= ") ORDER BY sdate desc";

        $ret = $this->_sqlite->query($query, PDO::FETCH_ASSOC);
        $return = array();

        if (is_object($ret)) {
            $rows = $ret->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $return[$row['stype']][$row['sdate']][$row['sname']] = $row['svalue'];
            }
        }
        return $return;

    }

    /**
     * gets all statistics for a program
     *
     * @param string $Program The name of the program to get stats for.
     *
     * @return array An array of statistics.
     */
    function getTotalStats($Program) 
    {
        $query = "SELECT * FROM '".$this->statsTable."' "
                ." WHERE "
                ." Program='".$Program."' "
                ." AND "
                ." stype='totals' ";

        $ret = $this->_sqlite->query($query, PDO::FETCH_ASSOC);
        $return = array();
        if (is_object($ret)) {
            $rows = $ret->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $return[$row['sname']] = $row['svalue'];
            }

        }
        return $return;

    }

}

?>
