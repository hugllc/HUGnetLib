<?php
/**
 * Sensor driver for light sensors.
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** Get the required base class */
require_once dirname(__FILE__)."/../../base/UnitsBase.php";
/**
* This class implements photo sensors.
*
* @category   Drivers
* @package    HUGnetLib
* @subpackage Units
* @author     Scott Price <prices@hugllc.com>
* @copyright  2007-2011 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
class DirectionUnits extends UnitsBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Direction",
        "Type" => "Units",
        "Class" => "DirectionUnits",
        "Flags" => array('Direction', '&#176;'),
    );
    /** @var The units of this point */
    public $to = "Direction";
    /** @var The type of this point */
    public $from = "&#176;";
    /** @var The units that are valid for conversion */
    protected $valid = array("&#176;", "Direction");

    /**
    * Does the actual conversion
    *
    * @param mixed  &$data The data to convert
    * @param string $to    The units to convert to
    * @param string $from  The units to convert from
    *
    * @return mixed The value returned
    */
    public function convert(&$data, $to=null, $from=null)
    {
        $ret = parent::convert($data, $to, $from);
        if (($this->from == '&#176;') && ($this->to == 'Direction')) {
            $data = $this->numDirtoDir($data);
        } else if (($this->from == 'Direction') && ($this->to == '&#176;')) {
            $data = $this->dirToNumDir($data);
        } else {
            return $ret;
        }
        return true;
    }

    /**
    * Converts from a numeric compass direction to a textual direction
    * abbreviation.
    *
    * So this converts 0 &#176; into 'N', 22.5 &#176; into 'NNE', etc.
    * This function is set up so that any number greater than the previous
    * number but less than or equal to the current number is taken for that
    * direction.  So if we got an input of 10 &#176; then it would return 'NNE'.
    *
    * If the number give is out of range (less than 0 or greater than 360)
    * 'N' is returned.
    *
    * @param float $ndir The numeric direction from 0 to 360 &#176;
    *
    * @return string The text direction
    *
    */
    protected function numDirtoDir($ndir)
    {
        if ($ndir <= 0) {
            return "N";
        }
        if ($ndir <= 22.5) {
            return "NNE";
        }
        if ($ndir <= 45) {
            return "NE";
        }
        if ($ndir <= 67.5) {
            return "ENE";
        }
        if ($ndir <= 90) {
            return "E";
        }
        if ($ndir <= 112.5) {
            return "ESE";
        }
        if ($ndir <= 135) {
            return "SE";
        }
        if ($ndir <= 157.5) {
            return "SSE";
        }
        if ($ndir <= 180) {
            return "S";
        }
        if ($ndir <= 202.5) {
            return "SSW";
        }
        if ($ndir <= 225) {
            return "SW";
        }
        if ($ndir <= 247.5) {
            return "WSW";
        }
        if ($ndir <= 270) {
            return "W";
        }
        if ($ndir <= 292.5) {
            return "WNW";
        }
        if ($ndir <= 315) {
            return "NW";
        }
        if ($ndir <= 337.5) {
            return "NNW";
        }
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
    *
    * @return float The text direction from 0 to 360 &#176;
    *
    */
    protected function dirToNumDir($ndir)
    {
        $ndir = trim(strtoupper($ndir));
        if ($ndir == "N") {
            return 0;
        }
        if ($ndir == "NNE") {
            return 22.5;
        }
        if ($ndir == "NE") {
            return 45;
        }
        if ($ndir == "ENE") {
            return 67.5;
        }
        if ($ndir == "E") {
            return 90;
        }
        if ($ndir == "ESE") {
            return 112.5;
        }
        if ($ndir == "SE") {
            return 135;
        }
        if ($ndir == "SSE") {
            return 157.5;
        }
        if ($ndir == "S") {
            return 180;
        }
        if ($ndir == "SSW") {
            return 202.5;
        }
        if ($ndir == "SW") {
            return 225;
        }
        if ($ndir == "WSW") {
            return 247.5;
        }
        if ($ndir == "W") {
            return 270;
        }
        if ($ndir == "WNW") {
            return 292.5;
        }
        if ($ndir == "NW") {
            return 315;
        }
        if ($ndir == "NNW") {
            return 337.5;
        }
        return 0;
    }

}

?>
