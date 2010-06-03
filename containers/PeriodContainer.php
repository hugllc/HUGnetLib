<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
//require_once dirname(__FILE__)."/../base/HUGnetContainer.php";
require_once dirname(__FILE__)."/../containers/ConfigContainer.php";

/**
 * This class keeps track of hooks that can be defined and used other places in the
 * code to cause custom functions to happen.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class PeriodContainer extends HUGnetContainer
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",     // The database group we are in
        "start" => 0,             // The start date in unix timestamp format
        "end" => 0,               // The end date in unix timestamp format
        "class"     => "",        // The class each record is
        "dateField" => "Date",    // The name of the date field in the records
        "records" => array(),     // The records we have
    );
    /** @var array This is where the data is stored */
    protected $auto = false;
    /**
    * Builds the class
    *
    * @param array $data The data to build the class with
    *
    * @return null
    */
    public function __construct($data = array())
    {
        parent::__construct($data);
    }
    /**
    * Clears the data
    *
    * @return null
    */
    public function clearData()
    {
        // If data is going to be cleared we can unlock the fields
        $this->unlock(array("dateField", "class"));
        return parent::clearData();
    }
    /**
    * Sets the extra attributes field
    *
    * @param bool $set True turns auto on, false turns it off
    *
    * @return null
    */
    public function autoRetrieve($set = true)
    {
        $this->auto = $set;
        if ($this->auto) {
            $this->getPeriod();
        }
    }
    /**
    * Sets the extra attributes field
    *
    * @param int $start The start of the time
    * @param int $end   The end of the time
    *
    * @return mixed The value of the attribute
    */
    public function getPeriod($start = null, $end = null)
    {
        $start   = (is_null($start)) ? $this->start : $start;
        $end     = (is_null($end)) ? $this->end : $end;
        $class   = $this->class;
        $myClass = new $class(array("group" => $this->group));
        $records = $myClass->select(
            "`".$this->dateField."` >= ? AND `".$this->dateField."` <= ?",
            array($start, $end)
        );
        foreach (array_keys((array)$records) as $k) {
            $this->insertRecord($records[$k]);
        }
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
        foreach ($this->getProperties() as $attrib) {
            if (isset($array[$attrib]) && ($attrib != "records")) {
                $this->$attrib = $array[$attrib];
            }
        }
        $this->lock(array("dateField", "class"));
        $this->records = array();
        foreach (array_keys((array)$array["records"]) as $key) {
            $this->insertRecord($array["records"][$key]);
        }
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array &$array This is a array to make a new class, or an object
    *
    * @return null
    */
    public function insertRecord(&$array)
    {
        $class = $this->class;
        $field = $this->dateField;
        if (is_array($array)) {
            if (isset($array[$field])
                && ($array[$field] <= $this->end) && ($array[$field] >= $this->start)
            ) {
                if (!is_a($this->records[$array[$field]], $this->class)) {
                    $this->records[$array[$field]] = new $class($array);
                }
            }
        } else if (is_a($array, $this->class)) {
            if (($array->$field <= $this->end) && ($array->$field >= $this->start)) {
                if (!is_a($this->records[$array->$field], $this->class)) {
                    $this->records[$array->$field] = &$array;
                }
            }
        }
        ksort($this->records);
    }
    /**
    * Tries to run the function on every record
    *
    * @param string $name The name of the function to call
    * @param array  $args The array of arguments
    *
    * @return mixed
    */
    public function __call($name, $args)
    {
        if (method_exists($this->class, $name)) {
            foreach (array_keys($this->records) as $key) {
                $code  ='return $this->records['.$key.']->'.$name.'(';
                if (count($args) > 0) {
                    $code .= '$args['.implode('], $args[', array_keys($args)).']';
                }
                $code .= ');';
                $output[$key] = eval($code);
            }
            return $output;
        }
        return false;
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

    /**
    * function to set class
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setClass($value)
    {
        if ($this->findClass($value, "tables")) {
            $this->data["class"] = $value;
        }
    }

    /**
    * function to set class
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setStart($value)
    {
        $old = $this->data["start"];
        $this->data["start"] = (int)$value;
        if ($old < $value) {
            // This truncates if we are smaller
            foreach (array_keys((array)$this->records) as $key) {
                if ($key < $this->data["start"]) {
                    unset($this->records[$key]);
                } else {
                    break;
                }
            }
        } else if ($this->auto && ($old > $value)) {
            // This adds if we are bigger
            $this->getPeriod($value, $old);
        }
    }
    /**
    * function to set class
    *
    * @param string $value The value to set
    *
    * @return null
    */
    protected function setEnd($value)
    {
        $old = $this->data["end"];
        $this->data["end"] = (int)$value;
        if ($old > $value) {
            // This truncates if we are smaller
            foreach (array_reverse(array_keys((array)$this->records)) as $key) {
                if ($key > $this->data["end"]) {
                    unset($this->records[$key]);
                } else {
                    break;
                }
            }
        } else if ($this->auto && ($old < $value)) {
            // This adds if we are bigger
            $this->getPeriod($old, $value);
        }
    }

}
?>
