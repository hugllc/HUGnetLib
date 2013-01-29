<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/** This is our base class */
require_once dirname(__FILE__)."/DummyBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Stubs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DummyTable extends DummyBase
{
    /** @var string this is our table */
    public $sqlTable = "myTable";
    /** @var string This is the index of our table */
    public $sqlId = "id";
    /** @var This is our columns */
    public $sqlColumns = array(
        "id" => array(
            "Name" => "id",
            "Type" => "INTEGER",
            "Default" => 0,
            "AutoIncrement" => true,
        ),
        "name" => array("Name" => "name", "Type" => "varchar(32)", "Default" => ""),
        "value" => array("Name" => "value", "Type" => "float", "Default" => 0.0),
    );
    public $sqlIndexes = array(
        "stuff" => array(
            "Name" => "stuff",
            "Unique" => true,
            "Columns" => array("id", "value"),
        ),
    );

    /** @var This is our set values */
    protected static $set = array(
    );
    /** @var This is our returns */
    protected static $ret = array(
        "columns" => array(
            "id" => array(
                "Name" => "id",
                "Type" => "INTEGER",
                "Default" => 0,
                "AutoIncrement" => true,
            ),
            "name" => array(
                "Name" => "name", "Type" => "varchar(32)", "Default" => ""
            ),
            "value" => array("Name" => "value", "Type" => "float", "Default" => 0.0),
        ),
        "tables" => array(
            "myTable" => "myTable",
        ),
        "toArray" => array(
            "a1" => "2",
        ),
    );
    /** @var This is our returns */
    protected $class = "Table";
    /**
    * Overload the get attribute
    *
    * @param string $name This is the attribute to get
    *
    * @return mixed The value of the attribute
    */
    public function get($name)
    {
        $class = $this->class;
        $ret = parent::get($name);
        if (is_null($ret)) {
            if (isset(DummyBase::$set[$class])
                && isset(DummyBase::$set[$class]['set'])
            ) {
                foreach ((array)DummyBase::$set[$class]['set'] as $value) {
                    if ($value[0] == $name) {
                        $ret = $value[1];
                    }
                }
            }
        }
        return $ret;
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param bool $default Return items set to their default?
    *
    * @return null
    */
    public function toArray($default = true)
    {
        $class = $this->class;
        if (isset(DummyBase::$ret[$class])
            && isset(DummyBase::$ret[$class]["toArray"])
            && isset(DummyBase::$ret[$class]["toArray"]["iterations"])
            && is_array(DummyBase::$ret[$class]["toArray"]["iterations"])
        ) {
            return array_shift(DummyBase::$ret[$class]["toArray"]["iterations"]);
        } else {
            return parent::toArray($default);
        }
    }
    /**
    * This puts the next result into the object
    *
    * @return bool True on success, False on failure
    */
    public function nextInto()
    {
        $class = $this->class;
        if (isset(DummyBase::$ret[$class])
            && isset(DummyBase::$ret[$class]["nextInto"])
            && is_array(DummyBase::$ret[$class]["nextInto"])
        ) {
            return array_shift(DummyBase::$ret[$class]["nextInto"]);
        } else {
            return parent::nextInto($default);
        }

    }
}
?>
