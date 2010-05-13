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
 * @version    SVN: $Id$
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
class TemperatureUnits extends unitBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Temperature",
        "Type" => "units",
    );
    /**
    *  This is the array that defines all of our units and how to
    * display and use them.
    *  @var array
    *
    */
    var $units = array(
        '&#176;C' => array(
            'longName' => '&#176;C',
            'varType' => 'float',
            'convert' => array(
                '&#176;F' => 'CtoF',
            ),
            'preferred' => '&#176;F',
        ),
        '&#176;F' => array(
            'longName' => '&#176;F',
            'varType' => 'float',
            'convert' => array(
                '&#176;C' => 'FtoC',
            ),
        ),

    );

    /**
    * Converts from &#176; C to &#176; F.
    *
    * If the temperature is differential we can't add 32 like we would
    * for an absolute temperature.  This is because it is already factored
    * out by the subtraction in the difference.
    *
    * @param float  $c    The temperature in C
    * @param int    $time The time in seconds between this record and the last.
    * @param string $type The type of data (diff, raw, etc)
    *
    * @return float The temperature in F
    */
    public function cToF($c, $time, $type)
    {
        $F = ((9*$c)/5);
        if ($type != 'diff') {
            $F += 32;
        }
        return($F);
    }

    /**
    *  Converts from &#176; F to &#176; C.
    *
    * If the temperature is differential we can't subtract 32 like we would
    * for an absolute temperature.  This is because it is already factored
    * out by the subtraction in the difference.
    *
    * @param float  $f    The temperature in F
    * @param int    $time The time in seconds between this record and the last.
    * @param string $type The type of data (diff, raw, etc)
    *
    * @return float The temperature in C
    */
    public function fToC($f, $time, $type)
    {
        if ($type != 'diff') {
            $f -= 32;
        }
        return((5/9)*($f));
    }

}

?>
