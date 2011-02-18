<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../../../base/VirtualSensorBase.php";
// Need to make sure this file is not added to the code coverage
PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class TestVirtualSensor extends VirtualSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "TestVirtualSensor",
        "Type" => "sensor",
        "Class" => "TestVirtualSensor",
        "Flags" => array("FE"),
    );
    /** @var object This is where we store our configuration */
    protected $typeValues = array("a", "c", "e", "Test3Sensor", "Hello");
    /** @var object This is where we store our configuration */
    protected $idValues = array(0xFE);
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Virtual Sensor",
        "unitType" => "secondUnit",
        "storageUnit" => 'anotherUnit2',
        "storageType" => UnitsBase::TYPE_RAW,  // This is the dataType as stored
        "extraText" => array(),
        "extraDefault" => array(),
        "maxDecimals"=> 4,
    );

    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $data["type"] = "Virtual";
        parent::__construct($data, $device);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array $data   The data from the other sensors that were crunched
    *
    * @return mixed The value in whatever the units are in the sensor
    */
    function getVirtualReading($A, $deltaT = 0, $data = array())
    {
    }
    /**
    * function to set unitType
    *
    * @param mixed $value The value to set
    *
    * @return null
    */
    protected function setType($value)
    {
        $this->data["type"] = $value;
    }

}
?>
