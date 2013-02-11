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
require_once dirname(__FILE__)."/XTableBase.php";

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
class Process extends XTableBase
{
    /**
    * This is the cache for the drivers.
    */
    protected $driverLoc = "processTable";
    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $table   The table to use
    * @param object &$device The device object to use
    *
    * @return null
    */
    public static function &factory(
        &$system, $data=null, $table=null, &$device = null
    ) {
        if (empty($table)) {
            $table = "DeviceProcesses";
        }
        $object = parent::factory($system, $data, $table, $device);
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
        if ($default) {
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
