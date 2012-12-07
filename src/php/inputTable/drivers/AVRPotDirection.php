<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensorss
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\inputTable\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../DriverAVR.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class AVRPotDirection extends \HUGnet\inputTable\DriverAVR
{
    /**
    * This is the array of sensor information.
    */
    protected $params = array(
        "longName" => "POT Direction Sensor",
        "shortName" => "POTDir",
        "unitType" => "Direction",
        "storageUnit" => '&#176;',
        "storageType" => \HUGnet\channels\Driver::TYPE_RAW,
        "extraText" => array(
            "POT Resistance in kOhms",
            "Direction 1 (&#176;)",
            "Resistance 1 (kOhms)",
            "Direction 2 (&#176;)",
            "Resistance 2 (kOhms)",
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(7, 4, 7, 4, 7),
        "extraDefault" => array(25,0, 0, 180, 25),
        "maxDecimals" => 1,
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
    public function getReading($A, $deltaT = 0, &$data = array(), $prev = null)
    {
        $RTotal = $this->getExtra(0);
        $dir1   = $this->getExtra(1);
        $R1     = $this->getExtra(2);
        $dir2   = $this->getExtra(3);
        $R2     = $this->getExtra(4);
        $R      = $this->getSweep($A, $RTotal, $data["timeConstant"]);

        if (is_null($R) || ($dir1 == $dir2) || ($R1 == $R2) || ($RTotal == 0)) {
            return null;
        }

        $m = ($dir1 - $dir2) / ($R1 - $R2);
        $b = $dir2 - ($m * $R2);
        $dir = ($m * $R) + $b;

        while ($dir > 360) {
            $dir -= 360;
        }
        while ($dir < 0) {
            $dir += 360;
        }
        return round($dir, $this->get("maxDecimals"));
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>
