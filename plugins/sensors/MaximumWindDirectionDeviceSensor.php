<?php
/**
 * Sensor driver for wind direction sensors
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
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/DeviceSensorBase.php";
/**
* This class deals with wind direction sensors.
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Sensors
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2010 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class MaximumWindDirectionDeviceSensor extends DeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "MaximumWindDirectionDeviceSensor",
        "Type" => "sensor",
        "Class" => "MaximumWindDirectionDeviceSensor",
        "Flags" => array("6F", "6F:maximum-inc"),
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(0x6F);
    /** @var object These are the valid values for type */
    protected $typeValues = array("maximum-inc");
    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "Maximum Inc Wind Direction Sensor",
        "unitType" => "Direction",
        "storageUnit" => '&#176',
        "extraText" => array(
        ),
        "extraDefault" => array(),
        "inputSize" => 5,
    );
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 0x6F;
        $this->default["type"] = "maximum-inc";
        parent::__construct($data, $device);
    }

    /**
    * Returns a numeric direction in degrees from the numeric bit
    * field that is returned by the Maximum Inc wind direction sensor.
    *
    * In this bit field the even bits are the cardinal directions and
    * the odd bits are the ordinal directions.  Only one bit in each
    * set can be set, and if there is a bit set in each they have to be
    * 45 degrees apart (0 sometimes counts as 360 because degrees are
    * circular).
    *
    * - If the ordinal direction is null it returns the cardinal direction.
    * - If the cardinal direction is null it returns the ordinal direction.
    * - Otherwise it retuns the average of the two.  This is only valid where
    *   the difference between the two is 45, so it checks this first.
    * - One special case is when the cardinal direction is north (0) and
    *   the ordinal direction is NW (315).  In this case the cardinal
    *   direction needs to be changed to 360 for the averaging to work
    *   properly.
    *
    * @param int   $bitField This is an 8 bit bit field returned by the sensor
    * @param float $deltaT   The difference in time between records
    *
    * @return float
    */
    function getReading($bitField, $deltaT = 0)
    {

        // Do the cardinal directions
        $cDirections = array(0 => 0.0, 2 => 90.0, 4 => 180.0, 6 => 270.0);
        $cDir        = null;
        $oDir        = null;
        foreach ($cDirections as $shift => $dir) {
            // Do the cardinal direction
            if ($bitField & (1<<$shift)) {
                if (!is_null($cDir)) {
                    return null;  // Can't have two cardinal directions!
                }
                $cDir = $dir;
            }
            // Do the ordinal direction that is +45deg from the cardinal
            if ($bitField & (1<<($shift+1))) {
                if (!is_null($oDir)) {
                    return null;  // Can't have two ordinal directions!
                }
                $oDir = $dir + 45.0;
            }
        }
        // If $oDir is null we are at a cardinal direction
        if (is_null($oDir)) {
            return $cDir;
        }
        // If $cDir is null we are at an ordinal direction
        if (is_null($cDir)) {
            return $oDir;
        }

        // Now we have to check the in between directions.
        // One special case first.  (see notes in docblock
        if (($cDir == 0) && ($oDir == 315)) {
            $cDir = 360;
        }
        // If the difference is not 45 it is a bad reading
        if (abs($cDir - $oDir) != 45) {
            return null;
        }
        // Return the average of the two directions.  This gives
        // us the inbetween readings.
        return ($cDir + $oDir)/2;
    }

}

?>
