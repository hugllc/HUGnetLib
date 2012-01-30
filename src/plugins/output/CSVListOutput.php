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
 * @package    HUGnetLib
 * @subpackage PluginsOutput
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/OutputPluginBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage PluginsOutput
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class CSVListOutput extends OutputPluginBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "CSVListOutput",
        "Type" => "output",
        "Class" => "CSVListOutput",
        "Flags" => array("CSVList"),
    );
    /** @var  These are the graph colors that will be used, in order */
    public $params = array(
        "separator" => ",",
        "eol" => "\r\n",
    );
    /** @var This is where our text is stored */
    protected $text = "";
    /**
    * Returns the object as a string
    *
    * @param array $array The data array for the row
    *
    * @return string
    */
    public function row($array = array())
    {
        $sep = "";
        foreach (array_keys((array)$this->header) as $key) {
            $value = $array[$key];
            if (!is_numeric($value)) {
                $value = '"'.strip_tags($value).'"';
            }
            $this->text .= $sep.html_entity_decode($value, ENT_COMPAT, "UTF-8");
            $sep = $this->params["separator"];
        }
        $this->text .= $this->params["eol"];
    }

    /**
    * This function implements the output before the data
    *
    * @return String the text to output
    */
    public function pre()
    {
        return "";
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    public function post()
    {
        return "";
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
        $sep = "";
        $this->setHeader($array);
        foreach ($this->header as $val) {
            $val = '"'.strip_tags($val).'"';
            $this->text .= $sep.html_entity_decode($val, ENT_COMPAT, "UTF-8");
            $sep = $this->params["separator"];
        }
        $this->text .= $this->params["eol"];
    }

}
?>
