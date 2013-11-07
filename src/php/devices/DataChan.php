<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/BaseChan.php";

/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class DataChan extends \HUGnet\base\BaseChan
{
    /**
    * This is our units object
    */
    private $_units;
    /** @var array The configuration that we are going to use */
    protected $setable = array("units", "decimals", "dataType");

    /**
    * Returns an array of valid units
    *
    * @return array
    */
    public function validUnits()
    {
        return $this->_units()->getValid();
    }
    /**
    * Does unit conversions
    *
    * @param array &$data The data to convert
    *
    * @return null
    */
    public function convert(&$data)
    {
        if (is_null($data)) {
            return;
        }
        $this->_units()->convert(
            $data,
            $this->get("units"),
            $this->get("storageUnit"),
            $this->get("storageType")
        );
        $data = round($data, $this->get('decimals'));
    }
    /**
    * Encodes data for this channel
    *
    * @param array $data The data to convert
    *
    * @return null
    */
    public function encode($data)
    {
        return $this->input()->encodeDataPoint(
            $data,
            $this->get("index")
        );
    }
    /**
    * Encodes data for this channel
    *
    * @param array $data The data to convert
    *
    * @return null
    */
    public function decode($data)
    {
        return $this->input()->decodeDataPoint(
            $data,
            $this->get("index")
        );
    }
    /**
    * Encodes data for this channel
    *
    * @param array &$data The data to convert
    *
    * @return null
    */
    public function decodeRaw(&$data)
    {
        return $this->input()->getRawData(
            $data,
            $this->get("index")
        );
    }
    /**
    * Returns the input object associated with this channel
    *
    * @return null
    */
    public function input()
    {
        return $this->device()->input($this->get("input"));
    }
    /**
    * Checks for consistancy
    *
    * @return object
    */
    protected function check()
    {
        if (!$this->_units()->valid($this->get("units"))) {
            $this->set("units", $this->get("storageUnit"));
        }
        if ($this->get("decimals") > $this->get("maxDecimals")) {
            $this->set("decimals", $this->get("maxDecimals"));
        }
    }
    /**
    * This creates the units driver
    *
    * @return object
    */
    private function &_units()
    {
        if (!is_object($this->_units)) {
            include_once dirname(__FILE__)."/datachan/Driver.php";
            $this->_units = \HUGnet\devices\datachan\Driver::factory(
                $this->get("unitType"),
                $this->get("storageUnit")
            );
        }
        return $this->_units;
    }
}


?>
