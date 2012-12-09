<?php
/**
 * Abstract class for building SQL queries
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
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
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
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
            include_once dirname(__FILE__)."/History.php";
            include_once dirname(__FILE__)."/tables/history/".$class.".php";
        } else if (file_exists(dirname(__FILE__)."/tables/average/".$class.".php")) {
            include_once dirname(__FILE__)."/History.php";
            include_once dirname(__FILE__)."/Average.php";
            include_once dirname(__FILE__)."/FastAverage.php";
            include_once dirname(__FILE__)."/tables/average/".$class.".php";
        }
        if (substr($class, 0, 17) != "HUGnet\\db\\tables\\") {
            $nclass = "HUGnet\\db\\tables\\".$class;
        }
        if (!class_exists($nclass)) {
            include_once dirname(__FILE__)."/tables/Generic.php";
            // Assume that the class given is the table name.
            return new \HUGnet\db\tables\Generic(
                $system, $data, $connect, $class
            );
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
            $value = date("Y-m-d H:i:s", (int)$value);
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
        $where = "`".$this->dateField."` >= ? AND `".$this->dateField."` <= ?";
        $data = array($start, $end);
        if (!is_null($rid)) {
            $where .= " AND `".$idField."` = ?";
            $data[] = $rid;
        }
        if (!empty($extraWhere)) {
            $where .= " AND ".$extraWhere;
        }
        if (is_array($extraData)) {
            $data = array_merge($data, $extraData);
        }
        return $this->selectInto(
            $where,
            $data
        );
    }


}


?>
