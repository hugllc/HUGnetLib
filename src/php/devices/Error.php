<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2015 Hunt Utilities Group, LLC
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
 * @copyright  2015 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/SystemTableBase.php";
/** This is our system interface */
require_once dirname(__FILE__)."/../interfaces/SystemInterface.php";

/**
 * This class controls all error messages and exceptions
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2015 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.5
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.14.5
 */
class Error extends \HUGnet\base\SystemTableBase
    implements \HUGnet\interfaces\SystemInterface
{
    /** @var int The database table class to use */
    protected $tableClass = "DeviceError";
    /** @var Severity level for syslog */
    private $_syslog = array(
    );
    /**
    * This is the device we rode in on
    */
    private $_device;
    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $dbtable The table to use
    * @param object &$device The device object to use
    *
    * @return null
    */
    public static function &factory(
        &$system, $data=null, $dbtable=null, &$device = null
    ) {
        \HUGnet\System::systemMissing(
            get_class($this)." needs to be passed a system object",
            !is_object($system)
        );
        $system->fatalError(
            get_class($this)." needs to be passed a device object",
            !is_object($device)
        );
        $class = get_called_class();
        $object = new $class($system, $dbtable);
        $object->_device = &$device; 
        $object->log($data);
        return $object;
    }
    /**
    * Logs an error in the database
    *
    * @param int    $id    The id of the device
    * @param string $error The error message
    *
    * @return null
    */
    public function log($error)
    {
        if (is_string($error)) {
            $error = $this->decode($error);
        }
        if (is_array($error)) {
            $error["id"] = $this->_device->get("id");
            $this->table()->fromArray($error);
            return $this->table()->insertRow(true);
        }
        return false;
    }
    /**
    * This builds the class from a setup string
    *
    * @param string $string The setup string to decode
    *
    * @return Reference to the network object
    */
    protected function decode($string)
    {
        $vals = array(
            "Date" => $this->_device->decodeRTC(substr($string, 0, 8)),
            "errno" => hexdec(substr($string, 8, 2)),
            "extra" => substr($string, 10),
        );
        return $vals;
    }
    
}


?>