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
class Firmware extends \HUGnet\base\SystemTableBase
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
    * Gets the latest firmware for the device
    *
    * @return bool True on success, false on failure
    */
    public function getLatest()
    {
        $where["FWPartNum"] = $this->_device->get("FWPartNum");
        $where["RelStatus"] = array('$lte' => $this->table()->get("RelStatus"));
        $where["Active"]    = array('$ne' => 0);
        $HWPartNum = $this->get("HWPartNum");
        if (!empty($HWPartNum)) {
            $where["HWPartNum"] = $HWPartNum;
        }
        $version = $this->get("Version");
        if (!empty($version)) {
            $where["Version"] = $version;
        }
        $ret = $this->table()->selectInto($where);
        // This makes sure we are getting a good one if there is one, instead
        // of a bad one.
        $highest = array("Version" => "0.0.0");
        $found = false;
        do {
            $data = $this->table()->toArray();
            if (($data["RelStatus"] == \HUGnet\db\tables\Firmware::BAD) || !$this->table()->checkHash()) {
                continue;
            }
            if ($this->compareVersion($highest["Version"], $data["Version"]) < 0) {
                $found = true;
                $highest = $data;
            }
        } while ($ret = $this->table()->nextInto());
        $this->table()->clearData();
        if ($found) {
            $this->table()->fromArray($highest);
        }
        return $found;
    }
    /**
    * Runs a function using the correct driver for the endpoint
    *
    * @param string $ver1 The first version to use in the compare
    * @param string $ver2 The second version to use in the compare
    *
    * @return int -1 if $ver1 < $ver2, 0 if $ver1 == $ver2, 1 if $ver1 > $ver2
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    */
    public function compareVersion($ver1, $ver2 = null)
    {
        $useVer2 = (empty($ver2)) ? $this->table()->get("Version") : $ver2;
        $v1 = explode(".", $ver1);
        $v2 = explode(".", $useVer2);
        for ($i = 0; $i < 3; $i++) {
            if ($v1[$i] > $v2[$i]) {
                return 1;
            } else if ($v1[$i] < $v2[$i]) {
                return -1;
            }
        }
        return 0;
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
        $where['HWPartNum'] = $this->_device->get("HWPartNum");
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
