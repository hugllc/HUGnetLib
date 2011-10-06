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
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Files
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once CODE_BASE."base/HUGnetContainer.php";
require_once CODE_BASE."interfaces/OutputInterface.php";
// Need to make sure this file is not added to the code coverage
PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

/**
 * This class keeps track of hooks that can be defined and used other places in the
 * code to cause custom functions to happen.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Files
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TestOutputContainer extends HUGnetContainer implements OutputInterface
{
    /** These are the endpoint information bits */
    /** @var array This is the default values for the data */
    protected $default = array(
        "group" => "default",
        "a" => null,
        "b" => null,
        "c" => null,
        "d" => null,
    );
    /** @var array This is the default column labels */
    protected $labels = array(
        "a" => "First Column",
        "c" => "Third",
        "d" => "Another Column",
    );
    /** @var array Array of data to use */
    var $myData = array(
        array(
            "group" => "default",
            "a" => 1,
            "b" => 2,
            "c" => 3,
            "d" => 4,
        ),
        array(
            "group" => "default",
            "a" => 4,
            "b" => 3,
            "c" => 2,
            "d" => 1,
        ),

    );
    /** @var int Index for where in the data array we are */
    protected $index = 0;
    /** @var int Index for where in the data array we are */
    public $outputParams = array();
    /**
    * This is the constructor
    *
    * @param mixed $data This is an array or string to create the object from
    */
    function __construct($data="")
    {
        parent::__construct($data);
    }
    /**
    * Sets all of the endpoint attributes from an array
    *
    * @param array $array This is an array of this class's attributes
    *
    * @return null
    */
    public function loadData($array)
    {

        if (!is_array($array[0])) {
            $array = array($array);
        }
        $this->myData = $array;
        $this->nextIntoP();
    }
    /**
    * This puts the next result into the object
    *
    * @return bool True on success, False on failure
    */
    protected function nextIntoP()
    {
        if (is_array($this->myData[$this->index])) {
            $this->fromArray($this->myData[$this->index]);
            $this->index++;
            return true;
        } else {
            return false;
        }
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $field The field to use
    * @param string $data  Data to preload
    * @param object $obj   The field to use
    *
    * @return string
    *
    * @dataProvider dataAddFunction
    */
    public function testAddFunction1($field, $data, $obj)
    {
        return $field."_".$data."_".get_class($obj)."_".get_class($this);
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $field The field to use
    * @param string $data  Data to preload
    * @param object $obj   The field to use
    *
    * @return string
    *
    * @dataProvider dataAddFunction
    */
    static public function testAddFunction2($field, $data, $obj)
    {
        return $data."_".$field."_".get_class($obj)."_static";
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
        return date($this->myConfig->dateFormat, $this->$field);
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
                $ret[$col] = (string)$this->$col;
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
    */
    public function outputFilters($cols = null)
    {
        return (array)$this->outputParams['filters'];
    }

}
?>
