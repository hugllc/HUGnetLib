<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/HUGnetClass.php";
require_once dirname(__FILE__)."/../../base/HUGnetContainer.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class endpointContainer extends HUGnetContainer
{
    /** These are the endpoint information bits */
    /** @var array The list of keys here */
    private $_attributes = array(
        "DeviceKey", "DeviceID", "DeviceName", "SerialNum", "HWPartNum",
        "FWPartNum", "FWVersion", "RawSetup", "Active", "GatewayKey",
        "ControllerKey", "ControllerIndex", "DeviceLocation", "DeviceJob",
        "Driver", "PollInterval", "ActiveSensors", "DeviceGroup",
        "BoredomThreshold", "LastConfig", "LastPoll", "LastHistory",
        "LastAnalysis", "MinAverage", "params",
    );
    /** @var int The main key in the database */
    public $DeviceKey;
    /** @var string This is the 'name' used to contact this unit */
    public $DeviceID;
    /** @var string The name this device has been given */
    public $DeviceName;
    /** @var int The serial number of this device */
    public $SerialNum;
    /** @var string The hardware part number of this device */
    public $HWPartNum;
    /** @var string The firmware part number of this device */
    public $FWPartNum;
    /** @var string The firmware version of this device */
    public $FWVersion;
    /** @var string The raw setup for this device */
    public $RawSetup;
    /** @var int Whether or not this device is active */
    public $Active;
    /** @var int The Gateway this device is attached to */
    public $GatewayKey;
    /** @var int The Controller this device is attached to */
    public $ControllerKey;
    /** @var int The HUGnet port on the controller for this device */
    public $ControllerIndex;
    /** @var string The location this device is in */
    public $DeviceLocation;
    /** @var string The job of this device */
    public $DeviceJob;
    /** @var string The driver to use for this device */
    public $Driver;
    /** @var int The poll interval in minutes */
    public $PollInterval;
    /** @var int How many active sensors are on this device */
    public $ActiveSensors;
    /** @var string The group this device is in */
    public $DeviceGroup;
    /** @var int The boredom threshold.  **** NOT CURRENTLY USED **** */
    public $BoredomThreshold;
    /** @var string The last time the configuration on this device was checked */
    public $LastConfig;
    /** @var string The last time this device was polled */
    public $LastPoll;
    /** @var string The last time the history was updated for this device */
    public $LastHistory;
    /** @var string The last time this device was analyzed */
    public $LastAnalysis;
    /** @var string The minimum average to calculate for this device */
    public $MinAverage;
    /** @var array Device parameters */
    public $params;

    /**
    * Makes sure the deviceID is valid
    *
    * @return null
    */
    private function _deviceID()
    {
        self::setStringSize($this->DeviceID, 6);
    }
}
?>
