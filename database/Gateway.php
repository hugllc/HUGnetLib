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
 */

/** This where our base class lives */
require_once HUGNET_INCLUDE_PATH."/base/HUGnetDB.php";

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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class Gateway extends HUGnetDB
{
    var $table = "gateways";                //!< The database table to use
    var $id = "GatewayKey";     //!< This is the Field name for the key of the record
    /** The number of columns */
    private $_columns = 6;
    /**
     * This returns an array setup for a HTML select list using the adodb
     * function 'GetMenu'
     *
     * @param string $name       The name of the select list
     * @param mixed  $selected   The entry that is currently selected
     * @param int    $GatewayKey The key to use if only one gateway is to be selected
     *
     * @return mixed
     */    
    function select($where = " isVisible <> 0 ", $data = array()) 
    {
        $rows = $this->getWhere($where, $data);
        $ret = array();
        foreach ($rows as $row) {
            $ret[$row["GatewayKey"]] = $row["GatewayName"];
        }
        return $ret;
    }    
    

    /**
     * Try to automatically find out which gateway to use
     *
     * @return mixed false on failure, Array of gateway information on success
     */
    function find() 
    {
        if (function_exists("posix_uname")) {
            $this->vprint("Trying to figure out which gateway to use...");
            $stuff = posix_uname();
            // Lookup up a gateway based on our host name
            $this->vprint("Looking for ".$stuff['nodename']."...");
            $ip  = gethostbyname($stuff["nodename"]);
            $res = $this->getWhere("GatewayIP = ? ", array($ip));
            if (isset($res[0])) {
                // We found one.  Set it up and warn the user.
                $this->vprint("Using ".$res[0]["GatewayName"].".  I hope that is what you wanted.");
                return $res[0];
            }
        }
        return false;
    }

    /**
     * This function creates the table in the database
     *
     * @param string $table The table to use if not the default
     *
     * @return mixed The output of the last SQL statement
     */
    function createTable($table = null) 
    {
        if (is_string($table)) $this->table = $table;

        $query = "CREATE TABLE IF NOT EXISTS `".$this->table."` (
                  `GatewayKey` int(11) NOT null,
                  `GatewayIP` varchar(15) NOT null default '',
                  `GatewayName` varchar(30) NOT null default '',
                  `GatewayLocation` varchar(64) NOT null default '',
                  `database` varchar(64) NOT null default '',
                  `FirmwareStatus` varchar(16) NOT null default 'RELEASE',
                  PRIMARY KEY  (`GatewayKey`)
               );";
                    
        $ret = $this->query($query);
        $ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `GatewayIP` ON `'.$this->table.'` (`GatewayIP`)');
        $ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `GatewayName` ON `'.$this->table.'` (`GatewayName`)');
        $this->getColumns();
        return $ret;
    }


}
?>
