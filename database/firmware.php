<?php
/**
 * Class to keep track of firmware and store it in the database.
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
 * @subpackage Firmware
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This where our base class lives */
require_once HUGNET_INCLUDE_PATH."/base/DbBase.php";

/**
 * Class for storing and retrieving firmware.
 *
 * @category   Database
 * @package    HUGnetLib
 * @subpackage Gateways
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class Firmware extends DbBase
{
    /** The table to use */
    var $table = "firmware";
    /** This is the Field name for the key of the record */
    var $id = "FirmwareKey";
    /** The number of columns */
    private $_columns = 11;
    /** This is our data cache */
    var $cache = array();
    /** Cache times out every day. */
    var $cacheTimeout = 86400;


    /**
     * Changes an SREC source into a raw memory buffer
     *
     * @param string $srec           The S record to change.
     * @param int    $MemBufferSize  The total available space in the memory buffer
     * @param int    $PageSize       The total number of bytes in 1 page of memory.  0 means no pages
     * @param string $MemBufferEmpty This is what a byte looks like when it is erased.
     *    The default is for flash memory (FF);
     *
     * @return string The raw memory buffer    
     */
    function interpSREC($srec, $MemBufferSize, $PageSize=0, $MemBufferEmpty="FF") 
    {
        $MemBuffer = str_repeat($MemBufferEmpty, $MemBufferSize);
    
        if (!is_array($srec)) {
            $srec = explode("\n", $srec);
        }
        if (is_array($srec)) {
            foreach ($srec as $rec) {
                $head = substr($rec, 0, 2);
                if ($head == "S1") {
                    $size  = hexdec(substr($rec, 2, 2));
                    $size -= 3;
                    $addr  = hexdec(substr($rec, 4, 4));
                    $data  = substr($rec, 8, ($size*2));
                    $csum  = hexdec(substr($rec, (8+($size*2)), 2));
                } else {
                    // Ignore it.
                    $data = false;
                    $addr = false;
                    $size = false;
                    $csum = false;
                }
                if ($data != false) {
                    $MemBuffer = substr_replace($MemBuffer, $data, ($addr*2), ($size*2));
                }
            }
        }
        if ($PageSize > 0) {
            $MemBuffer = str_split($MemBuffer, ($PageSize*2));
            $MemBuffer = array_reverse($MemBuffer);
            foreach ($MemBuffer as $pnum => $page) {
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
        return $MemBuffer;
    }

    /**
     * Creates the database table
     *
     * @param string $table The table name to use
     *
     * @return void
     */
    function createTable($table="") 
    {
        if (!empty($table)) $this->table = $table;
        $query = "CREATE TABLE IF NOT EXISTS `firmware` (
                  `FirmwareKey` mediumint(9) NOT NULL,
                  `FirmwareVersion` varchar(8) NOT NULL default '',
                  `FirmwareCode` longtext NOT NULL,
                  `FirmwareData` longtext NOT NULL,
                  `FWPartNum` varchar(12) NOT NULL default '',
                  `HWPartNum` varchar(12) NOT NULL default '',
                  `Date` datetime default '0000-00-00 00:00:00',
                  `FirmwareFileType` varcar(4) NOT NULL default 'SREC',
                  `FirmwareStatus` varcar(8) NOT NULL default 'DEV',
                  `FirmwareCVSTag` varchar(64) NOT NULL default '',
                  `Target` varchar(16) NOT NULL default 'attiny26',
                  PRIMARY KEY  (`FirmwareKey`)
                  );";
        $this->query($query);
        $ret = $this->query('CREATE UNIQUE INDEX IF NOT EXISTS `FirmwareVersion` ON `'.$this->table.'` (`FirmwareVersion`,`FWPartNum`,`HWPartNum`)');
        $this->getColumns();
    }
    
    /**
     * Returns the latest firmware for the part number given
     * 
     * @param string $FWPartNum This is the part number of the firmware wanted
     * @param string $Status    This is the status of the firmware
     * @param bool   $All       If this is true any firmware not listed as BAD is returned
     *
     * @return array The array of firmware information
     */
    function getLatestFirmware($FWPartNum, $Status=null, $All=false) 
    {
        $data  = array($FWPartNum);
        $query = " FWPartNum= ? ";
        if ($Status !== null) {
            $data[] = $Status;
            $query .= " AND "
                    ." FirmwareStatus= ? ";
        } else if (!$All) {
            $query .= " AND "
                    ." FirmwareStatus<>'BAD' ";
        }
        $query .= " ORDER BY Date DESC "
                ." LIMIT 0,1 ";
        $ret    = $this->getWhere($query, $data);
        return $ret[0];
    }

    /**
     * This get the firmware for a particular piece of hardware.
     * 
     * @param string $HWPartNum This is the part number of the firmware wanted
     * @param string $Status    This is the status of the firmware
     *
     * @return array The array of firmware information    
     */
    function getFirmwareFor($HWPartNum, $Status=null) 
    {
        $HWPartNum = substr($HWPartNum, 0, 7);
        $data      = array($HWPartNum);

        $query = " HWPartNum= ? ";
        if ($Status !== null) {
            $data[] = $Status;
            $query .= " AND "
                    ." FirmwareStatus= ? ";
        }
        $query .= " ORDER BY FWPartNum DESC, Date DESC ";
        $ret    =  $this->getWhere($query, $data);
        return $ret;
    }

    /**
     * Returns a piece of firmware
     * 
     * @param string $FWPartNum This is the part number of the firmware wanted
     * @param string $version   The particular version to get
     * @param string $Status    This is the status of the firmware
     *
     * @return array array of firmware information arrays    
     */
    function getFirmware($FWPartNum, $version=null, $Status=null) 
    {

        $data  = array($FWPartNum);
        $query = " FWPartNum= ? ";
        if ($version !== null) {
            $data[] = $version;
            $query .= " AND "
                    ." FirmwareVersion= ? ";
        }
        if ($Status !== null) {
            $data[] = $Status;
            $query .= " AND "
                    ." FirmwareStatus= ? ";
        }
        $query .= " ORDER BY FWPartNum DESC, Date DESC ";
        return $this->getWhere($query, $data);
    }


}

?>
