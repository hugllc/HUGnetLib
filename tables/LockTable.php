<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Tables
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../base/HUGnetDBTable.php";
/** This is for the configuration */
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Tables
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class LockTable extends HUGnetDBTable
{
    /** @var string This is the table we should use */
    public $sqlTable = "locks";
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
    public $sqlColumns = array(
        "id" => array(
            "Name" => "id",
            "Type" => "varchar(64)",
        ),
        "type" => array(
            "Name" => "type",
            "Type" => "varchar(64)",
        ),
        "lockData" => array(
            "Name" => "lockData",
            "Type" => "varchar(255)",
            "Default" => '',
        ),
        "expiration" => array(
            "Name" => "expiration",
            "Type" => "bigint",
        ),
    );
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
    */
    public $sqlIndexes = array(
        "typedata" => array(
            "Name" => "typedata",
            "Unique" => true,
            "Columns" => array("type", "lockData"),
        ),
    );

    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",    // Server group to use
    );
    /**
    * This is the constructor
    *
    * @param mixed $data This is an array or string to create the object from
    */
    function __construct($data="")
    {
        // We prefer group = 'volatile' but it might not exist
        $config = &ConfigContainer::singleton();
        if ($config->servers->available("volatile")) {
            $this->default["group"] = "volatile";
        }
        parent::__construct($data);
        $this->create();
    }

    /**
    * Gets a lock
    *
    * @param int    $id   The id of the locking element
    * @param string $type The type of lock
    * @param string $data The data string
    *
    * @return true if lock successful, false otherwise
    */
    public function check($id, $type, $data)
    {
        $this->clearData();
        $this->selectOneInto(
            "`type` = ? AND `lockData` = ? AND expiration > ?",
            array($type, $data, $this->now())
        );
        return ((int)$id) === $this->id;
    }
    /**
    * Gets a lock
    *
    * @param int    $id       The id of the locking element
    * @param string $type     The type of lock
    * @param string $data     The data string
    * @param int    $timeLeft The amount of time left on the lock
    *
    * @return true if lock successful, false otherwise
    */
    public function place($id, $type, $data, $timeLeft)
    {
        $this->clearData();
        if (is_null($id) || is_null($type) || is_null($data) || empty($timeLeft)) {
            return false;
        }
        $check = $this->check($id, $type, $data);
        if ($this->isEmpty()) {
            $this->id = (int)$id;
            $this->type = $type;
            $this->lockData = $data;
            $this->expiration = $this->now() + (int)$timeLeft;
            $ret = $this->insertRow();
        } else if ($check) {
            $this->expiration = $this->now() + (int)$timeLeft;
            $ret = $this->updateRow(array("expiration"));
        } else {
            $ret = false;
        }
        return $ret;
    }
    /**
    * This is the constructor
    *
    * @param string $type The type of lock
    * @param int    $id   The id of the locking element
    *
    * @return id of locking element
    */
    public function getAllLocks($type, $id = null)
    {
        $where = "`type` = ? AND `expiration` > ?";
        $data = array($type, $this->now());
        if (!empty($id)) {
            $where .= " AND `id` = ?";
            $data[] = $id;
        }
        return $this->selectInto($where, $data);
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/
    /**
    * function to set LastHistory
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setExpiration($value)
    {
        if (!empty($value)) {
            $this->data["expiration"] = self::unixDate($value);
        } else {
            $this->data["expiration"] = null;
        }
    }
    /**
    * function to set LastHistory
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setId($value)
    {
        if (is_null($value)) {
            $this->data["id"] = null;
        } else {
            $this->data["id"] = (int)$value;
        }
    }
}
?>
