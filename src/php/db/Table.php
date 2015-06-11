<?php
/**
 * Abstract class for building SQL queries
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
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
namespace HUGnet\db;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** require our base class */
require_once dirname(__FILE__)."/TableBase.php";
/**
 * Feature added DB class
 *
 * This class adds many features to the base database class.  Different methods
 * of input and output.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class Table extends TableBase
{
    /** @var This is the date field for this record */
    protected $dateField = null;

    /**
    * This function creates other tables that are identical to this one, except
    * for the data given.
    *
    * @param object &$system  The system object to use
    * @param mixed  $data     This is an array or string to create the object from
    * @param string $class    The class to use
    * @param object &$connect The connection manager
    * @param mixed  $extra1   Extra parameter that is just passed on
    * @param mixed  $extra2   Extra parameter that is just passed on
    *
    * @return object A reference to a table object
    */
    static public function &factory(
        &$system, $data = array(), $class = "Generic", &$connect = null,
        $extra1 = null, $extra2 = null
    ) {
        if (file_exists(dirname(__FILE__)."/tables/".$class.".php")) {
            include_once dirname(__FILE__)."/tables/".$class.".php";
        } else if (file_exists(dirname(__FILE__)."/tables/history/".$class.".php")) {
            return self::_historyFactory(
                $system, $data, $class, $connect, $extra1, $extra2
            );
        } else if (file_exists(dirname(__FILE__)."/tables/average/".$class.".php")) {
            return self::_averageFactory(
                $system, $data, $class, $connect, $extra1, $extra2
            );
        }
        if (substr($class, 0, 17) != "HUGnet\\db\\tables\\") {
            $nclass = "HUGnet\\db\\tables\\".$class;
        }
        $interface = "\\HUGnet\\interfaces\\DBTable";
        if (!is_subclass_of($nclass, $interface)) {
            include_once dirname(__FILE__)."/tables/Generic.php";
            // Assume that the class given is the table name.
            return new \HUGnet\db\tables\Generic(
                $system, $data, $connect, $class
            );
        }
        return new $nclass($system, $data, $connect, $extra1, $extra2);
    }
    /**
    * This function creates other tables that are identical to this one, except
    * for the data given.
    *
    * @param object &$system  The system object to use
    * @param mixed  $data     This is an array or string to create the object from
    * @param string $class    The class to use
    * @param object &$connect The connection manager
    * @param mixed  $extra1   Extra parameter that is just passed on
    * @param mixed  $extra2   Extra parameter that is just passed on
    *
    * @return object A reference to a table object
    */
    static private function &_historyFactory(
        &$system, $data, $class, &$connect, $extra1, $extra2
    ) {
        include_once dirname(__FILE__)."/History.php";
        include_once dirname(__FILE__)."/tables/history/".$class.".php";
        if (substr($class, 0, 17) != "HUGnet\\db\\tables\\") {
            $nclass = "HUGnet\\db\\tables\\".$class;
        }
        $interface1 = "\\HUGnet\\interfaces\\DBTable";
        $interface2 = "\\HUGnet\\interfaces\\DBTableHistory";
        if (!is_subclass_of($nclass, $interface1)
            || !is_subclass_of($nclass, $interface2)
        ) {
            include_once dirname(__FILE__)."/tables/history/EDEFAULTHistory.php";
            // Assume that the class given is the table name.
            $nclass = "\\HUGnet\\db\\tables\\EDEFAULTHistory";
        }
        return new $nclass($system, $data, $connect, $extra1, $extra2);
    }
    /**
    * This function creates other tables that are identical to this one, except
    * for the data given.
    *
    * @param object &$system  The system object to use
    * @param mixed  $data     This is an array or string to create the object from
    * @param string $class    The class to use
    * @param object &$connect The connection manager
    * @param mixed  $extra1   Extra parameter that is just passed on
    * @param mixed  $extra2   Extra parameter that is just passed on
    *
    * @return object A reference to a table object
    */
    static private function &_averageFactory(
        &$system, $data, $class, &$connect, $extra1, $extra2
    ) {
        include_once dirname(__FILE__)."/History.php";
        include_once dirname(__FILE__)."/Average.php";
        include_once dirname(__FILE__)."/FastAverage.php";
        include_once dirname(__FILE__)."/tables/average/".$class.".php";
        if (substr($class, 0, 17) != "HUGnet\\db\\tables\\") {
            $nclass = "HUGnet\\db\\tables\\".$class;
        }
        $interface1 = "\\HUGnet\\interfaces\\DBTable";
        $interface2 = "\\HUGnet\\interfaces\\DBTableAverage";
        if (!is_subclass_of($nclass, $interface1)
            || !is_subclass_of($nclass, $interface2)
        ) {
            include_once dirname(__FILE__)."/tables/average/EDEFAULTAverage.php";
            // Assume that the class given is the table name.
            $nclass = "\\HUGnet\\db\\tables\\EDEFAULTAverage";
        }
        return new $nclass($system, $data, $connect, $extra1, $extra2);
    }
    /**
    * This function gets a record with the given key
    *
    * @return bool True on success, False on failure
    */
    public function refresh()
    {
        $sqlId = $this->sqlId;
        return $this->getRow($this->get($sqlId));
    }
    /**
    * Creates the object from a string or array
    *
    * @param mixed $data This is whatever you want to give the class
    *
    * @return null
    */
    public function fromArray($data)
    {
        parent::fromArray($data);
        if (isset($data["group"])) {
            $this->default["group"] = $data["group"];
        }
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param string $string The CSV string to import
    *
    * @return null
    */
    public function fromCSV($string)
    {
        $values = explode(",", $string);
        foreach (array_keys((array)$this->sqlColumns) as $key => $col) {
            $this->set($col, trim($values[$key]));
        }
    }
    /**
    * This function gets a record with the given key
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return bool True on success, False on failure
    */
    public function selectOneInto($where, $data = array())
    {
        $ret = $this->selectInto($where, $data);
        $this->dbDriver()->reset();
        return $ret;
    }
    /**
    * This routine takes any date and turns it into an SQL date
    *
    * @param mixed  $value    The value to set
    * @param string $TimeZone The time zone to use.  Defaults to UTC
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedLocalVariable)
    */
    static public function sqlDate($value, $TimeZone = "UTC")
    {
        if (is_numeric($value)) {
            $value = gmdate("Y-m-d H:i:s", (int)$value);
        }
        try {
            $date = new \DateTime($value, new \DateTimeZone($TimeZone));
        } catch (\Exception $e) {
            return "1970-01-01 00:00:00";
        }
        return $date->format("Y-m-d H:i:s");
    }
    /**
    * This routine takes any date and turns it into an SQL date
    *
    * @param mixed  $value    The value to set
    * @param string $TimeZone The time zone to use.  Defaults to UTC
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedLocalVariable)
    */
    static public function unixDate($value, $TimeZone = "UTC")
    {
        if (is_numeric($value)) {
            return (int)$value;
        }
        try {
            $date = new \DateTime($value, new \DateTimeZone($TimeZone));
        } catch (\Exception $e) {
            return 0;
        }
        return (int)$date->format("U");
    }
    /**
    * This remove everything from the array but keys that are columns
    *
    * @param array $array The array to sanitize
    *
    * @return array
    */
    public function sanitizeWhere($array)
    {
        if (!is_array($array)) {
            return array();
        }
        foreach (array_keys($array) as $key) {
            if (!isset($this->sqlColumns[$key])) {
                unset($array[$key]);
            } else if (is_null($array[$key]) && !$this->sqlColumns[$key]["Null"]) {
                unset($array[$key]);
            }
        }
        return $array;
    }
    /**
    * Sets the extra attributes field
    *
    * @param int    $start      The start of the time
    * @param int    $end        The end of the time
    * @param mixed  $rid        The ID to use.  None if null
    * @param string $idField    The ID Field to use.  Table Primary id if left blank
    * @param string $extraWhere Extra where clause
    * @param array  $extraData  Data for the extraWhere clause
    *
    * @return mixed The value of the attribute
    */
    protected function getTimePeriod(
        $start,
        $end = null,
        $rid = null,
        $idField = null,
        $extraWhere = null,
        $extraData = null
    ) {
        // If date field doesn't exist return
        if (empty($this->dateField)) {
            return false;
        }
        if (is_null($idField)) {
            $idField = $this->sqlId;
        }
        // Make sure the start and end dates are in the correct form
        if (empty($end)) {
            $end = $start;
        }
        $end = self::unixDate($end);
        // Set up the where and data fields
        $where = array(
            $this->dateField => array(
                '$gte' => $start,
                '$lte' => $end
            )
        );
        if (!is_null($rid)) {
            $where[$idField] = $rid;
        }
        if (!empty($extraWhere) && is_array($extraWhere)) {
            $where = array_merge($where, $extraWhere);
        }
        return $this->selectInto(
            $where,
            array()
        );
    }
    /**
    * This upgrades the table to the current standard.
    *
    * @param bool $fake If true, we go through the motions, but don't do the upgrade
    *
    * @return null
    */
    public function upgrade($fake = true)
    {
        if (get_class($this) !== 'HUGnet\db\tables\Generic') {
            $diff = $this->diff();
            $this->dbdriver()->lock();
            $ret  = $this->_upgradeColumns($diff["column"], $fake);
            $ret &= $this->_upgradeIndexes($diff["index"], $fake);
            $this->dbdriver()->unlock();
            return (bool)$ret;
        }
        $this->system()->out(
            "Upgrading doesn't work with a generic class", 1
        );
        return false;
    }
    /**
    * This upgrades the table to the current standard.
    *
    * @param array $diff The difference
    *
    * @return null
    */
    private function _diffPrint($diff)
    {
        foreach ((array)$diff as $key => $value) {
            if (is_array($value)) {
                $value = "(".implode(", ", $value).")";
            } else if (is_bool($value)) {
                $value = ($value) ? "true" : "false";
            }
            $this->system()->out(
                " --> $key => $value", 1
            );
        }
    }
    /**
    * This upgrades the table to the current standard.
    *
    * @param array $diff The difference
    * @param bool  $fake If true, we go through the motions, but don't do the upgrade
    *
    * @return null
    */
    private function _upgradeColumns($diff, $fake = true)
    {
        if ($fake) {
            $fakeprint = "(What I would do)";
        } else {
            $fakeprint = "";
        }
        foreach ($diff as $name => $col) {
            switch ($col["type"]) {
            case "update":
                $this->system()->out(
                    "Upgrading column $name $fakeprint", 1
                );
                $this->_diffPrint($col["diff"]);
                if (!$fake) {
                    $this->dbdriver()->modifyColumn(
                        $this->sqlColumns[$name]
                    );
                }
                break;
            case "add":
                $this->system()->out(
                    "Adding column $name $fakeprint", 1
                );
                $this->_diffPrint($col["diff"]);
                if (!$fake) {
                    $this->dbdriver()->addColumn(
                        $this->sqlColumns[$name]
                    );
                }
                break;
            case "remove":
                $this->system()->out(
                    "Removing column $name $fakeprint", 1
                );
                $this->_diffPrint($col["diff"]);
                if (!$fake) {
                    $this->dbdriver()->removeColumn($name);
                }
                break;
            }
        }
        return true;
    }
    /**
    * This upgrades the table to the current standard.
    *
    * @param array $diff The difference
    * @param bool  $fake If true, we go through the motions, but don't do the upgrade
    *
    * @return null
    */
    private function _upgradeIndexes($diff, $fake = true)
    {
        if ($fake) {
            $fakeprint = "(What I would do)";
        } else {
            $fakeprint = "";
        }
        // Remove the bad ones first
        foreach ($diff as $name => $ind) {
            if ($ind["type"] == "remove") {
                $this->system()->out(
                    "Removing index $name $fakeprint", 1
                );
                $this->_diffPrint($ind["diff"]);
                if (!$fake) {
                    $this->dbdriver()->removeIndex($name);
                }
                break;
            }
        }
        // Now add the new ones
        foreach ($diff as $name => $ind) {
            if ($ind["type"] == "add") {
                $this->system()->out(
                    "Adding index ".$name."_".$this->sqlTable." $fakeprint", 1
                );
                $this->_diffPrint($ind["diff"]);
                if (!$fake) {
                    $this->dbdriver()->addIndex(
                        $ind["diff"]
                    );
                }
            }
        }
        return true;
    }
    /**
    * Checks the table in the database against the definition, and returns
    * the differences.
    *
    * @return null
    */
    public function diff()
    {
        return array(
            "column" => $this->_columnDiff(),
            "index"  => $this->_indexDiff(),
        );
    }
    /**
    * Checks the table in the database against the definition, and returns
    * the differences.
    *
    * @return null
    */
    private function _columnDiff()
    {
        $table = $this->dbdriver()->columns();
        $ret   = array();
        foreach ((array)$this->sqlColumns as $name => $col) {
            if (is_array($table[$name])) {
                $pos  = strpos($table[$name]["Type"], "(");
                $pos2 = strpos($col["Type"], "(");
                if (($pos !== false) && ($pos2 === false)) {
                    $table[$name]["Type"] = substr($table[$name]["Type"], 0, $pos);
                }
                if (($col["Type"] == "INTEGER") 
                    && ($table[$name]["Type"] == "int")
                ) {
                    $col["Type"] = "int";
                }
                $diff = array_diff_assoc($col, (array)$table[$name]);
                if (!empty($diff)) {
                    $ret[$name] = array(
                        "type" => "update",
                        "diff" => $diff,
                    );
                }
                unset($table[$name]);
            } else {
                $ret[$name] = array(
                    "type" => "add",
                    "diff" => $col,
                );
            }
        }
        foreach ($table as $col) {
            $ret[$col["Name"]] = array(
                "type" => "remove",
                "diff" => $col,
            );
        }
        return $ret;
    }
    /**
    * Checks the table in the database against the definition, and returns
    * the differences.
    *
    * @return null
    */
    private function _indexDiff()
    {
        $table = $this->dbdriver()->indexes();
        $ret   = array();
        foreach ((array)$this->sqlIndexes as $name => $ind) {
            $iname = $name."_".$this->sqlTable;
            if (!is_array($table[$iname])) {
                $ret[$name] = array(
                    "type" => "add",
                    "diff" => $ind,
                );
            } else {
                unset($table[$iname]);
            }
        }
        foreach ($table as $ind) {
            $ret[$ind["Name"]] = array(
                "type" => "remove",
                "diff" => $ind,
            );
        }
        return $ret;
    }
    /**
    * Checks the table in the database against the definition, and returns
    * the differences.
    *
    * @param array $index1 The first index to check
    * @param array $index2 The second index to check
    *
    * @return null
    */
    private function _indexSame($index1, $index2)
    {
        if ($index1["Unique"] != $index2["Unique"]) {
            return false;
        }
        $coldiff = array_diff_assoc(
            (array)$index1["Columns"], (array)$index2["Columns"]
        );
        if ($coldiff !== array()) {
            return false;
        }
        return true;
    }


}


?>
