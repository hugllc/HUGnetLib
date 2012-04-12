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

/**
 * Driver for reading voltage based pressure sensors
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
class ADuCPressure extends \HUGnet\sensors\Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected static $params = array(
        "longName" => "ADuC Pressure Sensor",
        "shortName" => "ADuCPressure",
        "unitType" => "Pressure",
        "storageUnit" => 'psi',
        "storageType" => \HUGnet\units\Driver::TYPE_RAW, // Storage dataType
        "extraText" => array(
            "Min Voltage (V)",
            "Max Voltage (V)",
            "Read @ Min Voltage (psi)",
            "Read @ Max Voltage (psi)",
            "Voltage Ref (V)",
            "R input (Ohms)",
            "R bias  (Ohms)"
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, 5, 7, 7, 10, 10, 10),
        "extraDefault" => array(0, 5, 0, 100, 1.2, 100, 1),
        "maxDecimals" => 4,
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
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    * @param array $sensor The sensor information
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null, $sensor = array()
    ) {
        bcscale(10);
        $Am   = pow(2, 23);
        $Vmin  = $this->getExtra(0, $sensor);
        $Vmax  = $this->getExtra(1, $sensor);
        $Pmin  = $this->getExtra(2, $sensor);
        $Pmax  = $this->getExtra(3, $sensor);
        $Vref  = $this->getExtra(4, $sensor);
        $Rin   = $this->getExtra(5, $sensor);
        $Rbias = $this->getExtra(6, $sensor);

        $A = \HUGnet\Util::getTwosCompliment($A, 32);
        $A = \HUGnet\Util::inputBiasCompensation($A, $Rin, $Rbias);

        $Va = ($A / $Am) * $Vref;
        $P = \HUGnet\Util::linearBounded($Va, $Vmin, $Vmax, $Pmin, $Pmax);
        return round($P, $this->get("maxDecimals", 1));

    }

}


?>
