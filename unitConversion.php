<?php
/**
 * Everything to do with Units.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage UnitConversion
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is for the base class */
require_once dirname(__FILE__)."/base/HUGnetClass.php";
/** This is the plugin interface */
require_once dirname(__FILE__)."/lib/plugins.inc.php";
/**
 * Class for doing unit conversions and storing unit information
 *
 * All units must be added to this class if they are to be used in HUGnet
 * code.  This is because this tells everything else how to deal with these
 * units and how to convert between them.  If units are not added here then
 * many things will not work with the units.
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage UnitConversion
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class UnitConversion extends HUGnetClass
{

    /**
     *  This is the array that defines all of our units and how to
     * display and use them.
     *  @var array
     *
     */
    var $units = array();
    /**
     * This registers the sensor Plugins so we know what code we have available.
     *
     * @param object &$plugins This is a object of type plugin
     *
     * @see plugin
      */
    function __construct(&$plugins = "")
    {
        if (!is_object($plugins)) {
            $plugins = new Plugins(dirname(__FILE__)."/drivers/units/", "php");
        }

        if (is_array($plugins->plugins["Generic"]["units"])) {
            foreach ($plugins->plugins["Generic"]["units"] as $units) {
                $this->registerUnits($units);
            }
        }

    }
    /**
     * Register a sensor class.
     *
     * @param array $units Array with "Name" and "Class" defined.
     *
     * @return bool true on success, false on failure
     */
    public function registerUnits($units)
    {
        $class = (string) $units["Class"];
        $name  = (string) $units["Name"];
        if (!class_exists($class)) {
            return;
        }

        $this->unitsClass[$class] = new $class();

        if (is_array($this->unitsClass[$class]->units)) {
            $this->units[$name] = $this->unitsClass[$class]->units;
            foreach ($this->units[$name] as $key => $val) {
                $this->units[$name][$key]["class"] = $class;
            }
            return true;
        } else {
            return false;
        }

    }

    /**
     * Checks to see if there is a unit that is preferred over the one given.
     *
     * @param string $unit The unit to check
     *
     * @return string The preferred unit
      */
    public function preferredUnit($unit)
    {
        $u = $this->findUnit($unit);
        if (isset($u['preferred'])) {
            return $u['preferred'];
        } else {
            return $unit;
        }
    }

    /**
     * Checks to see if a particular unit is able to be graphed
     *
     * @param string $unit The unit to check
     *
     * @return bool Whether the unit can be graphed or not
      */
    public function graphable($unit)
    {
        $unit = trim($unit);
        $u    = $this->findUnit($unit);
        if ($u === false) {
            return false;
        }
        if (($u['varType'] == 'int') || ($u['varType'] == 'float')) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Checks if a unit exists and returns the information on it if it does.
     *
     * Returns false if the unit is not found
     *
     * @param string $unit The unit to find
     *
     * @return array the array of unit information if it is found
     */
    public function findUnit($unit)
    {

        foreach ($this->units as $key => $value) {
            if (isset($value[$unit])) {
                return $this->units[$key][$unit];
            }
        }
        return false;

    }

    /**
     * Gets the data type for this particular unit
     *
     * @param string $from    The starting unit
     * @param string $to      The unit to be converted into
     * @param string $default The data type to use if none is specified
     *
     * @return string The data type to use
     */
    public function getDataType($from, $to, $default = 'all')
    {
        if (trim(strtolower($default)) == 'ignore') {
            return $default;
        }
        $u = $this->findUnit($from);
        if ($u !== false) {
            if (isset($u['mode'])) {
                return $u['mode'];
            }
        }
        return $default;
    }

    /**
     * Gets the conversion function to convert $from to $to
     *
     * @param string $from The starting unit
     * @param string $to   The unit to be converted into
     * @param string $type The data type to use
     *
     * @return string null if no function exists, the function name otherwise.
     */
    protected function getConvFunct($from, $to, $type)
    {
        if ($to == $from) {
            return null;
        }
        $f = $this->findUnit($from);
        $t = $this->findUnit($to);
        if (empty($t['mode']) || ($t['mode'] == $type)) {
            return array($f['class'], $f['convert'][$to]);
        }
        return null;
    }

    /**
     * Converts a value based on input given.
     *
     * @param mixed  $val   The value to convert
     * @param string $from  The starting unit
     * @param string &$to   The unit to be converted into
     * @param int    $time  The time in seconds between this record and the last.
     * @param string $type  The data type to use
     * @param mixed  $extra Any extra stuff we might need.
     *
     * @return mixed
     */
    public function convert($val, $from, &$to, $time, $type, $extra)
    {
        list($class, $func) = $this->getConvFunct($from, $to, $type);
        if (substr(trim(strtolower($func)), 0, 6) == "shift:") {
            if (method_exists($this->unitsClass[$class], "shift")) {
                $shift = (int)substr($func, 6);
                $val = $this->unitsClass[$class]->shift($val, $shift);
            }
        } else {
            if (method_exists($this->unitsClass[$class], $func)
                && ($val !== null)
            ) {
                $val = $this->unitsClass[$class]->{$func}(
                    $val,
                    $time,
                    $type,
                    $extra
                );
            } else {
                $to = $from;
            }
        }
        return $val;
    }
    /**
     * Gets all possible conversions if $from == null.  Otherwise
     *
     * it gets all possible conversions from $from.
     *
     * @param string $type The data type to use if none is specified
     * @param string $from The starting unit
     *
     * @return array The possible conversions
     */
    public function getPossConv($type, $from=null)
    {

        $ret = array();
        foreach ($this->units as $c => $cat) {
            foreach ($cat as $f => $info) {
                if (($info["mode"] == $type) || !isset($info["mode"])) {
                    if (!is_array($info["convert"])) {
                        continue;
                    }
                    foreach ($info['convert'] as $t => $func) {
                        $ret[$f][] = $t;
                    }
                }
            }
        }

        if (!is_null($from)) {
            $ret[$from][] = $from;
            return $ret[$from];
        }
        return $ret;
    }
    /**
     * Gets all units, or all units from $type if $type != null
     *
     * @param string $type The data type to use if none is specified
     * @param bool   $flat If the data returned should be flat
     *
     * @return array The possible conversions
     */
    public function getAllUnits($type=null, $flat=false)
    {

        $ret = array();
        $flatret = array();
        foreach ($this->units as $c => $cat) {
            foreach ($cat as $f => $info) {
                $ret[$c][$f] = $f;
                $flatret[$f] = $c.":".$f;
            }
        }

        if (!is_null($type)) {
            return $ret[$type];
        }
        if ($flat) {
            return $flatret;
        }
        return $ret;
    }

    /**
     * Modifies the units based on input.
     *
     * This expects all the units to be correct.
     *
     * @param array &$history The history to modify.  This array gets directly
     *                           modified.
     * @param array &$devInfo The devInfo array to modify.  This array gets
     *                           directly modified.
     * @param int   $dPlaces  The maximum number of decimal places to show.
     * @param array &$type    The types to change to
     * @param array &$units   The units to change to
     *
     * @return null
     */
    function modifyUnits(&$history, &$devInfo, $dPlaces, &$type=null, &$units=null)
    {
        $devInfo["modifyUnits"]++;
        $lastRecord = null;
        $totalSensors = $this->_totalSensors($devInfo);
        if (!is_array($history)) {
            $history = array();
        }
        foreach ($history as $key => $val) {
            if (!is_array($val) || !isset($history[$key])) {
                unset($history[$key]);
                continue;
            }
            $this->_modifyUnitsDiff($history, $devInfo, $type, $key);
            $this->_modifyUnits($history, $devInfo, $dPlaces, $type, $units, $key);
        }
        for ($i = 0; $i < $totalSensors; $i ++) {
            if (!empty($units[$i])) {
                $devInfo["Units"][$i] = $units[$i];
            }
            $devInfo["dType"][$i] = $type[$i];
        }
    }


    /**
    * This function deals with the diff data type.
    *
     * @param array &$history The history to modify.  This array gets directly
     *                           modified.
     * @param array &$devInfo The devInfo array to modify.  This array gets
     *                           directly modified.
     * @param array &$type    The types to change to
     * @param int   $key      The history key to use
     *
     * @return null
    *
    */
    private function _modifyUnitsDiff(
        &$history,
        &$devInfo,
        &$type,
        $key
    ) {
        static $lastRecord;

        $totalSensors = $this->_totalSensors($devInfo);
        if (($lastRecord !== null) || (count($history) < 2)) {
            for ($i = 0; $i < $totalSensors; $i ++) {
                if (empty($type[$i])) {
                    $type[$i] = $devInfo["params"]["dType"][$i];
                }
                if ($type[$i] == "ignore") {
                    continue;
                }
                if ($type[$i] != $devInfo["dType"][$i]) {
                    switch($type[$i]) {
                    case 'diff':
                        if (!isset($history[$key]['deltaT'])) {
                            $history[$key]['deltaT']
                                = strtotime($history[$key]['Date'])
                                - strtotime($lastRecord['Date']);
                        }
                        $history[$key]["Data".$i]
                            = $lastRecord["Data".$i]
                            - $history[$key]["Data".$i];
                        break;
                    default:
                        // Do nothing by default.
                        // That means we need to make sure we change the
                        // data type in the $type array to reflect what
                        // we have not done.  ;)
                        if (!empty($devInfo["dType"][$i])) {
                            $type[$i] = $devInfo["dType"][$i];
                        }
                        break;
                    }
                }
            }
            $lastRecord = $history[$key];
        } else {
            $lastRecord = $history[$key];
            unset($history[$key]);
        }

    }
    /**
     * Modifies the units based on input.
     *
     * This expects all the units to be correct.
     *
     * @param array &$history The history to modify.  This array gets directly
     *                           modified.
     * @param array &$devInfo The devInfo array to modify.  This array gets
     *                           directly modified.
     * @param int   $dPlaces  The maximum number of decimal places to show.
     * @param array &$type    The types to change to
     * @param array &$units   The units to change to
     * @param int   $key      The Key to use
     *
     * @return null
     */
    private function _modifyUnits(
        &$history,
        &$devInfo,
        $dPlaces,
        &$type,
        &$units,
        $key
    ) {
        $totalSensors = $this->_totalSensors($devInfo);
        for ($i = 0; $i < $totalSensors; $i ++) {
            if (is_null($history[$key]['Data'.$i])) {
                continue;
            }
            if ($type[$i] == "ignore") {
                continue;
            }
            if (empty($units[$i])) {
                $units[$i] = $devInfo["params"]['Units'][$i];
            }
            if (isset($history[$key]['Units'][$i])) {
                $from = $history[$key]['Units'][$i];
            } else {
                $from = $devInfo['Units'][$i];
            }
            if (empty($units[$i])) {
                $units[$i] = $this->preferredUnit($from);
            }
            if (!empty($units[$i])) {
                $history[$key]['Data'.$i] = $this->convert(
                    $history[$key]['Data'.$i],
                    $from,
                    $units[$i],
                    $history[$key]['deltaT'],
                    $type[$i],
                    $extra[$i]
                );
            }
            if (isset($dPlaces) && is_numeric($dPlaces)
                && is_numeric($history[$key]["Data".$i])
            ) {
                $history[$key]["Data".$i] = round(
                    $history[$key]["Data".$i],
                    $dPlaces
                );
            }
            $history[$key]['data'][$i] = $history[$key]['Data'.$i];
        }

    }
    /**
    * This function deals with the diff data type.
    *
     * @param array &$devInfo The devInfo array to modify.
     *
     * @return int The total number of sensors
    *
    */
    private function _totalSensors(&$devInfo)
    {
        if (empty($devInfo["TotalSensors"])) {
            return $devInfo["ActiveSensors"];
        }
        return $devInfo["TotalSensors"];


    }

}


?>
