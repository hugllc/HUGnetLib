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
require_once dirname(__FILE__)."/../../base/DeviceSensorBase.php";

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
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class ResistiveDeviceSensorBase extends DeviceSensorBase
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "location" => "",                // The location of the sensors
        "id" => null,                    // The id of the sensor.  This is the value
                                         // Stored in the device  It will be an int
        "type" => "",                    // The type of the sensors
        "dataType" => UnitsBase::TYPE_RAW,       // The datatype of each sensor
        "extra" => array(),              // Extra input for crunching numbers
        "units" => "",                   // The units to put the data into by default
        "bound" => false,                // This says if this sensor is changeable
        "rawCalibration" => "",          // The raw calibration string
        "timeConstant" => 1,             // The time constant
        "Am" => 1023,                    // The maximum value for the AtoD convertor
        "Tf" => 65536,                   // The Tf value
        "D" => 65536,                    // The D value
        "s" => 64,                       // The s value
        "Vcc" => 5,                      // The Vcc value
        "filter" => array(),             // Information on the output filter
    );
    /** @var object These are the valid values for unitType */
    protected $unitTypeValues = array();
    /** @var object These are the valid values for units */
    protected $unitsValues = array();
    /** @var object These are the valid values for type */
    protected $typeValues = array();

    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        parent::__construct($data, $device);
    }

    /**
    * Converts a raw AtoD reading into resistance
    *
    * This function takes in the AtoD value and returns the calculated
    * resistance of the sensor.  It does this using a fairly complex
    * formula.  This formula and how it was derived is detailed in
    *
    * @param int   $A    Integer The AtoD reading
    * @param float $Bias Float The bias resistance in kOhms
    *
    * @return The resistance corresponding to the values given in k Ohms
    */
    protected function getResistance($A, $Bias)
    {
        if ($this->D == 0) {
            return 0.0;
        }
        $Den = ((($this->Am*$this->s*$this->timeConstant*$this->Tf)/$this->D) - $A);
        if (($Den == 0) || !is_numeric($Den)) {
            $Den = 1.0;
        }
        $R = (float)($A*$Bias)/$Den;
        return round($R, 4);
    }

    /**
    * Converts a raw AtoD reading into resistance
    *
    * If you connect the two ends of a pot up to Vcc and ground, and connect the
    * sweep terminal to the AtoD converter, this function returns the
    * resistance between ground and the sweep terminal.
    *
    * This function takes in the AtoD value and returns the calculated
    * resistance that the sweep is at.  It does this using a fairly complex
    * formula.  This formula and how it was derived is detailed in
    *
    * @param int   $A Integer The AtoD reading
    * @param float $R Float The overall resistance in kOhms
    *
    * @return The resistance corresponding to the values given in k Ohms
    */
    protected function getSweep($A, $R)
    {
        if ($this->D == 0) {
            return 0.0;
        }
        $Den = (($this->Am*$this->s*$this->timeConstant*$this->Tf)/$this->D);
        if (($Den == 0) || !is_numeric($Den)) {
            $Den = 1.0;
        }
        $Rs = (float)(($A*$R)/$Den);
        if ($Rs > $R) {
            return round($R, 4);
        }
        if ($Rs < 0) {
            return 0.0;
        }
        return round($Rs, 4);
    }
    /**
    * This function should be called with the values set for the specific
    * thermistor that is used.
    *
    * @param float $R The current resistance of the thermistor in k ohms
    *
    * @return float The Temperature in degrees C
    */
    protected function tableInterpolate($R)
    {
        $R *= 1000;
        $max = max(array_keys($this->valueTable));
        $min = min(array_keys($this->valueTable));
        if (($R < $min) || ($R > $max)) {
            return null;
        }
        $table = &$this->valueTable;
        foreach (array_keys($table) as $ohm) {
            $ohm  = $ohm;
            $last = $ohm;
            if ((float)$ohm < $R) {
                break;
            }
            $next = $ohm;
        }
        $T     = $table[$last];
        $fract = ($next - $R) / ($next - $last);
        $T    += $fract;
        return $T;
    }


}
?>
