<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensorss
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\avr;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../DriverAVR.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class AVRPotDirection extends \HUGnet\devices\inputTable\DriverAVR
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is the array of sensor information.
    */
    protected $params = array(
        "longName" => "POT Direction Sensor",
        "shortName" => "POTDir",
        "unitType" => "Direction",
        "storageUnit" => '&#176;',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "POT Resistance in kOhms",
            "Direction 1 (&#176;)",
            "Resistance 1 (kOhms)",
            "Direction 2 (&#176;)",
            "Resistance 2 (kOhms)",
        ),
        "extraDesc" => array(
            "The total resistance of the POT",
            "The direction the thing is pointed when the resistace is at
             Resistance 1",
            "The resistance that is read when the thing is pointed in Direction 1",
            "The direction the thing is pointed when the resistace is at
             Resistance 2",
            "The resistance that is read when the thing is pointed in Direction 2",
        ),
        "extraNames" => array(
            "potr" => 0,
            "dir1" => 1,
            "r1"   => 2,
            "dir2" => 3,
            "r2"   => 4,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(7, 4, 7, 4, 7),
        "extraDefault" => array(25,0, 0, 180, 25),
        "maxDecimals" => 1,
        "requires" => array("AI"),
        "provides" => array("DC"),
    );
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        bcscale(10);
        $RTotal = $this->getExtra(0);
        $dir1   = $this->getExtra(1);
        $R1     = $this->getExtra(2);
        $dir2   = $this->getExtra(3);
        $R2     = $this->getExtra(4);
        $R      = $this->getSweep($A, $RTotal, $data["timeConstant"]);

        $dir = $this->linearUnbounded($R, $R1, $R2, $dir1, $dir2);
        if (is_null($R) || ($RTotal == 0) || is_null($dir)) {
            return null;
        }
        while ($dir > 360) {
            $dir -= 360;
        }
        while ($dir < 0) {
            $dir += 360;
        }
        return round($dir, $this->get("maxDecimals"));
    }
    /**
    * Returns the reversed reading
    *
    * @param array $value   The data to use
    * @param int   $channel The channel to get
    * @param float $deltaT  The time delta in seconds between this record
    * @param array &$prev   The previous reading
    * @param array &$data   The data from the other sensors that were crunched
    *
    * @return string The reading as it would have come out of the endpoint
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getRaw(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        $RTotal = $this->getExtra(0);
        $dir1   = $this->getExtra(1);
        $R1     = $this->getExtra(2);
        $dir2   = $this->getExtra(3);
        $R2     = $this->getExtra(4);
        $R      = $this->linearUnbounded($value, $dir1, $dir2, $R1, $R2);
        $A      = $this->revSweep($R, $RTotal, $data["timeConstant"]);

        return (int)round($A);
    }

}
?>
