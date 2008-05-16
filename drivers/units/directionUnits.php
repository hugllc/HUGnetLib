<?php
/**
 * Sensor driver for light sensors.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** Get the required base class */
require_once dirname(__FILE__)."/../../base/UnitBase.php"; 
 
if (!class_exists('directionUnits')) {
    /**
     * This class implements photo sensors.
     *
     * @category   Drivers
     * @package    HUGnetLib
     * @subpackage Units
     * @author     Scott Price <prices@hugllc.com>
     * @copyright  2007 Hunt Utilities Group, LLC
     * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
     * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
     */
    class directionUnits extends unitBase
    {
        /**
         *  This is the array that defines all of our units and how to
         * display and use them.
         *  @var array
         *
         */
        var $units = array(
            '&#176;' => array(
                'longName' => 'Compass Degrees',
                'varType' => 'float',
                'mode' => 'raw',        
                'convert' => array(
                    'Direction' => 'numDirtoDir',
                ),
            ),
            'Direction' => array(
                'longName' => 'Direction',
                'varType' => 'text',
                'mode' => 'raw',
                'convert' => array(
                    '&#176;' => 'DirtonumDir',
                ),
            ),  
        );
        /**
         * Converts from a numeric compass direction to a textual direction abbreviation.
         *
         * So this converts 0 &#176; into 'N', 22.5 &#176; into 'NNE', etc.
         * This function is set up so that any number greater than the previous number
         * but less than or equal to the current number is taken for that direction.  So
         * if we got an input of 10 &#176; then it would return 'NNE'.
         *
         * If the number give is out of range (less than 0 or greater than 360) 'N' is
         * returned.
         *
         * @param float  $ndir The numeric direction from 0 to 360 &#176;
         * @param int    $time The time in seconds between this record and the last.
         * @param string $type The type of data (diff, raw, etc)
         *
         * @return string The text direction
         *
         */
        public function numDirtoDir($ndir, $time, $type) 
        {
            if ($ndir <= 0) return "N";
            if ($ndir <= 22.5) return "NNE";
            if ($ndir <= 45) return "NE";
            if ($ndir <= 67.5) return "ENE";
            if ($ndir <= 90) return "E";
            if ($ndir <= 112.5) return "ESE";
            if ($ndir <= 135) return "SE";
            if ($ndir <= 157.5) return "SSE";
            if ($ndir <= 180) return "S";
            if ($ndir <= 202.5) return "SSW";
            if ($ndir <= 225) return "SW";
            if ($ndir <= 247.5) return "WSW";
            if ($ndir <= 270) return "W";
            if ($ndir <= 292.5) return "WNW";
            if ($ndir <= 315) return "NW";
            if ($ndir <= 337.5) return "NNW";
            return "N";
        }
    
        /**
         * Converts from a textual direction abbreviation to a numberic 
         * compass direction.
         *
         * So this converts 'N' into 0 &#176;, 'NNE' into 22.5 &#176;, etc.
         *   
         * This function returns 0 if it gets an abbreviation that it does not 
         * understand.
         *
         * @param string $ndir The text direction
         * @param int    $time The time in seconds between this record and the last.
         * @param string $type The type of data (diff, raw, etc)
         *
         * @return float The text direction from 0 to 360 &#176;
         *
         */
        public function dirToNumDir($ndir, $time, $type) 
        {
            $ndir = trim(strtoupper($ndir));
            if ($ndir == "N") return 0;
            if ($ndir == "NNE") return 22.5;
            if ($ndir == "NE") return 45;
            if ($ndir == "ENE") return 67.5;
            if ($ndir == "E") return 90;
            if ($ndir == "ESE") return 112.5;
            if ($ndir == "SE") return 135;
            if ($ndir == "SSE") return 157.5;
            if ($ndir == "S") return 180;
            if ($ndir == "SSW") return 202.5;
            if ($ndir == "SW") return 225;
            if ($ndir == "WSW") return 247.5;
            if ($ndir == "W") return 270;
            if ($ndir == "WNW") return 292.5;
            if ($ndir == "NW") return 315;
            if ($ndir == "NNW") return 337.5;
            return 0;
        }
        
    }
}

if (method_exists($this, "addGeneric")) {
    $this->addGeneric(array("Name" => "Direction", "Type" => "units", "Class" => "directionUnits"));
}



?>
