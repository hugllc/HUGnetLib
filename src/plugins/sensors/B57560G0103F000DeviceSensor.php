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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsSensorss
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/sensors/ResistiveDeviceSensorBase.php";

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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class B57560G0103F000DeviceSensor extends ResistiveDeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "EPCOS B57560G0103F000 Thermistor",
        "Type" => "sensor",
        "Class" => "B57560G0103F000DeviceSensor",
        "Flags" => array("02:B57560G0103F000"),
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(2);
    /** @var object These are the valid values for type */
    protected $typeValues = array("B57560G0103F000");

    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "EPCOS B57560G0103F000",
        "unitType" => "Temperature",
        "storageUnit" => '&#176;C',
        "storageType" => UnitsBase::TYPE_RAW,  // This is the dataType as stored
        "extraText" => array("Bias Resistor (kOhms)"),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5),
        "extraDefault" => array(10),
        "maxDecimals" => 2,
    );
    /** @var array The table for IMC Sensors */
    protected $valueTable = array(
        "519910" => -55, "379890" => -50, "280700" => -45,
        "209600" => -40, "158090" => -35, "120370" => -30,
        "92484" => -25, "71668" => -20, "55993" => -15,
        "44087" => -10, "34971" => -5, "27936" => 0,
        "22468" => 5, "18187" => 10, "14813" => 15,
        "12136" => 20, "10000" => 25, "8284" => 30,
        "6899" => 35, "5774" => 40, "4856" => 45,
        "4103" => 50, "3482" => 55, "2967" => 60,
        "2539" => 65, "2182" => 70, "1882" => 75,
        "1629" => 80, "1415" => 85, "1234" => 90,
        "1079" => 95, "946.6" => 100, "833.1" => 105,
        "735.5" => 110, "651.1" => 115, "578.1" => 120,
        "514.6" => 125, "459.4" => 130, "411.1" => 135,
        "368.8" => 140, "331.6" => 145, "298.9" => 150,
        "270.0" => 155, "244.4" => 160, "221.7" => 165,
        "201.6" => 170, "183.6" => 175, "167.6" => 180,
        "153.3" => 185, "140.4" => 190, "128.9" => 195,
        "118.5" => 200, "109.1" => 205, "100.7" => 210,
        "93.01" => 215, "86.08" => 220, "79.78" => 225,
        "74.05" => 230, "68.83" => 235, "64.08" => 240,
        "59.73" => 245, "55.75" => 250, "52.11" => 255,
        "48.76" => 260, "45.69" => 265, "42.87" => 270,
        "40.26" => 275, "37.86" => 280, "35.64" => 285,
        "33.59" => 290, "31.70" => 295, "29.94" => 300,
    );
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 2;
        $this->default["type"] = "B57560G0103F000";
        parent::__construct($data, $device);
        // This takes care of The older sensors with the 100k bias resistor
    }
    /**
    * Changes a raw reading into a output value
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
        $Bias = $this->getExtra(0);
        $ohms = $this->getResistance($A, $Bias);
        $T    = $this->tableInterpolate($ohms);
        if (is_null($T)) {
            return null;
        }
        // tableInterpolate forces the result to be in range, or returns null
        $T = round($T, 4);
        return $T;
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>
