<?php
/**
 * Sensor driver for light sensors.
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
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: insulationUnits.php 1353 2008-05-16 22:03:16Z prices $
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** Get the required base class */
require_once dirname(__FILE__)."/../../base/UnitBase.php";
/**
* This class implements photo sensors.
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Units
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2010 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
* @deprecated since version 0.9.0
*/
class HeatInsulationUnits extends unitBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Heat Insulation",
        "Type" => "units",
    );
    /**
    *  This is the array that defines all of our units and how to
    * display and use them.
    *  @var array
    *
    */
    var $units = array(
        'm^2 C / W' => array(
            'longName' => 'm^2 C / W',
            'varType' => 'float',
            'convert' => array(
                'ft^2 F h / Btu' => 'metricToEng',
            ),
        ),
        'ft^2 F h / Btu' => array(
            'longName' => 'ft^2 F h / Btu',
            'varType' => 'float',
            'convert' => array(
                'm^2 C / W' => 'engToMetric',
            ),
        ),

    );

    /**
    * Converts
    *
    * @param float  $e    The insulation in ft^2 F h/Btu
    * @param int    $time The time in seconds between this record and the last.
    * @param string $type The type of data (diff, raw, etc)
    *
    * @return float The insulation in F
    */
    public function engToMetric($e, $time, $type)
    {
        return round($e / 5.678, 4);
    }

    /**
    *  Converts from &#176; F to &#176; C.
    *
    * @param float  $m    The insulation in m^2 C/ W
    * @param int    $time The time in seconds between this record and the last.
    * @param string $type The type of data (diff, raw, etc)
    *
    * @return float The insulation in C
    */
    public function metricToEng($m, $time, $type)
    {
        return round($m * 5.678, 4);
    }

    /**
    *  Converts from &#176; F to &#176; C.
    *
    * @param float  $r    The insulation in m^2 C/ W
    * @param int    $time The time in seconds between this record and the last.
    * @param string $type The type of data (diff, raw, etc)
    *
    * @return float The insulation in C
    */
    public function rToU($r, $time, $type)
    {
        if ($r == 0) {
            return null;
        }
        return round(1/$r, 4);
    }

}


?>
