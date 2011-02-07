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
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/OutputPluginBase.php";
require_once dirname(__FILE__)."/../../base/HUGnetDBTable.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class FlotDatLinOutput extends OutputPluginBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "FlotDatLinOutput",
        "Type" => "output",
        "Class" => "FlotDatLinOutput",
        "Flags" => array("FlotDatLin"),
    );
    /** @var  These are the graph colors that will be used, in order */
    public $params = array(
        "width" => 600,
        "height" => 500,
        "doLegend" => true,
        "units" => array(1 => "", 2 => ""),
        "unitTypes" => array(1 => "", 2 => ""),
        "dateField" => "Date",
        "fields" => array(
            1 => array(),
            2 => array(),
        ),
        "title" => "",
        "tag" => "placeholder",
        "legendTag" => "legend",
    );
    /** @var This is the data to graph */
    protected $graphData = array();
    /** @var This is the dates for the graph */
    protected $graphDates = array();
    /** @var This is the graph class */
    protected $graph = null;
    /** @var This is whether or not we have a second y axis */
    protected $y2 = false;
    
    /**
    * Returns the object as a string
    *
    * @param array $array the data array
    *
    * @return string
    */
    public function row($array = array())
    {
        $dateField = &$this->params["dateField"];
        if (count((array)$array) == 0) {
            return;
        }
        // We need the date in miliseconds since epoc
        $date = HUGnetDBTable::unixDate($array[$dateField]) * 1000;
        foreach ((array) $this->params["fields"] as $line => $val) {
            foreach ((array) $val as $field) {
                if (!is_null($array[$field])) {
                    $this->graphData[$line][$field][$date] = (float)$array[$field];
                } else {
                    $this->graphData[$line][$field][$date] = null;
                }
            }
        }
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
        $this->setHeader($array);
    }

    /**
    * This function implements the output after the data
    *
    * @param int    $line  The graph line to use
    * @param string $field The field to use in that line
    * 
    * @return String the text to output
    */
    private function _createLine($line, $field)
    {
        $ret = "    var $field = [";
        $sep = "";
        ksort($this->graphData[$line][$field]);
        foreach ($this->graphData[$line][$field] as $date => $value) {
            $ret .= $sep."[$date, $value]";
            $sep = ", ";
        }

        $ret .= "];\n";
        return $ret;
    }
    
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createXaxis()
    {
        return "            xaxis: {
                mode: 'time',
                label: 'Test',
                timeformat: '%y/%m/%d %H:%M:%S'
            },\n";
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createLegend()
    {
        return "            legend: {
                position: 'nw',
                container: '#".$this->params["legendTag"]."'
            }\n";
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createPlot()
    {
        $ret .= '    $.plot($("#'.$this->params["tag"].'"),'."\n".'       [';
        $sep = "";
        foreach ((array) $this->params["fields"][1] as $field) {
            $ret .= $sep."{ data: $field, label: \"".$this->header[$field]."\" }";
            $sep = ", ";
        }
        foreach ((array) $this->params["fields"][2] as $field) {
            $ret .= $sep."{ data: $field, label: \"";
            $ret .= $this->header[$field]."\" , yaxis: 2}";
            $sep = ", ";
        }
        $ret .= "],\n";
        $ret .= "        {\n";
        $ret .= $this->_createXaxis();
        $ret .= $this->_createLegend();
        $ret .= "    });\n";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    public function body()
    {
        $ret  = '<script id="source" language="javascript" type="text/javascript">';
        $ret .= "\n\$(function () {\n";
        foreach ($this->graphData as $line => $fields) {
            foreach (array_keys($fields) as $field) {
                $ret .= $this->_createLine($line, $field);
            }
        }
        $ret .= $this->_createPlot();
        $ret .= "});\n";
        $ret .= "</script>\n";
        return $ret;
    }

}

?>
