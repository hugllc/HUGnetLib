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
 * @version    Release: 0.14.8
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
    /** This is our url */
    protected $url = "/error";
    /** This is our error messages */
    private $_errorMsg = array(
        1  => "CPU Error",
        2  => "Hardware Over Current",
        3  => "Software Over Current",
        4  => "Hardware Over Power",
        5  => "Software Over Power",
        6  => "Bad Switch",
        7  => "Bad Current Sensor",
        8  => "Power Port Error",
        9  => "Power Flowing in the Wrong Direction",
        10 => "Multiple Port Errors",
        11 => "Bus Brownout",
        12 => "Waiting for Calibration",
        13 => "FET Over Heat"
    );
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
        return $object;
    }
    /**
    * Logs an error in the database
    *
    * @param string $error The error message
    * @param string $sev   The severity of the error
    *
    * @return null
    */
    private function _log($error, $sev)
    {
        if (is_string($error)) {
            $error = $this->decode($error);
        }
        if (is_array($error)) {
            $error["id"] = $this->_device->id();
            $error["severity"] = $sev;
            $this->table()->fromArray($error);
            return $this->table()->insertRow(true);
        }
        return false;
    }
    /**
    * Returns a list of the items that it sees.
    *
    * @param array $where   The things the list should filter for
    * @param bool  $default Whether to add the default stuff on or not.
    *
    * @return null
    */
    public function getList($where = null, $default = false)
    {
        $where = (array)$where;
        $where['id'] = $this->_device->id();
        return parent::getList($where, $default);
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
        return $this->_log($error, "E");
    }
    /**
    * Logs an error in the database
    *
    * @param int    $id    The id of the device
    * @param string $error The error message
    *
    * @return null
    */
    public function warning($error)
    {
        return $this->_log($error, "W");
    }
    /**
    * Logs an error in the database
    *
    * @param int    $id    The id of the device
    * @param string $error The error message
    *
    * @return null
    */
    public function out()
    {
        $sev = $this->get("severity");
        $id    = sprintf("%06X", $this->get("id"));
        $errno = $this->get("errno");
        $extra = $this->get("extra");
        if ($sev == "E") {
            $port  = hexdec(substr($extra, 0, 2)) - 0x30;
            $msg   = $this->_errorMsg[$errno];
            if (empty($msg)) {
                $msg = "Unknown";
            }
            $this->system()->out("Error Device $id Port $port (".$errno."): ".$msg." (".pack("H*", substr($extra, 2)).")");
        } else {
            $this->system()->out("Warning Device $id (".$errno."): ".pack("H*", $extra));
        }
    }
    /**
    * Logs an error in the database
    *
    * @param int    $id    The id of the device
    * @param string $error The error message
    *
    * @return null
    */
    public function logwarn($error)
    {
        return $this->_log($error, "W");
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
    /**
    * Lists the ids of the table values
    *
    * @return int The ID of this device
    *
    * @SuppressWarnings(PHPMD.ShortMethodName)
    */
    public function id()
    {
        return $this->_device->id();
    }

}


?>
