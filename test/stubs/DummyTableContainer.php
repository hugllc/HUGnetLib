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
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
// Need to make sure this file is not added to the code coverage
PHPUnit_Util_Filter::addFileToFilter(__FILE__);
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/HUGnetClass.php";
require_once dirname(__FILE__)."/../../base/HUGnetContainer.php";
//require_once dirname(__FILE__)."/../../interfaces/HUGnetSocketInterface.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DummyTableContainer
{
    /** @var string this is our table */
    public $sqlTable = "myTable";
    /** @var string This is the index of our table */
    public $sqlId = "id";
    /** @var This is our columns */
    public $sqlColumns = array(
        "id" => array("Name" => "id", "Type" => "int", "Default" => 0),
        "name" => array("Name" => "id", "Type" => "varchar(32)", "Default" => ""),
        "value" => array("Name" => "id", "Type" => "float", "Default" => 0.0),
    );
    public $sqlIndxes = array(
        "stuff" => array(
            "Name" => "stuff",
            "Unique" => true,
            "Columns" => array("id", "value"),
        ),
    );
    public $id;
    public $name;
    public $value;

    /**
    * This is the constructor
    *
    * @param mixed $data This is an array or string to create the object from
    */
    function __construct($data=array())
    {
        $this->fromArray($data);
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
        if (empty($array)) {
            return;
        }
        $this->id = $array["id"];
        $this->name = $array["name"];
        $this->value = $array["value"];
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
        foreach (array("id", "name", "value") as $k) {
            if (!is_null($this->$k)) {
                $row[$k] = $this->$k;
            }
        }
        return $row;
    }

}
?>
