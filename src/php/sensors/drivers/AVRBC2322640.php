<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\sensors\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../DriverAVR.php";

/**
 * Default sensor driver
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class AVRBC2322640 extends \HUGnet\sensors\DriverAVR
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected static $params = array(
        "longName" => "BC Components Thermistor #2322640",
        "shortName" => "AVRBC2322640",
        "unitType" => "Temperature",
        "storageUnit" => '&#176;C',
        "storageType" => \HUGnet\units\Driver::TYPE_RAW,  // Data stored as
        "extraText" => array(
            "Bias Resistor (kOhms)",
            "Value @25&#176;C (kOhms)"
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, 5),
        "extraDefault" => array(10, 10),
        "maxDecimals" => 2,
    );
    /**
    * This function creates the system.
    *
    * @return null
    */
    public static function &factory()
    {
        return parent::intFactory();
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A       Output of the A to D converter
    * @param array &$sensor The sensor information
    * @param float $deltaT  The time delta in seconds between this record
    * @param array &$data   The data from the other sensors that were crunched
    * @param mixed $prev    The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getReading(
        $A, &$sensor, $deltaT = 0, &$data = array(), $prev = null
    ) {
        $Bias      = $this->getExtra(0, $sensor);
        $baseTherm = $this->getExtra(1, $sensor);
        $Am = 1023;
        $s = 64;
        $Tf = 65535;
        $D = 65535;
        $Tc = $data["timeConstant"];
        $Den = ((($Am*$s*$Tc*$Tf)/$D) - $A);
        if (($Den == 0) || !is_numeric($Den)) {
            $Den = 1.0;
        }
        $ohms = (float)($A*$Bias)/$Den;
        $T         = $this->_BcTherm2322640Interpolate(
            $ohms,
            $baseTherm,
            3.354016e-3,
            2.569355e-4,
            2.626311e-6,
            0.675278e-7
        );

        if (is_null($T)) {
            return null;
        }
        if ($T > 150) {
            return null;
        }
        if ($T < -40) {
            return null;
        }
        $T = round($T, 4);
        return $T;
    }

    /**
    * This formula is from BCcomponents PDF file for the
    * # 2322 640 thermistor series on page 6.  See the data sheet for
    * more information.
    *
    * This function should be called with the values set for the specific
    * thermistor that is used.  See eDEFAULT::Therm0Interpolate as an example.
    *
    * @param float $R  The current resistance of the thermistor in kOhms
    * @param float $R0 The resistance of the thermistor at 25C in kOhms
    * @param float $A  Thermistor Constant A (From datasheet)
    * @param float $B  Thermistor Constant B (From datasheet)
    * @param float $C  Thermistor Constant C (From datasheet)
    * @param float $D  Thermistor Constant D (From datasheet)
    *
    * @return float The Temperature in degrees C
    */
    private function _bcTherm2322640Interpolate($R, $R0, $A, $B, $C, $D)
    {
        // This gets out bad values
        if ($R <= 0) {
            return null;
        }
        if ($R0 == 0) {
            return null;
        }
        $T  = $A;
        $T += $B * log($R/$R0);
        $T += $C * pow(log($R/$R0), 2);
        $T += $D * pow(log($R/$R0), 3);
        $T  = pow($T, -1);

        $T -= 273.15;
        return($T);
    }

}


?>
