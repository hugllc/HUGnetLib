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
require_once dirname(__FILE__)."/../base/HUGnetClass.php";
require_once dirname(__FILE__)."/../base/HUGnetExtensibleContainer.php";
require_once dirname(__FILE__)."/../devInfo.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DeviceContainer extends HUGnetExtensibleContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "DeviceKey"         => 0,               // Database key
        "DeviceID"          => "000000",        // Device ID
        "DeviceName"        => "",              // Name of the device
        "SerialNum"         => 0,               // Serial number
        "HWPartNum"         => "",              // Hardware Part Number
        "FWPartNum"         => "",              // Firmware Part Number
        "FWVersion"         => "",              // Firmware Version
        "RawSetup"          => "",              // The raw setup
        "RawCalibration"    => "",              // The raw calibration
        "Active"            => 0,               // Is the device active
        "GatewayKey"        => 0,               // The gateway for this
        "ControllerKey"     => 0,               // The controller to use
        "ControllerIndex"   => 0,               // The index on the controller
        "DeviceLocation"    => "",              // The location of the device
        "DeviceJob"         => "",              // The job of the device
        "Driver"            => "eDEFAULT",      // The driver to use
        "PollInterval"      => 0,               // The poll interval in minutes
        "ActiveSensors"     => 0,               // How many active sensors
        "DeviceGroup"       => "FFFFFF",        // What group the device is in
        "BoredomThreshold"  => 50,              // Not currently used
        "LastConfig"        => "2000-01-01 00:00:00",  // Last configuration check
        "LastPoll"          => "2000-01-01 00:00:00",  // Last poll
        "LastHistory"       => "2000-01-01 00:00:00",  // Last history record
        "LastAnalysis"      => "2000-01-01 00:00:00",  // Last analysis performed
        "MinAverage"        => "15MIN",         // How often to do averages
        "CurrentGatewayKey" => 0,               // Not used
        "params"            => null,            // Device Parameters
    );
    /** @var array This is where the data is stored */
    protected $data = array();

    /** The database table to use */
    protected $table = "devices";
    /** This is the Field name for the key of the record */
    protected $id = "DeviceKey";

    /**
    * Builds the class
    *
    * @param array $data The data to build the class with
    *
    * @return null
    */
    public function __construct($data)
    {
        $this->data = $this->default;
        $this->fromArray($data);
    }

    /**
    * Disconnects from the gateway
    *
    * @return null
    */
    public function __destruct()
    {
    }
    /**
     *  Encodes the parameter array and returns it as a string
     *
     * @param array &$params the parameter array to encode
     *
     * @return string
     */
    function encodeParams(&$params)
    {
        if (is_array($params)) {
            $params = serialize($params);
            $params = base64_encode($params);
        }
        if (!is_string($params)) {
            $params = "";
        }
        return $params;
    }

    /**
     *  Decodes the parameter string and returns it as a array
     *
     * @param string &$params the parameter array to decode
     *
     * @return array
     */
    function decodeParams(&$params)
    {
        if (is_string($params)) {
            $params = base64_decode($params);
            $params = unserialize($params);
        }
        if (!is_array($params)) {
            $params = array();
        }
        return $params;
    }
    /**
    * Runs a function using the correct driver for the endpoint
    *
    * @return string
    */
    function getDriver()
    {
        $HWPart = &$this->data["HWPartNum"];
        $FWPart = &$this->data["FWPartNum"];
        $FWVer  = &$this->data["FWVersion"];
        if (isset($this->dev[$HWPart][$FWPart][$FWVer])) {
            return $this->dev[$HWPart][$FWPart][$FWVer];
        } else if (isset($this->dev[$HWPart][$FWPart]["BAD"])) {
            return "eDEFAULT";
        } else if (isset($this->dev[$HWPart][$FWPart]["DEFAULT"])) {
            return $this->dev[$HWPart][$FWPart]["DEFAULT"];
        } else if (isset($this->dev[$HWPart]["DEFAULT"]["DEFAULT"])) {
            return $this->dev[$HWPart]["DEFAULT"]["DEFAULT"];
        }
        return "eDEFAULT";
    }

}
?>
