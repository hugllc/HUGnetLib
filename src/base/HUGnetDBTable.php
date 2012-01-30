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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** require our base class */
require_once dirname(__FILE__)."/HUGnetClass.php";
/** require our base class */
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";
/** require our base class */
require_once dirname(__FILE__)."/HUGnetContainer.php";
/** require our base class */
require_once dirname(__FILE__)."/../interfaces/OutputInterface.php";
/** require our base class */
require_once dirname(__FILE__)."/../interfaces/IteratorInterface.php";
/**
 * Base class for all database work
 *
 * This class uses the {@link http://www.php.net/pdo PDO} extension to php.  This
 * is a query building class.  That is just about all that it does.  It is abstract
 * because a class should be built for each pdo driver.  These are generally very
 * small.  This class will be used by the table classes to query the database.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Base
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
abstract class HUGnetDBTable extends HUGnetContainer
    implements OutputInterface, IteratorInterface, Serializable
{
    /** @var int This is where we store the limit */
    public $sqlLimit = 0;
    /** @var int This is where we store the start */
    public $sqlStart = 0;
    /** @var string The orderby clause for this table */
    public $sqlOrderBy = "";

    /** @var string This is the table we should use */
    public $sqlTable = "";
    /** @var string This is the primary key of the table.  Leave blank if none  */
    public $sqlId = "";
    /**
    * @var array This is the definition of the columns
    *
    * This should consist of the following structure:
    * array(
    *   "name" => array(
    *       "Name"          => string The name of the column
    *       "Type"          => string The type of the column
    *       "Default"       => mixed  The default value for the column
    *       "Null"          => bool   true if null is allowed, false otherwise
    *       "AutoIncrement" => bool   true if the column is auto_increment
    *       "CharSet"       => string the character set if the column is text or char
    *       "Collate"       => string colation if the table is text or char
    *       "Primary"       => bool   If we are a primary Key.
    *       "Unique"        => bool   If we are a unique column.
    *   ),
    *   "name2" => array(
    *   .
    *   .
    *   .
    * );
    *
    * Not all fields have to be filled in.  Name and Type are the only required
    * fields.  The index of the base array should be the same as the "Name" field.
    */
    public $sqlColumns = array();
    /**
    * @var array This is the definition of the indexes
    *
    *   array(
    *       "Name" => array (
    *           "Name"    => string The name of the index
    *           "Unique"  => bool   Create a Unique index
    *           "Columns" => array  Array of column names
    *       ),
    *       "name2" => array(
    *       .
    *       .
    *   ),
    *
    *  To add a length to the column, simply add a comma and then the length to
    *  the column name in the "Columns" array.  For a column named "colName" with
    *  a index length of 15 would be:
    *
    *  "colName,15"
    */
    public $sqlIndexes = array();

    /** @var object This is where we store our sqlDriver */
    private $_driver = null;
    /** @var object This is where we store our configuration object */
    protected $myConfig = null;
    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
    );
    /** @var The labels for the output columns */
    protected $labels = array(
    );
    /** @var This is the date field for this record */
    public $dateField = null;
    /** @var This is the output parameters */
    protected $outputParams = array();

    /**
    * This is the constructor
    *
    * @param mixed $data This is an array or string to create the object from
    */
    function __construct($data="")
    {
        $this->setupColsDefault();
        parent::__construct($data);
        // Get our config.
        $this->myConfig = &ConfigContainer::singleton();
        $this->verbose($this->myConfig->verbose);
    }
    /**
    * This function returns a reference to the database driver.
    *
    * @return Database Driver object reference
    */
    protected function &dbDriver()
    {
        if (!is_object($this->_driver)) {
            if (is_object($this->myConfig->servers)) {
                $this->_driver = &$this->myConfig->servers->getDriver(
                    $this,
                    $this->group
                );
            }
            if (!is_object($this->_driver)) {
                $this->throwException(
                    "No available database connection available in group '"
                    .$this->group."'.  Check your database configuration.", -2
                );
                // @codeCoverageIgnoreStart
                // It thinks this line won't run.  The above function never returns.
            } else {
                // @codeCoverageIgnoreEnd
                $this->_driver->verbose($verbose);
            }
        }
        return $this->_driver;
    }
    /**
    * This is the destructor
    */
    public function __destruct()
    {
        unset($this->_driver);
        unset($this->myConfig);
    }
    /**
    * This serializes the object without the PDO connection
    *
    * @return string The serialized object
    */
    public function serialize()
    {
        return (string)serialize($this->toArray());
    }
    /**
    * This unserializes the object
    *
    * @param array $data The data to use to unserialize this class
    *
    * @return null
    */
    public function unserialize($data)
    {
        $this->setupColsDefault();
        $this->clearData();
        $this->fromArray(unserialize($data));
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @return null
    */
    protected function setupColsDefault()
    {
        // This loads any columns that are not already in $this->default into
        // that array so that they will be picked up by the database.  This must
        // happen before any calls to the parent constructor or to from***().
        foreach ((array)$this->sqlColumns as $col) {
            if (!isset($this->default[$col["Name"]])) {
                $this->default[$col["Name"]] = $col["Default"];
            }
        }
        $this->clearData();
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function fromArray($array)
    {
        parent::fromArray($array);
        // Set the new default group.  This is for when the data is cleared
        // The group will remain the same.
        $this->default["group"] = $this->group;

    }

    /**
    * Returns an array with only the values the database cares about
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function toDB($default = true)
    {
        foreach ((array)$this->sqlColumns as $col) {
            $key = $col["Name"];
            if (is_object($this->$key)) {
                if (method_exists($this->$key, "toString")) {
                    $array[$col["Name"]] = $this->$key->toString();
                }
            } else {
                $array[$col["Name"]] = $this->$key;
            }
        }
        return (array)$array;
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
            $this->$col = trim($values[$key]);
        }
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
    public function getPeriod(
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
        $start = self::unixDate($start);
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
    /**
    * This function gets a record with the given key
    *
    * @param mixed $key This is either an array or a straight value
    *
    * @return bool True on success, False on failure
    */
    public function getRow($key)
    {
        if (!is_array($key)) {
            $key = array($this->sqlId => $key);
        }
        $ret = $this->dbDriver()->selectWhere($key);
        $this->dbDriver()->fetchInto();
        $this->dbDriver()->reset();
        return $ret;
    }
    /**
    * This function gets a record with the given key
    *
    * @return bool True on success, False on failure
    */
    public function refresh()
    {
        $sqlId = $this->sqlId;
        return $this->getRow($this->$sqlId);
    }
    /**
    * This function updates the record currently in this table
    *
    * @param array $columns The columns to update, defaults to all
    *
    * @return bool True on success, False on failure
    */
    public function updateRow($columns = array())
    {
        if ($this->isEmpty()) {
            return false;
        }
        $ret = $this->dbDriver()->updateOnce($this->toDB(), "", array(), $columns);
        $this->dbDriver()->reset();
        return $ret;
    }
    /**
    * This function updates the record currently in this table
    *
    * @param bool $replace Replace any records found that collide with this one.
    *
    * @return bool True on success, False on failure
    */
    public function insert($replace = false)
    {
        if ($this->isEmpty()) {
            return false;
        }
        $sqlId = $this->sqlId;
        if ($this->default[$this->sqlId] === $this->$sqlId) {
            $cols = $this->dbDriver()->autoIncrement();
        }
        $ret = $this->dbDriver()->insert($this->toDB(), (array)$cols, $replace);
        return $ret;
    }
    /**
    * This function updates the record currently in this table
    *
    * @return bool True on success, False on failure
    */
    public function insertEnd()
    {
        $this->dbDriver()->reset();
    }
    /**
    * This function updates the record currently in this table
    *
    * @param bool $replace Replace any records found that collide with this one.
    *
    * @return bool True on success, False on failure
    */
    public function insertRow($replace = false)
    {
        $this->dbDriver()->reset();
        $ret = $this->insert($replace);
        $this->insertEnd();
        return $ret;
    }

    /**
    * This function updates the record currently in this table
    *
    * @return bool True on success, False on failure
    */
    public function deleteRow()
    {
        if ($this->isEmpty()) {
            return false;
        }
        $ret = $this->dbDriver()->deleteWhere($this->toDB());
        $this->dbDriver()->reset();
        return $ret;
    }

    /**
    * This function updates the record currently in this table
    *
    * @return bool True on success, False on failure
    */
    public function create()
    {
        $ret = $this->dbDriver()->createTable();
        if ($ret) {
            foreach ((array)$this->sqlIndexes as $index) {
                $this->dbDriver()->addIndex($index);
            }
        }
        $this->dbDriver()->reset();
        return $ret;
    }
    /**
    * This function gets a record with the given key
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return array Array of objects
    */
    public function &select($where, $data = array())
    {
        $this->dbDriver()->selectWhere($where, $data);
        $ret = $this->dbDriver()->fetchAll();
        $this->dbDriver()->reset();
        return $ret;
    }
    /**
    * Returns the number of records this query would return
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return false on failure, int on success
    */
    public function count($where, $data = array())
    {
        return $this->dbDriver()->countWhere($where, $data);
    }
    /**
    * This function gets a record with the given key
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return array Array of objects
    */
    public function selectIDs($where, $data = array())
    {
        $this->dbDriver()->selectWhere(
            $where,
            $data,
            array($this->sqlId => $this->sqlId)
        );
        $res = $this->dbDriver()->fetchAll(PDO::FETCH_ASSOC);
        $this->dbDriver()->reset();
        $ret = array();
        foreach ((array)$res as $r) {
            $ret[$r[$this->sqlId]] = $r[$this->sqlId];
        }
        return $ret;
    }
    /**
    * This function gets a record with the given key
    *
    * @param string $where The where clause
    * @param array  $data  The data to use with the where clause
    *
    * @return bool True on success, False on failure
    */
    public function selectInto($where, $data = array())
    {
        $this->dbDriver()->selectWhere($where, $data);
        return $this->nextInto();
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
    * This puts the next result into the object
    *
    * @return bool True on success, False on failure
    */
    public function nextInto()
    {
        $this->clearData();
        $ret = $this->dbDriver()->fetchInto();
        if ($ret === false) {
            $this->dbDriver()->reset();
        }
        return $ret;
    }
    /**
    * This function creates other tables that are identical to this one, except
    * for the data given.
    *
    * @param array &$data The data to populate in the new class.
    *
    * @return object A reference to a table object
    */
    public function &factory(&$data = array())
    {
        $class = get_class($this);
        return new $class($data);
    }
    /**
    * This function creates other tables that are identical to this one, except
    * for the data given.
    *
    * @param int $verbose The verbose number to use
    *
    * @return object A reference to a table object
    */
    public function verbose($verbose)
    {
        parent::verbose($verbose);
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
            $date = new DateTime($value, new DateTimeZone($TimeZone));
        } catch (exception $e) {
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
            $date = new DateTime($value, new DateTimeZone($TimeZone));
        } catch (exception $e) {
            return 0;
        }
        return (int)$date->format("U");
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    */
    protected function getOutputCols($cols = null)
    {
        if (!is_array($cols) || empty($cols)) {
            if (empty($this->labels)) {
                $cols = array_keys($this->default);
            } else {
                $cols = array_keys($this->labels);
            }
        }
        return $cols;
    }
    /**
    * By default it outputs the date in the format specified in myConfig
    *
    * @param string $field The field to output
    *
    * @return string The date as a formatted string
    */
    protected function outputDate($field)
    {
        return $this->$field;
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    */
    public function toOutput($cols = null)
    {
        $cols = $this->getOutputCols($cols);
        $ret = array();
        foreach ($cols as $col) {
            if ($col == $this->dateField) {
                $ret[$col] = $this->outputDate($col);
            } else {
                $ret[$col] = $this->$col;
            }
        }
        return $ret;
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    */
    public function toOutputHeader($cols = null)
    {
        if (!is_array($cols) || empty($cols)) {
            if (empty($this->labels)) {
                $cols = array_keys($this->default);
            } else {
                $cols = array_keys($this->labels);
            }
        }
        $ret = array();
        foreach ($cols as $col) {
            if (isset($this->labels[$col])) {
                $ret[$col] = $this->labels[$col];
            } else {
                $ret[$col] = $col;
            }
        }
        return $ret;
    }
    /**
    * There should only be a single instance of this class
    *
    * @param string $type The output plugin type
    * @param array  $cols The columns to get
    *
    * @return array
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function outputParams($type, $cols = null)
    {
        return (array)$this->outputParams[$type];
    }
    /**
    * There should only be a single instance of this class
    *
    * @param array $cols The columns to get
    *
    * @return array
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function outputFilters($cols = null)
    {
        return (array)$this->outputParams['filters'];
    }

    /**
    * This sets the labels, or gets them if no argument
    *
    * @param array $cols The columns with their labels
    *
    * @return array
    */
    public function labels($cols = null)
    {
        if (is_array($cols)) {
            $this->labels = $cols;
        }
        return $this->labels;
    }

}


?>
