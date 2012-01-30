<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Files
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once CODE_BASE."base/UnitsBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Files
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class TestUnitsSample extends UnitsBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "TestUnitsSample",
        "Type" => "UnitsSample",
        "Class" => "TestUnitsSample",
        "Flags" => array("firstUnit", "testUnit"),
    );
    /** @var The units that are valid for conversion */
    protected $valid = array("firstUnit", "testUnit");
    /** @var The units of this point */
    public $to = "testUnit";

    /**
    * Sets everything up
    *
    * @param array $data The data to start with
    *
    * @return null
    */
    public function __construct($data)
    {
        parent::__construct($data);
    }
    /**
    * Does the actual conversion
    *
    * @param mixed  &$data The data to convert
    * @param string $to    The units to convert to
    * @param string $from  The units to convert from
    *
    * @return mixed The value returned
    */
    public function convert(&$data, $to=null, $from=null)
    {
        if (($to == "firstUnit") && ($from == "testUnit")) {
            $data = $data / 2;
        } else if (($to == "testUnit") && ($from == "firstUnit")) {
            $data = $data * 2;
        } else {
            return false;
        }
        return true;
    }


}
?>
