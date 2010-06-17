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
require_once dirname(__FILE__)."/../../../../base/DeviceSensorBase.php";
// Need to make sure this file is not added to the code coverage
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

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
class Test2Sensor extends DeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Test2Sensor",
        "Type" => "sensor",
        "Class" => "Test2Sensor",
        "Flags" => array("02","03:Hello","04","05"),
    );
    /** @var object This is where we store our configuration */
    protected $typeValues = array("a", "c", "e", "Test2Sensor", "Hello");
    /** @var object This is where we store our configuration */
    protected $idValues = array(2, 3, 4, 5);
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Unknown Sensor",
        "unitType" => "secondUnit",
        "units" => 'anotherUnit',
        "extraText" => array(),
        "extraDefault" => array(),
    );

    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        parent::__construct($data, $device);
        if ($this->id == 3) {
            $this->data["type"] = "Hello";
        }
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    *
    * @return float The direction in degrees
    */
    function getReading($A, $deltaT = 0)
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
