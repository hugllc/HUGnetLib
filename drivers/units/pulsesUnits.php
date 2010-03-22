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

if (!class_exists('PulsesUnits')) {
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
    */
    class PulsesUnits extends unitBase
    {
        /** @var This is to register the class */
        public static $registerPlugin = array(
            "Name" => "Pulses",
            "Type" => "units",
        );
        /**
        *  This is the array that defines all of our units and how to
        * display and use them.
        *  @var array
        *
        */
        var $units = array(
            'counts' => array(
                'longName' => 'Counts',
                'varType' => 'int',
                'convert' => array(
                    'RPM' => 'CnttoRPM',
                    'PPM' => 'CnttoRPM',
                ),
            ),
            'PPM' => array(
                'longName' => 'Pulses Per Minute',
                'mode' => 'diff',
                'varType' => 'float',
            ),
            'RPM' => array(
                'longName' => 'Revolutions Per Minute',
                'mode' => 'diff',
                'varType' => 'float',
            ),
        );

        /**
        * Change counts into revolutions per minute
        *
        * @param int    $cnt       The number of counts
        * @param int    $time      The time in seconds between this record
        *                          and the last.
        * @param string $type      The type of data (diff, raw, etc)
        * @param int    $cntPerRev the number of counts per revolution
        *
        * @return float null if not differential data, the RPM otherwise
        *
        */
        public function cntToRPM ($cnt, $time, $type, $cntPerRev)
        {
            if ($cntPerRev <= 0) {
                $cntPerRev = 1;
            }
            if ($type == 'diff') {
                $rpm = ($cnt/$time/$cntPerRev)*60;
                return($rpm);
            } else {
                return(null);
            }
        }


    }
}



?>
