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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\sensors\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * Networking for devices.
 *
 * This class will do all of the networking for devices.  It will poll, get configs,
 * update software, and anything else related to talking to devices.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class ADuCVoltage extends \HUGnet\sensors\Driver
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "ADuC Voltage Sensor",
        "shortName" => "ADuCVoltage",
        "unitType" => "Voltage",
        "storageUnit" => 'V',
        "storageType" => \UnitsBase::TYPE_RAW,  // This is the dataType as stored
        "extraText" => array(
            "R1 to Source (kOhms)",
            "R2 to Ground (kOhms)",
            "AtoD Ref Voltage"
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(5, 5, 5),
        "extraDefault" => array(100, 1, 1.2),
        "maxDecimals" => 8,
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
    * @param array $extra  The extra setup that is needed.
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null, $extra = array()
    ) {
        $Am    = pow(2, 23);
        $Rin   = $this->getExtra(0, $extra);
        $Rbias = $this->getExtra(1, $extra);
        $Vref  = $this->getExtra(2, $extra);

        $A = \HUGnet\Util::getTwosCompliment($A, 32);
        //$A = $this->inputBiasCompensation($A, $Rin, $Rbias);
        $Va = ($A / $Am) * $Vref;
        return round($Va, $this->get('maxDecimals'));
    }

}


?>
