<?php
/**
 * Main sensor driver.
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
 * @category   Sensors
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/HUGnetClass.php";
/**
 * Base class for sensors.
 *
 * @category   Sensors
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class SensorBase extends HUGnetClass
{

    /** @var int The maximum value for the AtoD convertor from */
    var $Am = 1023;
    /** @var int The Tf value */
    var $Tf = 65536;
    /** @var int The D value */
    var $D = 65536;
    /** @var int The s value */
    var $s = 64;
    /** @var float The Vcc value */
    var $Vcc = 5;
    /** @var array This defines all of the sensors that this driver deals with... */
    public $sensors = array();

    /**
     * Constructor.
     *
     * This just sets up the variables if they are passed to it.
     *
     * @param int $Tf  See {@link sensor_base::$Tf}
     * @param int $D   See {@link sensor_base::$D}
     * @param int $s   See {@link sensor_base::$s}
     * @param int $Am  See {@link sensor_base::$Am}
     * @param int $Vcc See {@link sensor_base::$Vcc}
     */
    function __construct($Tf=false, $D=false, $s=false, $Am=false, $Vcc=false)
    {
        if (is_numeric($Tf)) {
            $this->Tf = $Tf;
        }
        if (is_numeric($D)) {
            $this->D = $D;
        }
        if (is_numeric($Am)) {
            $this->Am = $Am;
        }
        if (is_numeric($Vcc)) {
            $this->Vcc = $Vcc;
        }
        if (is_numeric($s)) {
            $this->s = $s;
        }
    }

    /**
     * This function performs a linear calibration.
     *
     * See http://dev.hugllc.com/wiki/index.php/Project:Linear_Calibration for
     * more information.  This page also includes how the math was worked out
     * for this.
     *
     * @param mixed $val The value to modify
     * @param array $cal The calibrations to use
     *
     * @return mixed The calibrated value
     */
    function linearCalibration($val, $cal)
    {
        $deltaA = ($cal[0]["A"] - $cal[1]["A"]);
        $deltaC = ($cal[0]["C"] - $cal[1]["C"]);
        if (($deltaA == 0) || ($deltaC == 0)) {
            return $val;
        }
        $m = $deltaC / $deltaA;
        $b = $cal[1]["C"] - ($cal[1]["A"] * $m);
        return ($val * $m) + $b;
    }
}

?>
