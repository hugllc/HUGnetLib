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
 * This class represents pressure in the HUGnet system.
 *
 * Information on conversion factors was found at:
 * http://en.wikipedia.org/wiki/Pressure
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
class Pressure extends \HUGnet\units\Driver
{
    /** @var The units that are valid for conversion */
    protected $valid = array("Pa", "bar", "at", "atm", "Torr", "psi");
    /** @var Unit conversion multipliers */
    protected $multiplier = array(
        "Pa" => array(
            "bar"  => 1E5,
            "at"   => 0.980665E5,
            "atm"  => 1.01324E5,
            "Torr" => 133.322,
            "psi"  => 6.895E3,
        ),
        "bar" => array(
            "Pa"   => 1E-5,
            "at"   => 0.980665,
            "atm"  => 1.01325,
            "Torr" => 1.332E-3,
            "psi"  => 68.948E-3,
        ),
        "at" => array(
            "Pa"   => 1.0197E-5,
            "bar"  => 1.0197,
            "atm"  => 1.0332,
            "Torr" => 1.3595E-3,
            "psi"  => 70.307E-3,
        ),
        "atm" => array(
            "Pa"   => 9.8692E-6,
            "bar"  => 0.98692,
            "at"   => 0.96784,
            "Torr" => 1.3158E-3,
            "psi"  => 68.046E-3,
        ),
        "Torr" => array(
            "Pa"   => 7.5006E-3,
            "bar"  => 750.06,
            "at"   => 735.56,
            "atm"  => 760,
            "psi"  => 51.715,
        ),
        "psi" => array(
            "Pa"   => 145.04E-6,
            "bar"  => 14.5037744,
            "at"   => 14.223,
            "atm"  => 14.696,
            "Torr" => 19.337E-3,
        ),
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

}
?>
