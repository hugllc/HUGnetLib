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
require_once dirname(__FILE__)."/../../base/sensors/ResistiveDeviceSensorBase.php";

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
 */
class PotDirectionDeviceSensor extends ResistiveDeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "PotDirectionDeviceSensor",
        "Type" => "sensor",
        "Class" => "PotDirectionDeviceSensor",
        "Flags" => array("02:potDirection"),
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(2);
    /** @var object These are the valid values for type */
    protected $typeValues = array("potDirection");

    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "POT Direction Sensor",
        "unitType" => "Direction",
        "storageUnit" => '&#176;',
        "storageType" => UnitsBase::TYPE_RAW,  // This is the dataType as stored
        "extraText" => array(
            "POT Resistance in kOhms",
            "Direction 1 in degrees",
            "Resistance 1 in kOhms",
            "Direction 2 in degrees",
            "Resistance 2 in kOhms",
        ),
        "extraDefault" => array(25,0, 0, 180, 25),
    );
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 0x02;
        $this->default["type"] = "potDirection";
        parent::__construct($data, $device);
        // This takes care of The older sensors with the 100k bias resistor
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
        if ($this->dataType == DeviceSensorBase::TYPE_IGNORE) {
            return null;
        }
        $RTotal = $this->getExtra(0);
        $dir1   = $this->getExtra(1);
        $R1     = $this->getExtra(2);
        $dir2   = $this->getExtra(3);
        $R2     = $this->getExtra(4);
        $R      = $this->getSweep($A, $RTotal);

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
        return round($dir, 4);
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>
