<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/HUGnetClass.php";
require_once dirname(__FILE__)."/../interfaces/UnitsInterface.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class UnitsBase extends HUGnetClass implements UnitsInterface
{
    /** This is a raw record */
    const TYPE_RAW = "raw";
    /** This is a differential record */
    const TYPE_DIFF = "diff";
    /** This is a raw record */
    const TYPE_IGNORE = "ignore";

    /** @var The units of this point */
    public $to = "Unknown";
    /** @var The type of this point */
    public $from = "Unknown";
    /** @var The original values given to us */
    protected $type = self::TYPE_RAW;

    /** @var The units that are valid for conversion */
    protected $valid = array();

    /**
    * Sets everything up
    *
    * @param array $data The data to start with
    *
    * @return null
    */
    public function __construct($data)
    {
        foreach (array("from", "to", "type") as $t) {
            if (!empty($data[$t])) {
                $this->$t = $data[$t];
            }
        }
    }


    /**
    * Does the actual conversion
    *
    * @param mixed  &$data The data to convert
    * @param string $to    The units to convert to
    * @param string $from  The units to convert from
    *
    * @return mixed The value returned
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function convert(&$data, $to=null, $from=null)
    {
        if (!is_null($from)) {
            $this->from = $from;
        }
        if (!is_null($to)) {
            $this->to = $to;
        }
        if ($this->from == $this->to) {
            return true;
        } else {
            return false;
        }
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
