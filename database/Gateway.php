<?php
/**
 * Class to keep track of gateways.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2009 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
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
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
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
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class Gateway extends HUGnetDB
{
    var $table = "gateways";                //!< The database table to use
    var $id = "GatewayKey";     //!< This is the Field name for the key of the record
    /** The number of columns */
    private $_columns = 7;
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
        $ret = array(VIRTUAL_ENDPOINT_GATEWAY => "Virtual");
        foreach ($rows as $row) {
            $ret[$row["GatewayKey"]] = $row["GatewayName"];
        }
        return $ret;
    }

    /**
    * Gets all rows from the database
    *
    * @param string $where   Where clause
    * @param array  $data    Data for query
    * @param int    $limit   The maximum number of rows to return (0 to return all)
    * @param int    $start   The row offset to start returning records at
    * @param string $orderby The orderby Clause.  Must include "ORDER BY"
    *
    * @return array
    */
    public function getWhere($where,
                             $data = array(),
                             $limit = 0,
                             $start = 0,
                             $orderby = "")
    {
        $query = parent::getWhere($where, $data, $limit, $start, $orderby);
        foreach ($query as $key => $row) {
            $query[$key]["GatewayIP"] = $this->decodeIP($row["GatewayIP"]);
        }
        return $query;
    }
    /**
    * Updates a row in the database.
    *
    * This function MUST be overwritten by child classes
    *
    * @param array  $info  The row in array form.
    * @param string $where Where clause
    * @param array  $data  Data for query
    *
    * @return mixed
    */
    public function updateWhere($info, $where, $data = array())
    {
        $info["GatewayIP"] = $this->encodeIP($info["GatewayIP"]);
        return parent::updateWhere($info, $where, $data);
    }
        /**
    * Adds an row to the database
    *
    * @param array $info    The row in array form
    * @param bool  $replace If true it replaces the "INSERT"
    *                       keyword with "REPLACE".  Not all
    *                       databases support "REPLACE".
    *
    * @return bool
    */
    public function add($info, $replace = false)
    {
        $info["GatewayIP"] = $this->encodeIP($info["GatewayIP"]);
        return parent::add($info, $replace);
    }
    /**
     * Try to automatically find out which gateway to use
     *
     * @param string $IP The string to decode
     *
     * @return mixed false on failure, Array of gateway information on success
     */
    function decodeIP($IP)
    {
        $ret = array();
        if (is_string($IP)) {
            // This gives us the old way
            if (stristr($IP, ":") === FALSE) {
                return $IP;
            }
            $ip = explode("\n", $IP);
            foreach ($ip as $line) {
                if (empty($line)) {
                    continue;
                }
                $l = explode(":", $line);
                $ret[$l[0]] = $l[1];
            }
        }
        return $ret;
    }

    /**
     * Try to automatically find out which gateway to use
     *
     * @param string $array The array to encode
     *
     * @return mixed false on failure, Array of gateway information on success
     */
    function encodeIP($array)
    {
        $ret = "";
        if (is_array($array)) {
            foreach ($array as $key => $val) {
                $ret .= $key.":".$val."\n";
            }
        } if (is_string($array)) {
            return $array;
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
            $res = $this->getWhere("GatewayIP like ? ", array("%$ip%"));
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
                  `GatewayIP` varchar(255) NOT null default '',
                  `GatewayName` varchar(30) NOT null default '',
                  `GatewayLocation` varchar(64) NOT null default '',
                  `database` varchar(64) NOT null default '',
                  `FirmwareStatus` varchar(16) NOT null default 'RELEASE',
                  `isVisible` int(4) NOT null default 0,
                  PRIMARY KEY  (`GatewayKey`)
               );";

        $ret = $this->query($query);
        $ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `GatewayIP` ON `'.$this->table.'` (`GatewayIP`)');
        $ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `GatewayName` ON `'.$this->table.'` (`GatewayName`)');
        $this->getColumns();
        return $ret;
    }

    /**
     * This function creates the table in the database
     *
     * @param string $table The table to use if not the default
     *
     * @return mixed The output of the last SQL statement
     */
    function createLocalTable($table = null)
    {
        if (is_string($table)) $this->table = $table;
        $this->id = "DeviceID";

        $query = "CREATE TABLE IF NOT EXISTS `".$this->table."` (
                  `DeviceID` varchar(6) NOT null,
                  `CurrentGatewayKey` int(11) NOT null,
                  `IP` varchar(15) NOT null default '',
                  `Name` varchar(30) NOT null default '',
                  `HWPartNum` varchar(32) NOT null,
                  `FWPartNum` varchar(32) NOT null,
                  `FWVersion` varchar(10) NOT null,
                  `LastContact` datetime NOT null,
                  `RawSetup` varchar(255) NOT null,
                  `Job` int(11) NOT null default 0,
                  `Priority` int(11) NOT null default 0,
                  `Local` int(11) NOT null default 0,
                  PRIMARY KEY  (`DeviceID`, `Local`)
               );";

        $ret = $this->query($query);
        $this->getColumns();
        return $ret;
    }


}
?>
