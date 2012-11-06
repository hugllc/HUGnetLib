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
namespace HUGnet\units;
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
abstract class Driver
{
    /** This is a raw record */
    const TYPE_RAW = "raw";
    /** This is a differential record */
    const TYPE_DIFF = "diff";
    /** This is a raw record */
    const TYPE_IGNORE = "ignore";

    /** @var The units that are valid for conversion */
    protected $valid = array();
    /** @var Unit conversion multipliers */
    protected $multiplier = array();

    /**
    * Sets everything up
    *
    * @return null
    */
    private function __construct()
    {
    }

    /**
    * This function creates the system.
    *
    * @return null
    */
    protected static function &intFactory()
    {
        $class = get_called_class();
        $object = new $class();
        return $object;
    }
    /**
    * This function creates the system.
    *
    * @param string $unitType The type of unit to load
    * @param string $units    The units we are loading
    *
    * @return null
    */
    public static function &factory($unitType, $units)
    {
        $class = '\\HUGnet\\units\\drivers\\'.$unitType;
        $file = dirname(__FILE__)."/drivers/".$unitType.".php";
        if (file_exists($file)) {
            include_once $file;
        }
        if (class_exists($class)) {
            return $class::factory($units);
        }
        include_once dirname(__FILE__)."/drivers/GENERIC.php";
        return \HUGnet\units\drivers\GENERIC::factory($units);
    }

    /**
    * Does the actual conversion
    *
    * @param mixed  &$data The data to convert
    * @param string $to    The units to convert to
    * @param string $from  The units to convert from
    * @param string $type  The data type we are converting (raw or differential)
    *
    * @return mixed The value returned
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function convert(&$data, $to, $from, $type)
    {
        $ret = false;
        if (isset($this->multiplier[$to]) && isset($this->multiplier[$to][$from])) {
            $data *= $this->multiplier[$to][$from];
            $ret = true;
        } else if ($from == $to) {
            $ret = true;
        }

        return $ret;
    }

    /**
    * Checks to see if units are valid
    *
    * @param string $units The units to check for validity
    *
    * @return mixed The value returned
    */
    public function valid($units)
    {
        return in_array($units, (array)$this->valid);
    }

    /**
    * Checks to see if units are valid
    *
    * @return array Array of units returned
    */
    public function getValid()
    {
        $ret = array();
        foreach ((array)$this->valid as $valid) {
            $ret[$valid] = $valid;
        }
        return $ret;
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
        // This only replies true for units that it knows about
        return in_array($units, (array)$this->valid);
    }

}
?>
