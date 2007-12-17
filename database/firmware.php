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
class firmware extends DbBase
{
    /** The table to use */
    var $table = "firmware";
    /** This is the Field name for the key of the record */
    var $id = "FirmwareKey";
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
    function InterpSREC($srec, $MemBufferSize, $PageSize=0, $MemBufferEmpty="FF") 
    {
        $MemBuffer = str_repeat($MemBufferEmpty, $MemBufferSize);
    
        if (!is_array($srec)) {
            $srec = explode("\n", $srec);
        }
        if (is_array($srec)) {
            foreach ($srec as $rec) {
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
                        $data = false;
                        $addr = false;
                        $size = false;
                        $csum = false;
                        break;
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
    //            $page = str_replace($MemBufferEmpty, "", $page);
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
     * Returns the latest firmware for the part number given
     * 
     * @param string $FWPartNum This is the part number of the firmware wanted
     * @param string $Status    This is the status of the firmware
     * @param bool   $All       If this is true any firmware not listed as BAD is returned
     *
     * @return array The array of firmware information
     */
    function GetLatestFirmware($FWPartNum, $Status=null, $All=false) 
    {
        /*
        $this->reset();
        $this->addWhere("FWPartNum='".$FWPartNum."'");
        $this->addWhere("FirmwareStatus='".$Status."'");
        $ret = $this->getAll();
         */

        $query = "SELECT * FROM ".$this->table
                ." WHERE "
                ." FWPartNum='".$FWPartNum."' ";
        if ($Status !== null) {
            $query .= " AND "
                    ." FirmwareStatus='".$Status."' ";
        } else if (!$All) {
            $query .= " AND "
                    ." FirmwareStatus<>'BAD' ";
        }
        $query .= " ORDER BY Date DESC "
                ." LIMIT 0,1 ";
        $ret    = $this->query($query);
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
    function GetFirmwareFor($HWPartNum, $Status=null) 
    {
        $HWPartNum = substr($HWPartNum, 0, 7);

        $query = "SELECT * FROM ".$this->table
                ." WHERE "
                ." HWPartNum='".$HWPartNum."' ";
        if ($Status !== null) {
            $query .= " AND "
                    ." FirmwareStatus='".$Status."' ";
        }
        $query .= " ORDER BY FWPartNum DESC, Date DESC ";
        $ret    = $this->query($query);
        return $ret;
    }

    /**
     * 
     * @param string $FWPartNum This is the part number of the firmware wanted
     * @param string $Status    This is the status of the firmware
     * @param string $version   The particular version to get
     *
     * @return array array of firmware information arrays    
     */
    function GetFirmware($FWPartNum, $version=null, $Status=null) 
    {

        $query = "SELECT * FROM ".$this->table
                ." WHERE "
                ." FWPartNum='".$FWPartNum."' ";
        if ($version !== null) {
            $query .= " AND "
                    ." FirmwareVersion='".$version."' ";
        }
        if ($Status !== null) {
            $query .= " AND "
                    ." FirmwareStatus='".$Status."' ";
        }
        $query .= " ORDER BY FWPartNum DESC, Date DESC ";
        $ret    = $this->query($query);
        return $ret;
    }


}

?>
