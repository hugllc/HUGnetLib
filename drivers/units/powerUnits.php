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
 
if (!class_exists('powerUnits')) {
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
    class powerUnits extends unitBase
    {
        /**
         *  This is the array that defines all of our units and how to
         * display and use them.
         *  @var array
         *
         */
        var $units = array(
            'kWh' => array(
                'longName' => 'Kilowatt Hours',
                'varType' => 'float',
                'convert' => array(
                    'Wh' => 'shift:-3',
                    'kW' => 'kWhTokW',
                    'W' => 'kWhToW',
                ),
            ),
            'kW' => array(
                'longName' => 'Kilowatts',
                'mode' => 'diff',
                'varType' => 'float',
                'convert' => array(
                    'W' => 'shift:-3',
                ),
            ),
            'W' => array(
                'longName' => 'Watts',
                'mode' => 'diff',
                'varType' => 'float',
                'convert' => array(
                    'kW' => 'shift:3', 
                ),
            ),
            'Wh' => array(
                'longName' => 'Watt Hours',
                'varType' => 'float',
                'convert' => array(
                    'kWh' => 'shift:3',
                    'W' => 'kWhTokW',
                ),
            ),
        );
        
        /**
         *  This function changes kWh into kW
         *
         *  It does this by dividing the delta time out of it.  I am
         *  not sure if this is a valid way to do it.
         *
         * @param float  $val   The input value
         * @param int    $time  The time in seconds between this record and the last.
         * @param string $type  The type of data (diff, raw, etc)
         * @param mixed  $extra The extra information from the sensor.
         *
         * @return float The kW value
         */
        public function kWhTokW ($val, $time, $type, $extra) 
        {
            if (empty($time)) return null;
            if ($type != "diff") return null;
            return ($val / (abs($time) / 3600));
        }
    
        /**
         *  This function changes kWh into W
         *
         * @param float  $val   The input value
         * @param int    $time  The time in seconds between this record and the last.
         * @param string $type  The type of data (diff, raw, etc)
         * @param mixed  $extra The extra information from the sensor.
         *
         * @return float The W value
         *
         * @uses unitConversion::kWhTokW()
         */
        public function kWhToW ($val, $time, $type, $extra) 
        {
            $val = self::kWhTokW($val, $time, $type, $extra);
            if (is_null($val)) return $val;
            return $val * 1000;
        }
        
    }
}

if (method_exists($this, "addGeneric")) {
    $this->addGeneric(array("Name" => "Power", "Type" => "units", "Class" => "powerUnits"));
}



?>
