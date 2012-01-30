<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Interfaces
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/**
 * This is the interface definition for the driver class.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Interfaces
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
interface EndpointDriverInterface
{

    /**
    * Returns the packet to send to read the sensor data out of an endpoint
    *
    * This should only be defined in a driver that inherits this class if the packet
    * differs
    *
    * @param array $Info Infomation about the device to use
    *
    * @return array
    */
    public function readSensors($Info);

    /**
    * Returns the packet to send to read the sensor data out of an endpoint
    *
    * This should only be defined in a driver that inherits this class if the packet
    * differs
    *
    * @param array $Info    Infomation about the device to use
    * @param array $Packets The packet to save.
    *
    * @return bool
    */
    public function saveSensorData($Info, $Packets);

    /**
    * This function does any extra configuration updates
    * that are required by a device that aren't in the genereic
    * device::updateDevice() function.
    *
    * This function should be implemented in child classes.
    *
    * @param array $Info Infomation about the device to use
    *
    * @return bool Always false
    */
    public function updateConfig($Info);

    /**
    * Checks a database record to see if it should be interpreted.
    *
    * @param array &$work the data to work on
    *
    * @return array The same packet with the 'Data' array created
    */
    public function checkDataArray(&$work);

    /**
    * Checks a data record to determine what its status is.  It changes
    * Rec['Status'] to reflect the status and adds Rec['Statusold'] which
    * is the status that the record had originally.
    *
    * @param array $Info The information array on the device
    * @param array &$Rec The data record to check
    *
    * @return null
    */
    public function checkRecord($Info, &$Rec);

    /**
    * Read the memory of an endpoint
    *
    * @param array $Info The information array on the device
    *
    * @return array A packet array to be sent to the packet structure
    * @see EPacket
    */
    public function readMem($Info);

    /**
    * Gets the configuration variables from the device configuration
    *
    * These differ from the returnn of eDEFAULT::GetCols in that these are stored
    * in the device itself, rather than in the database.
    *
    * @return array The names of all of the configuration variables
    */
    public function getConfigVars();

    /**
    * Returns the packet to send to read the configuration out of an endpoint
    *
    * This should only be defined in a driver that inherits this class if the
    * packet differs
    *
    * @param array $Info Infomation about the device to use
    *
    * @return array
    */
    public function readConfig($Info);

    /**
    * Does something with an unsolicited packet.
    *
    * This method MUST be implemented by each driver that inherits this class
    *
    * @param array $Info Infomation about the device to use including the
    *                    unsolicited packet.
    *
    * @return always true
    */
    public function unsolicited($Info);

    /**
    * Interprets a config packet
    *
    * @param array &$Info devInfo array
    *
    * @return null
    */
    public function interpConfig(&$Info);

    /**
    * Finds the correct error code for why it was called
    *
    * @param array  $Info Infomation about the device to use
    * @param string $fct  The function that the code tried to run
    *
    * @return bool Always false
    */
    public function badDriver($Info, $fct);

    /**
    * The routine that interprets returned sensor data
    *
    * This is a minimal implementation that only picks out the common things
    * in all packets: DataIndex.  This happens so that if there is a driver that
    * the polling software doesn't know about, it will still at least try to download
    * sensor readings from the endpoint.
    *
    * This method MUST be implemented by each driver that inherits this class.
    *
    * @param array $Info    The device info array
    * @param array $Packets An array of packets to interpret
    *
    * @return array
    */
    public function interpSensors($Info, $Packets);

    /**
    * Get the columns in the database that are for this endpoint
    *
    * This is used to easily display the pertinent columns for any endpoint.
    *
    * Should NOT be implemented in child classes that class needs it to work
    * differently
    *
    * @param array $Info Infomation about the device to use
    *
    * @return array The columns that pertain to this endpoint
    */
    public function getCols($Info);

    /**
    * Get the columns in the database that are editable by the user
    *
    * This function is here so that it is easy to create pages that allow these
    * columns to be changed.
    *
    * Should NOT be implemented in child classes that class needs it to work
    * differently
    *
    * @param array $Info Infomation about the device to use
    *
    * @return array The columns that can be edited
    */
    public function getEditCols($Info);

    /**
    * Gets calibration data for this endpoint
    *
    * @param array $Info Infomation about the device to use
    *
    * @return null
    *
    * @todo make this function work?
    */
    public function readCalibration($Info);

    /**
    * Returns a packet that will set the configuration data in an endpoint
    *
    * @param array $Info  Infomation about the device to use
    * @param int   $start The first byte to program
    * @param mixed $data  The data either as an array or in hexified form
    *
    * @return false on failure, The packet in array form on success
    *
    * @todo Document this better.
    */
    public function loadConfig($Info, $start, $data);

    /**
    * Runs a function using the correct driver for the endpoint
    *
    * @param string $ver1 The first version to use in the compare
    * @param string $ver2 The second version to use in the compare
    *
    * @return int -1 if $ver1 < $ver2, 0 if $ver1 == $ver2, 1 if $ver1 > $ver2
    */
    public function compareFWVersion($ver1, $ver2);

    /**
    * Gets the name of the history table for a particular device
    *
    * @return mixed The name of the table as a string on success, false on failure
    */
    public function getHistoryTable();

    /**
    * Gets the name of the average table for a particular device
    *
    * @return mixed The name of the table as a string on success, false on failure
    */
    public function getAverageTable();

    /**
    * Gets the name of the location table for a particular device
    *
    * @return mixed The name of the table as a string on success, false on failure
    */
    public function getLocationTable();
}
?>