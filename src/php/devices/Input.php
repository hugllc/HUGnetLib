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
 * @since      0.9.7
 */
class Input extends \HUGnet\base\IOPBase
    implements \HUGnet\interfaces\SystemInterface
{
    /** These are our keys to search for.  Null means search everything given */
    protected $keys = array("dev", "input");
    /** This is the type of IOP this is */
    protected $type = "input";
    /**
    * This is the cache for the drivers.
    */
    protected $driverLoc = "inputTable";
    /** This is our url */
    protected $url = "/input";

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
        &$system, $data=null, $dbtable=null, &$device = null, $table = null
    ) {
        if (empty($dbtable)) {
            $dbtable = "DeviceInputs";
        }
        $object = parent::factory($system, $data, $dbtable, $device, $table);
        return $object;
    }
    /**
    * Lists the ids of the table values
    *
    * @return The ID of this input
    *
    * @SuppressWarnings(PHPMD.ShortMethodName)
    */
    public function id()
    {
        return $this->table()->get("input");
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
            $arch = $this->device()->get("arch");
            if ($arch == "old") {
                /* Can't change anything about the old system */
                $return["validIds"] = array(
                    $this->get("id") => $this->get("longName")
                );
            } else {
                $return["validIds"] = $this->driver()->getDrivers();
            }
            $return["otherTypes"] = \HUGnet\devices\inputTable\Driver::getTypes(
                $return["id"]
            );
            $return["validUnits"] = $this->units()->getValid();
        }
        return (array)$return;
    }
    /**
    * This creates the units driver
    *
    * @return object
    */
    protected function &units()
    {
        include_once dirname(__FILE__)."/../devices/datachan/Driver.php";
        $units = \HUGnet\devices\datachan\Driver::factory(
            $this->get("unitType"),
            $this->get("storageUnit")
        );
        return $units;
    }
    /**
    * Gets the direction from a direction input made out of a POT.
    *
    * @param string &$string The data string
    * @param float  $deltaT  The time delta in seconds between this record
    * @param array  &$prev   The previous reading
    * @param array  &$data   The data from the other inputs that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function decodeData(
        &$string, $deltaT = 0, &$prev = null, &$data = array(), $chan = NULL
    ) {
        if (is_null($chan)) {
            $chan = $this->channelStart();
        }
        return $this->driver()->decodeData($string, $chan, $deltaT, $prev, $data);
    }
    /**
    * Gets the direction from a direction input made out of a POT.
    *
    * @param string &$string The data string
    * @param int    $channel The channel to decode
    * @param float  $deltaT  The time delta in seconds between this record
    * @param array  &$prev   The previous reading
    * @param array  &$data   The data from the other inputs that were crunched
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function decodeDataPoint(
        &$string, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        return $this->driver()->decodeDataPoint(
            $string, $channel, $deltaT, $prev, $data
        );
    }
    /**
    * Gets the direction from a direction input made out of a POT.
    *
    * @param array $data    The data to use
    * @param int   $channel The channel to get
    *
    * @return float The direction in degrees
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function encodeDataPoint($data, $channel = 0)
    {
        return $this->driver()->encodeDataPoint($data, $channel);
    }
    /**
    * Gets the direction from a direction sensor made out of a POT.
    *
    * @param string &$string The data string
    * @param int    $channel The channel to decode
    *
    * @return float The raw value
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function getRawData(&$string, $channel = 0)
    {
        return $this->driver()->getRawData($string, $channel);
    }
    /**
    * This function should be overloaded to make changes to the table based on
    * changes to incoming data.
    *
    * This is a way to make sure that the data is consistant before it gets stored
    * in the database
    *
    * @return null
    */
    protected function fixTable()
    {
        parent::fixTable();
        $table =& $this->table();
        $driver =& $this->driver();
        if (!$this->units()->valid($table->get("units"))) {
            $table->set("units", $this->get("storageUnit", $driver));
        }
        $extra = (array)$table->get("extra");
        if (!is_array($extra)) {
            $table->set("extra", array());
        }
    }
    /**
    * Converts data between units
    *
    * @param mixed  &$data The data to convert
    * @param string $units The units to convert to
    *
    * @return true on success, false on failure
    */
    public function convertUnits(&$data, $units = null)
    {
        if (is_array($data) && !is_null($data["value"])) {
            if (is_null($units)) {
                $units = $this->table()->get("units");
            }
            $ret = $this->units()->convert(
                $data["value"],
                $units,
                $data["units"],
                $data["unitType"]
            );
            if ($ret === true) {
                $data["units"] = $units;
            }
            if (is_numeric($data["value"])) {
                $data["value"] = round($data["value"], (int)$this->get("decimals"));
            }
        } else {
            $ret = true;
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
            $channels[$key]["input"] = $sid;
        }
        return $channels;
    }
    /**
    * This builds the class from a setup string
    *
    * @return Array of channel information
    */
    public function channelStart()
    {
        $chan   = 0;
        $input = (int)$this->id();
        for ($i = 0; $i < $input; $i++) {
            $chan += count($this->device()->input($i)->channels());
        }
        return $chan;
    }
    /**
    * Gets the config and saves it
    *
    * @param string $url The url to post to
    *
    * @return string The left over string
    */
    public function post($url = null, $timeout=60)
    {
        if (!is_string($url) || (strlen($url) == 0)) {
            $master = $this->system()->get("master");
            $url = $master["url"];
        }
        $input = $this->toArray(false);
        return \HUGnet\Util::postData(
            $url,
            array(
                "uuid"   => urlencode($this->system()->get("uuid")),
                "id"     => sprintf("%06X", $input["dev"]).".".$input["input"],
                "action" => "put",
                "task"   => "deviceinput",
                "data"   => $input,
            )
        );
    }
}


?>
