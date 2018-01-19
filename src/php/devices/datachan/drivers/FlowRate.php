<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\datachan\drivers;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * This class represents pressure in the HUGnet system.
 *
 * Information on conversion factors was found at:
 * http://en.wikipedia.org/wiki/Flow_rate
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Units
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class FlowRate extends \HUGnet\devices\datachan\Driver
{
    /** @var The units that are valid for conversion */
    protected $valid = array(
        "gal/min", "gal/hr", "gal/day", "L/min", "L/hr", "L/day" 
    );
    /** @var Unit conversion multipliers */
    protected $multiplier = array(
        "gal/min" => array(
            "gal/hr"    => 0.01666666667,
            "gal/day"   => 0.00069444444,
            "L/min"     => 0.264172052637,
            "L/hr"      => 0.0044028675,
            "L/day"     => 0.0001834528,
        ),
        "gal/hr" => array(
            "gal/min"  => 60,
            "gal/day"  => 0.04166666667,
            "L/min"    => 15.8503231582,
            "L/hr"     => 0.264172052637,
            "L/day"    => 0.0110071689,
        ),
        "gal/day" => array(
            "gal/min"  => 1440,
            "gal/hr"   => 24,
            "L/min"    => 380.4077557973,
            "L/hr"     => 6.3401292633,
            "L/day"    => 0.264172052637,
        ),
        "L/min" => array(
            "gal/min"  => 3.78541178,
            "gal/hr"   => 0.0630901963,
            "gal/day"  => 0.0026287582,
            "L/hr"     => 0.01666666667,
            "L/day"    => 0.00069444444,
        ),
        "L/hr" => array(
            "gal/min"  => 227.124707,
            "gal/hr"   => 3.78541178,
            "gal/day"  => 0.1577254908,
            "L/min"    => 60,
            "L/day"    => 0.04166666667,
        ),
        "L/day" => array(
            "gal/min"  => 5450.99297,
            "gal/hr"   => 90.84988272,
            "gal/day"  => 3.78541178,
            "L/min"    => 1440,
            "L/hr"     => 24,
        ),
    );
    /** @var Unit conversion prefixes */
    protected $prefix = array(
    );

}
?>
