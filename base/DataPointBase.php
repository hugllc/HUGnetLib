<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/HUGnetClass.php";
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DataPointBase extends HUGnetClass
{
    /** This is a raw record */
    const TYPE_RAW = "raw";
    /** This is a differential record */
    const TYPE_DIFF = "diff";
    /** This is a raw record */
    const TYPE_IGNORE = "ignore";

    /** @var The value of this point */
    protected $value = null;
    /** @var The units of this point */
    protected $units = null;
    /** @var The type of this point */
    protected $type = null;

    /**
    * Sets everything up
    *
    * @param mixed  $value The current value of the data
    * @param string $units The units to usef
    * @param string $type  The type of record
    *
    * @return null
    */
    public function __construct($value, $units, $type)
    {
        $this->value = $value;
        $this->units = $units;
        $this->type  = $type;
    }

    /**
    * Returns the value
    *
    * @return mixed The value
    */
    public function value()
    {
        return $this->value;
    }
    /**
    * Creates a sensor from data given
    *
    * @param mixed  $value The current value of the data
    * @param string $units The units to usef
    * @param string $type  The type of record
    *
    * @return string/false The name of the class to use.  False on failure
    */
    protected static function getClass($value, $units, $type)
    {
        static $config;
        static $drivers;
        // Get the config if we don't have it yet.
        if (empty($config)) {
            $config = &ConfigContainer::singleton();
        }
        // Set up the drivers if they are not already set up
        if (empty($drivers)) {
            $drivers = array();
            foreach ((array)$config->plugins->getClass("datapoint") as $d) {
                foreach ($d["Units"] as $u) {
                    $drivers[$u] = $d["Class"];
                }
            }
        }
        // Return the units if there is one.
        if (isset($drivers[$units])) {
            return $drivers[$units];
        }
        // Return the default driver if we didn't find anything else
        return $drivers["DEFAULT"];

    }
    /**
    * Creates a sensor from data given
    *
    * @param mixed  $value The current value of the data
    * @param string $units The units to usef
    * @param string $type  The type of record
    *
    * @return Reference to the sensor on success, null on failure
    */
    public static function &factory($value, $units, $type)
    {
        $class = self::getClass($value, $units, $type);
        $data = new $class($value, $units, $type);
        return $data;
    }
    /**
    * returns a string
    *
    * @return Reference to the sensor on success, null on failure
    */
    public function toString()
    {
        return trim((string)$this->value." ".$this->units);
    }

    /**
    * returns a string
    *
    * @return Reference to the sensor on success, null on failure
    */
    public function __toString()
    {
        return $this->toString();
    }

}
?>
