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
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once CODE_BASE."base/OutputPluginBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage Files
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class Test1OutputSample extends OutputPluginBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "Test1OutputSample",
        "Type" => "outputSample",
        "Class" => "Test1OutputSample",
        "Flags" => array("DEFAULT"),
    );
    /**
    * Returns the object as a string
    *
    * @param array $array The array of header information.
    *
    * @return string
    */
    public function row($array = array())
    {
        $this->text .= print_r($array, true);
    }

    /**
    * This function implements the output before the data
    *
    * @return String the text to output
    */
    public function pre()
    {
        return "pre";
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    public function post()
    {
        return "post";
    }
    /**
    * Returns the object as a string
    *
    * @param array $array The array of header information.
    *
    * @return string
    */
    public function header($array = array())
    {
        $this->text .= print_r($array, true);
    }

}
?>
