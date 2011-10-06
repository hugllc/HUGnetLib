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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    0.9.7
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
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HTMLListOutput extends OutputPluginBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "HTMLListOutput",
        "Type" => "output",
        "Class" => "HTMLListOutput",
        "Flags" => array("DEFAULT"),
    );
    /** @var  These are the graph colors that will be used, in order */
    public $params = array(
        "tableStyle" => "",
        "rowStyle" => array('class="row1"', 'class="row2"'),
        "headerRowStyle" => "",
        "dataStyle" => array(
            0 => array(),
            1 => array(),
            "DEFAULT" => 'align="center"'
        ),
        "headerStyle" => array("DEFAULT" => 'align="center"'),
        "headerTag" => "th",
    );
    /** @var This is the row index.  It oscilates between 0 and 1. */
    private $_row = 1;

    /**
    * Returns the object as a string
    *
    * @param array $array the data array
    *
    * @return string
    */
    public function row($array = array())
    {
        $this->_row = 1 - $this->_row;
        $this->text .= "    <tr ".$this->params["rowStyle"][$this->_row].">\n";
        foreach (array_keys((array)$this->header) as $key) {
            $this->text .= "        <td ".$this->_dataStyle($key).">";
            $this->text .= $array[$key];
            $this->text .= "</td>\n";
        }
        $this->text  .= "    </tr>\n";
    }

    /**
    * This function implements the output before the data
    *
    * @return String the text to output
    */
    public function pre()
    {
        $text = "<table ".$this->params["tableStyle"].">\n";
        return $text;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    public function post()
    {
        $text = "</table>\n";
        return $text;
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
        $this->text .= "    <tr ".$this->params["headerRowStyle"].">\n";
        $this->setHeader($array);
        foreach ($this->header as $key => $val) {
            $this->text .= "        <".$this->params["headerTag"];
            $this->text .= " ".$this->_headerStyle($key).">";
            $this->text .= $val;
            $this->text .= "</".$this->params["headerTag"].">\n";
        }
        $this->text  .= "    </tr>\n";
    }

    /**
    * This function implements the output after the data
    *
    * @param string $field The data field to have the style for
    *
    * @return String the text to output
    */
    private function _dataStyle($field)
    {
        if (isset($this->params["dataStyle"][$this->_row][$field])) {
            return $this->params["dataStyle"][$this->_row][$field];
        } else if (isset($this->params["dataStyle"][$this->_row]["DEFAULT"])) {
            return $this->params["dataStyle"][$this->_row]["DEFAULT"];
        } else if (isset($this->params["dataStyle"][$field])) {
            return $this->params["dataStyle"][$field];
        }
        return $this->params["dataStyle"]["DEFAULT"];
    }
    /**
    * This function implements the output after the data
    *
    * @param string $field The data field to have the style for
    *
    * @return String the text to output
    */
    private function _headerStyle($field)
    {
        if (isset($this->params["headerStyle"][$field])) {
            return $this->params["headerStyle"][$field];
        }
        return $this->params["headerStyle"]["DEFAULT"];
    }

}
?>
