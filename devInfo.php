<?php
/**
 *   This is the default endpoint driver and the base for all other
 *   endpoint drivers.
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *   
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *   
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *   
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Endpoints
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id: eDEFAULT.php 445 2007-11-13 16:53:06Z prices $    
 *
 */

class devInfo {
    /**
     *  Sets the DeviceID if it is not set.  Valid places to set the DeviceID from are:
     *  - PacketFrom
     *  - From
     *
     * @param array $Info devInfo array
     * @return string The DeviceID
     */
    public static function DeviceID(&$Info) {
        if (empty($Info['DeviceID'])) {
            if (isset($Info['PacketFrom'])) {
                $Info['DeviceID'] = $Info['PacketFrom'];
            } else if (isset($Info['From'])) {
                $Info['DeviceID'] = $Info['From'];
            }    
        }
        if (!empty($Info['DeviceID'])) devInfo::setStringSize($Info['DeviceID'], 6);
        return $Info['DeviceID'];
    }

    /**
     *  Sets the RawData if it is not set.  Valid places to set the RawData from are:
     *  - Data
     *  - rawdata
     *  - RawSetup
     *
     * @param array $Info devInfo array
     * @return string The RawData
     */
    public static function RawData(&$Info) {
        if (empty($Info["RawData"])) {
            if (!empty($Info["Data"])) {
                $Info["RawData"] = $Info["Data"];
            } else if (!empty($Info["rawdata"])) {
                $Info["RawData"] = $Info["rawdata"];
            } else if (!empty($Info["RawSetup"])) {
                $Info['RawData'] = $Info['RawSetup'];
            }
        }
        return $Info['RawData'];
    }

    /**
     *  Sets the Date if it is not set.  Valid places to set the Date from are:
     *  - Date
     *
     * @param array $Info devInfo array
     * @param string $Field The field in the $Info array to set the date in.
     * @return string The RawData
     */
    public static function setDate(&$Info, $Field) {
        if (!empty($Info["Date"])) {
            $Info[$Field] = $Info["Date"];
        } else {
            $Info[$Field] = date("Y-m-d H:i:s");
        }
        return $Info[$Field];
    }

    
    /**
     * Sets the string to a particular size. It modifies the $value parameter.  It will
     * shorten or lengthen the string as it needs to.
     *  
     * - It will ALWAYS left pad the string if the string is too short.
     * - It will ALWAYS throw out the left end of the string if the string is too long
     *
     * @param string $value The string to fix the size of
     * @param int $size The number of characters the string should be fixed to
     * @param string $pad The characters to pad to the LEFT end of the string
     */
    public static function setStringSize(&$value, $size, $pad="0") {
        $value = trim($value);
        $value = str_pad($value, $size, $pad, STR_PAD_LEFT);
        $value = substr($value, strlen($value)-$size);
    }   
    
}
?>