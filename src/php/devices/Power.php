<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/IOPBase.php";
/** This is our system interface */
require_once dirname(__FILE__)."/../interfaces/SystemInterface.php";

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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.14.5
 */
class Power extends \HUGnet\base\IOPBase
    implements \HUGnet\interfaces\SystemInterface
{
    /** These are our keys to search for.  Null means search everything given */
    protected $keys = array("dev", "power");
    /** This is the type of IOP this is */
    protected $type = "power";
    /**
    * This is the cache for the drivers.
    */
    protected $driverLoc = "powerTable";
    /** This is our url */
    protected $url = "/power";
    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $dbtable The table to use
    * @param object &$device The device object to use
    * @param array  $table   The table to use.  This forces the table, instead of
    *                        using the database to find it
    *
    * @return null
    */
    public static function &factory(
        &$system, $data = null, $dbtable = null, &$device = null, $table = null
    ) {
        if (empty($dbtable)) {
            $dbtable = "DevicePowers";
        }
        $object = parent::factory($system, $data, $dbtable, $device, $table);
        return $object;
    }
    /**
    * Lists the ids of the table values
    *
    * @return The ID of this sensor
    *
    * @SuppressWarnings(PHPMD.ShortMethodName)
    */
    public function id()
    {
        return $this->table()->get("power");
    }
    /**
    * Returns the table as an array
    *
    * @param bool $default Whether to include the default params or not
    *
    * @return array
    */
    public function toArray($default = true)
    {
        $return = (array)parent::toArray($default);
        if (($default) && ($default !== "entryonly")) {
            $return["otherTypes"] = powerTable\Driver::getTypes(
                $return["id"]
            );
            $return["validIds"] = $this->driver()->getDrivers();
        }
        return (array)$return;
    }
    /**
    * Gets the data for this item
    *
    * @param object $args The argument object
    *
    * @return string
    */
    protected function getData($api)
    {
        $size   = $this->device()->get("PowerPortDataSize");
        $starts = $this->device()->get("PowerPortData");
        $power  = $this->id();
        $ret    = null;
        if (!empty($size) && is_array($starts) && isset($starts[$power])) {
            $start = $starts[$power];
            $data = $this->device()->getParam("LastPollData");
            if (is_array($data)) {
                $ret = array();
                for ($i = 0; $i < $size; $i++) {
                    if (isset($data[$start + $i])) {
                        $ret[$i] = $data[$start + $i];
                    }
                }
            }
        }
        return $ret;
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channels()
    {
        $channels = (array)$this->driver()->channels();
        $sid      = $this->id();
        $loc      = explode(",", (string)$this->get("location"));
        foreach (array_keys($channels) as $key) {
            $label = (isset($loc[$key])) ? $loc[$key] : $loc[0];
            $channels[$key]['label'] = $label;
            $channels[$key]["power"] = $sid;
        }
        return $channels;
    }
    /**
    * Returns the arch to use for the table
    *
    * @return string The arch
    */
    protected function getTableArch()
    {
        return "";
    }
}


?>
