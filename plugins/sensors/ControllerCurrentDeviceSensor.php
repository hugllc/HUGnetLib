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
require_once dirname(__FILE__)."/../../base/sensors/CurrentDeviceSensorBase.php";

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
class ControllerCurrentDeviceSensor extends CurrentDeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "ControllerCurrentDeviceSensor",
        "Type" => "sensor",
        "Class" => "ControllerCurrentDeviceSensor",
        "Flags" => array("50:Controller"),
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(0x50);
    /** @var object These are the valid values for type */
    protected $typeValues = array("Controller");
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Controller Board Current Sensor",
        "unitType" => "Current",
        "storageUnit" => 'mA',
        "extraText" => array(
            "R in kOhms",
            "Gain"
        ),
        "extraDefault" => array(.5, 7),
        "maxDecimals" => 4,
   );
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 0x50;
        $this->default["type"] = "Controller";
        parent::__construct($data, $device);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    *
    * @return mixed The value in whatever the units are in the sensor
    */
    function getReading($A, $deltaT = 0)
    {
        return $this->direct($A);
    }

    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>
