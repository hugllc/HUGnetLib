<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Interfaces
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/**
* This is the interface definition for the driver class.
*
* This is for devices that new code can be loaded into.
*
*
* @category   Interfaces
* @package    HUGnetLib
* @subpackage Endpoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2010 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
interface EndpointDriverLoadableInterface
{
    /**
    * Programs a page of flash
    *
    * @param array $Info Infomation about the device to use
    *
    * @return Array of MCU information on success, false on failure
    */
    function getMCUInfo($Info);

    /**
    * Programs a page of flash
    *
    * Due to the nature of flash, $Val must contain the data for
    * a whole page of flash.
    *
    * @param array  $Info Infomation about the device to use
    * @param int    $Addr The start address of this block
    * @param string $Val  The data to program into E2 as a hex string
    *
    * @return true on success, false on failure
    */
    function programFlashPage($Info, $Addr, $Val);
    /**
    * Programs a block of E2
    *
    * @param array  $Info Infomation about the device to use
    * @param int    $Addr The start address of this block
    * @param string $Val  The data to program into E2 as a hex string
    *
    * @return true on success, false on failure
    */
    function programE2Page($Info, $Addr, $Val);

    /**
    * Gets the CRC of the data
    *
    * @param array $Info Infomation about the device to use
    *
    * @return The CRC on success, false on failure
    */
    function getApplicationCRC($Info);

    /**
    * Gets the CRC of the data
    *
    * @param array $Info Infomation about the device to use
    *
    * @return The CRC on success, false on failure
    */
    function setApplicationCRC($Info);
    /**
    * Runs the application
    *
    * @param array $Info Infomation about the device to use
    *
    * @return bool true on success, false on failure
    */
    function runApplication($Info);

    /**
    * Runs the bootloader
    *
    * @param array $Info devInfo array about the device to use
    *
    * @return mixed Reply Packet on success, false on failure
    *
    */
    function runBootloader($Info);

    /**
    * Runs the application
    *
    * @param array $Info   Infomation about the device to use
    * @param array $dInfo  Not Used
    * @param bool  $update Whether or not to update the device if it needs it.
    *
    * @return bool true on success, false on failure
    */
    function checkProgram($Info, $dInfo, $update=false);
    /**
    * Runs the application
    *
    * @param array $Info        Infomation about the device to use
    *
    * @return string The part number for the firmware to use
    */
    function getFWPartNum($Info);
    /**
    * Runs the application
    *
    * @param array $Info        Infomation about the device to use
    * @param array $gw          The gateway array to use
    * @param int   $FirmwareKey The firmware key of the program to load into memory
    *
    * @return bool true on success, false on failure
    */
    function loadProgram($Info, $gw=null, $FirmwareKey=null);

}
?>