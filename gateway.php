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
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Gateways
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** This where our base class lives */
require_once HUGNET_INCLUDE_PATH."/base/DbBase.php";

/**
 * Database interface class for gateways
 *   
 * This class started out as both a database interface class
 * and a class for talking with gateways.  That has changed
 * and it is now only the database interface class.  Use ep_socket
 * and EPacket for talking with gateways.
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Gateways
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class Gateway extends DbBase
{
    var $table = "gateways";                //!< The database table to use
    var $id = "GatewayKey";     //!< This is the Field name for the key of the record

    /**
     * Try to automatically find out which gateway to use
     *
     * @param bool $verbose Whether to send output to the terminal or not
     *
     * @return false on failure, Array of gateway information on success
     */
    function Find($verbose = false) 
    {
        $return = false;
        if (function_exists("posix_uname")) {
            if ($verbose) print "Trying to figure out which gateway to use...\r\n";
            $stuff = posix_uname();
            // Lookup up a gateway based on our host name
            if ($verbose) print "Looking for ".$stuff['nodename']."...\r\n";
            $res = $this->getWhere("GatewayIP = ? ", array(gethostbyname($stuff["nodename"])));
            if (isset($res[0])) {
                // We found one.  Set it up and warn the user.
                $return = $res[0];
                if ($verbose) print "Using ".$res[0]["GatewayName"].".  I hope that is what you wanted.\r\n";
            }
        }
        return $return;
    }

    /**
     * This function creates the table in the database
     *
     * @return mixed The output of the last SQL statement
     */
    function createTable($table = null) {
        if (is_string($table)) $this->table = $table;

        $query = "CREATE TABLE `".$this->table."` (
                  `GatewayKey` int(11) NOT null auto_increment,
                  `GatewayIP` varchar(15) NOT null default '',
                  `GatewayName` varchar(30) NOT null default '',
                  `GatewayLocation` varchar(64) NOT null default '',
                  `database` varchar(64) NOT null default '',
                  `FirmwareStatus` varchar(16) NOT null default 'RELEASE',
                  PRIMARY KEY  (`GatewayKey`),
                );
                    ";
        $ret = $this->query($query);
        $ret = $this->query('CREATE UNIQUE INDEX `GatewayIP` ON `'.$this->table.'` (`GatewayIP`)');
        $ret = $this->query('CREATE UNIQUE INDEX `GatewayName` ON `'.$this->table.'` (`GatewayName`)');
        $this->_getColumns();
        return $ret;
    }


}
/**
 * This will go away
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Gateways
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class gatewayCache extends DbBase {
    var $table = "gateways";                //!< The database table to use
    var $in = "GatewayKey";     //!< This is the Field name for the key of the record

    /**
     * Constructor
     * @param string $file The file name to store the database in    
     * @param int $mode The octal mode to set the file to.
     * @param string $error A variable to store errors in.
     */
    function __construct($file = null) {
        if (is_string($file)) $this->file = $file;
        
        $this->_db = new PDO("sqlite:".$this->file.".sq3");
        parent::__construct($this->db);

    }
    
    /**
     *
      */
    function createTable() {
        Gateway::createTable();
        return $ret;
    }    
}


?>
