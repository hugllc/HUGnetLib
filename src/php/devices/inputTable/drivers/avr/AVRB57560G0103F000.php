<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\avr;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../DriverAVR.php";

/**
 * Default sensor driver
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.9
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class AVRB57560G0103F000 extends \HUGnet\devices\inputTable\DriverAVR
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "EPCOS B57560G0103F000",
        "shortName" => "B57560G0103F000",
        "unitType" => "Temperature",
        "storageUnit" => '&#176;C',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array("Bias Resistor (kOhms)"),
        "extraDesc" => array(
            "The resistor connecting the thermistor and the AtoD reference voltage.",
        ),
        "extraNames" => array(
        ),
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
    protected function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        $Bias = $this->getExtra(0);
        $kohms = $this->getResistance($A, $Bias, $data["timeConstant"]);
        $T    = $this->tableInterpolate($kohms * 1000, $this->valueTable);
        if (is_null($T)) {
            return null;
        }
        // tableInterpolate forces the result to be in range, or returns null
        $T = round($T, $this->get("maxDecimals"));
        return $T;
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
        $Bias  = $this->getExtra(0);
        $table = array_reverse(array_flip($this->valueTable), true);
        $Kohms = $this->tableInterpolate($value, $table) / 1000;
        $A     = $this->revResistance($Kohms, $Bias, $data["timeConstant"]);
        if (is_null($A)) {
            return null;
        }
        return (int)round($A);
    }
}


?>
