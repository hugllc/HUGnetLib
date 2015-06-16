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
class Process extends \HUGnet\base\IOPBase
    implements \HUGnet\interfaces\SystemInterface
{
    /** These are our keys to search for.  Null means search everything given */
    protected $keys = array("dev", "process");
    /** This is the type of IOP this is */
    protected $type = "process";
    /**
    * This is the cache for the drivers.
    */
    protected $driverLoc = "processTable";
    /** This is our url */
    protected $url = "/process";
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
            $dbtable = "DeviceProcesses";
        }
        $object = parent::factory($system, $data, $dbtable, $device, $table);
        return $object;
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
            $return["otherTypes"] = processTable\Driver::getTypes(
                $return["id"]
            );
            $return["validIds"] = $this->driver()->getDrivers();
        }
        return (array)$return;
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
        return $this->table()->get("process");
    }
    /**
    * Gets the config and saves it
    *
    * @param string $url The url to post to
    *
    * @return string The left over string
    */
    public function post($url = null)
    {
        if (!is_string($url) || (strlen($url) == 0)) {
            $master = $this->system()->get("master");
            $url = $master["url"];
        }
        $process = $this->toArray(false);
        return \HUGnet\Util::postData(
            $url,
            array(
                "uuid"   => urlencode($this->system()->get("uuid")),
                "id"     => sprintf("%06X", $process["dev"]).".".$process["process"],
                "action" => "put",
                "task"   => "deviceprocess",
                "data"   => $process,
            )
        );
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function push()
    {
        $encode = $this->encode();
        $ret = $this->device()->send(
            array(
                "Command" => "SETPROCESSTABLERAM",
                "Data" => sprintf("%02X", $this->get("process")).$encode
            )
        );
        if (is_object($ret)) {
            $this->decode($ret->reply());
            return $ret->reply() == $encode;
        }
        return false;
    }
    /**
    * Encodes this driver as a setup string
    *
    * @return array
    */
    public function pull()
    {
        $ret = $this->device()->send(
            array(
                "Command" => "READPROCESSTABLERAM",
                "Data" => sprintf("%02X", $this->get("process"))
            )
        );
        if (is_object($ret)) {
            $this->decode($ret->reply());
            return true;
        }
        return false;
    }
}


?>
