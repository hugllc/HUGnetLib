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
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../../base/HUGnetDBTable.php";
// Need to make sure this file is not added to the code coverage
PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

/**
 * This class keeps track of hooks that can be defined and used other places in the
 * code to cause custom functions to happen.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Containers
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class TestOutputContainer extends HUGnetDBTable
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
        $this->nextInto();
    }
    /**
    * This puts the next result into the object
    *
    * @return bool True on success, False on failure
    */
    public function nextInto()
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

}
?>
