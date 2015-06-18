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
    implements \HUGnet\interfaces\SystemInterface,
        \HUGnet\interfaces\WebAPI2
{
    /** @var int The database table class to use */
    protected $tableClass = "Firmware";
    /** @var Severity level for syslog */
    private $_syslog = array(
    );
    /**
    * This is the device we rode in on
    */
    private $_device;
    /** This is our url */
    protected $url = "/firmware";
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
        $object = new $class($system, $dbtable, $data);
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
        if (is_object($this->_device)) {
            $HWPartNum = $this->_device->get("arch");
            if (trim(strtolower($HWPartNum)) == "bootloader") {
                $HWPartNum = $this->table()->fixHWPartNum(
                    $this->_device->get("HWPartNum")
                );
            } else {
                $where["FWPartNum"] = $this->_device->get("FWPartNum");
            }
        } else {
            $where["FWPartNum"] = $this->table()->get("FWPartNum");
        }
        $where["RelStatus"] = array('$lte' => $this->table()->get("RelStatus"));
        $where["Active"]    = array('$ne' => 0);
        if (!empty($HWPartNum)) {
            $where["HWPartNum"] = $HWPartNum;
        }
        $version = $this->table()->get("Version");
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
            if (($data["RelStatus"] == \HUGnet\db\tables\Firmware::BAD) || !$this->table()->checkHash() || !$this->isLoadable()) {
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
    * Gets the latest firmware for the device
    *
    * @return bool True on success, false on failure
    */
    public function isLoadable()
    {
        // This makes sure that it is the correct firmware for this device
        if (is_object($this->_device)) {
            $Part = $this->table()->fixHWPartNum($this->_device->get("HWPartNum"));
            if ($this->table()->get("HWPartNum") != $Part) {
                return false;
            }
        }
        // This makes sure it is not a bootloader.
        $Part = $this->table()->get("FWPartNum");
        return (($Part != "0039-38-02-C") && ($Part != "0039-38-81-C") && ($Part != "0039-38-82-C"));
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
        if (is_object($this->_device)) {
            $where['HWPartNum'] = $this->_device->get("arch");
        }
        $ret =  parent::getList($where, $default);
        // We don't want to return code and data.  They are too big for a list.
        // The client can request specific firmwares if it wants code and data.
        foreach ($ret as &$val) {
            unset($val["Code"]);
            unset($val["Data"]);
        }
        return $ret;
    }
    /**
    * Changes an SREC source into a raw memory buffer
    *
    * @param string $empty This is what a byte looks like when it is
    *    erased.  The default is for flash memory (FF);
    *
    * @return string The raw memory buffer
    */
    public function getCode($empty="FF")
    {
        return $this->_interpSREC($this->table()->get("Code"), $empty);
    }
    /**
    * Changes an SREC source into a raw memory buffer
    *
    * @param string $empty This is what a byte looks like when it is
    *    erased.  The default is for flash memory (FF);
    *
    * @return string The raw memory buffer
    */
    public function getData($empty="FF")
    {
        return $this->_interpSREC($this->table()->get("Data"), $empty);
    }
    /**
    * Changes an SREC source into a raw memory buffer
    *
    * @param string $srec  The S record to change.
    * @param string $empty This is what a byte looks like when it is
    *    erased.  The default is for flash memory (FF);
    *
    * @return string The raw memory buffer
    */
    private function _interpSREC($srec, $empty="FF")
    {
        // Put the srec into the buffer
        $srec = explode("\n", $srec);
        $code = array();
        $start = -1;
        foreach ((array)$srec as $rec) {
            if (substr($rec, 0, 2) == "S0") {
                if ($start >= 0) {
                    // Got some stuff
                    // remove the extra
                    while (substr($buffer, -2) == $empty) {
                        $buffer = substr($buffer, 0, -2);
                    }
                    $code[$start] = $buffer;
                    $buffer = "";
                }
                $start = -1;
            } else if (substr($rec, 0, 2) == "S1") {
                // Set up all the stuff to put into the buffer
                $size  = hexdec(substr($rec, 2, 2));
                $size -= 3;
                $addr  = hexdec(substr($rec, 4, 4));
                if ($start < 0) {
                    $start = $addr;
                }
                $addr -= $start;
                $data  = substr($rec, 8, ($size*2));
                // Make sure the buffer is big enough for the data
                $buffer = str_pad($buffer, ($addr + $size)*2, $empty, STR_PAD_RIGHT);
                // Put the data into the buffer
                $buffer = substr_replace($buffer, $data, $addr*2, $size*2);
            } else if (substr($rec, 0, 2) == "S2") {
                // Set up all the stuff to put into the buffer
                $size  = hexdec(substr($rec, 2, 2));
                $size -= 3;
                $addr  = hexdec(substr($rec, 4, 6));
                if ($start < 0) {
                    $start = $addr;
                }
                $addr -= $start;
                $data  = substr($rec, 10, ($size*2));
                // Make sure the buffer is big enough for the data
                $buffer = str_pad($buffer, ($addr + $size)*2, $empty, STR_PAD_RIGHT);
                // Put the data into the buffer
                $buffer = substr_replace($buffer, $data, $addr*2, $size*2);
            }
        }
        if ($start >= 0) {
            // remove the extra
            while (substr($buffer, -2) == $empty) {
                $buffer = substr($buffer, 0, -2);
            }
            $code[$start] = $buffer;
        }
        // return the buffer
        return $code;
    }
    /**
    * returns a history object for this device
    *
    * @param object $api   The api object
    * @param array  $extra Extra data from the
    *
    * @return string
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function webAPI2($api, $extra)
    {
        $return = 0;
        $method = trim(strtoupper($api->args()->get("method")));
        $sid = trim(strtolower($api->args()->get("sid")));
        if (is_object($this->_device)
            && (($method == "PUT") || ($method == "POST"))
        ) {
            if (empty($sid)) {
                $return = $this->getLatest();
            }
            if ($return && $this->_device->action()->loadFirmware($this)) {
                $api->response(202);
                $return = 1
            } else {
                $api->response(400);
                $api->error(\HUGnet\ui\WebAPI2::BAD_REQUEST);
            }
        } else {
            $api->response(501);
            $api->error(\HUGnet\ui\WebAPI2::NOT_IMPLEMENTED);
        }
        return $return;
    }
}


?>
