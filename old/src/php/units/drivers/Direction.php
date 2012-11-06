<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\units\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Direction extends \HUGnet\units\Driver
{
    /** @var The units that are valid for conversion */
    protected $valid = array('Direction', '&#176;');
    /** @var These are the direction names */
    private $_directions = array(
        "N" => 0,
        "NNE" => 22.5,
        "NE" => 45,
        "ENE" => 67.5,
        "E" => 90,
        "ESE" => 112.5,
        "SE" => 135,
        "SSE" => 157.5,
        "S" => 180,
        "SSW" => 202.5,
        "SW" => 225,
        "WSW" => 247.5,
        "W" => 270,
        "WNW" => 292.5,
        "NW" => 315,
        "NNW" => 337.5,
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
    * Does the actual conversion
    *
    * @param mixed  &$data The data to convert
    * @param string $to    The units to convert to
    * @param string $from  The units to convert from
    * @param string $type  The data type to convert
    *
    * @return mixed The value returned
    */
    public function convert(&$data, $to, $from, $type)
    {
        if (($from == '&#176;') && ($to == 'Direction')) {
            $data = $this->_numDirtoDir($data);
        } else if (($from == 'Direction') && ($to == '&#176;')) {
            $data = $this->_dirToNumDir($data);
        } else {
            return parent::convert($data, $to, $from, $type);
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
    private function _numDirtoDir($ndir)
    {
        foreach ($this->_directions as $name => $val) {
            if ($ndir <= $val) {
                return $name;
            }
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
    private function _dirToNumDir($ndir)
    {
        $ndir = trim(strtoupper($ndir));
        if (isset($this->_directions[$ndir])) {
            return $this->_directions[$ndir];
        }
        return 0;
    }
    /**
    * Checks to see if value the units represent is numeric
    *
    * @param string $units The units to check
    *
    * @return bool True if they are numeric, false otherwise
    */
    public function numeric($units)
    {
        // Degrees are numeric.  Nothing else here is.
        if ($units === "&#176;") {
            return true;
        }
        return false;
    }
}
?>
