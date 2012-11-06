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
 * @package    HUGnetLib
 * @subpackage PluginsOutput
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
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
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
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
        "doLegend" => true,
        "units" => array(1 => "", 2 => ""),
        "unitTypes" => array(1 => "", 2 => ""),
        "dateField" => "Date",
        "margin" => array(
            "top"    => 20,
            "bottom" => 60,
            "left"   => 50,
            "right"  => 50,
        ),
        "width" => 600,
        "height" => 500,
        "fields" => array(
            1 => array(),
            2 => array(),
        ),
        "title" => "",
        "tag" => "placeholder",
        "legendTag" => "legend",
        "doToolTip" => true,
        "doZoom" => true,
        "doPan" => true,
        "doSelect" => true,
        "background" => "#EEE",
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
        $date = (int)$array[$dateField] * 1000;
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
        $ret = "[";
        $sep = "";
        ksort($this->graphData[$line][$field]);
        foreach ($this->graphData[$line][$field] as $date => $value) {
            $ret .= $sep."[$date, $value]";
            $sep = ", ";
        }

        $ret .= "]";
        return $ret;
    }

    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createXaxis()
    {
        $ret  = "xaxis: {";
        $ret .= " mode: 'time'";
        $ret .= ", label: 'Test'";
        $ret .= ", timeformat: '%m/%d %y %H:%M'";
        $ret .= " }";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createLegend()
    {
        $ret  = "legend: {";
        $ret .= " position: 'nw'";
        $ret .= ", container: '#".$this->params["legendTag"]."'";
        $ret .= ", noColumns: 3";
        $ret .= " }";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createSelection()
    {
        $ret  = "selection: { ";
        if ($this->params["doSelect"]) {
            $ret .= "mode: 'x'";
        }
        $ret .= " }";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createOptions()
    {
        $ret  = "    var options = {
        ".$this->_createXaxis().",
        ".$this->_createLegend().",
        ".$this->_createSelection().",
        ".$this->_createGrid().",
        ".$this->_createZoom().",
        ".$this->_createPan()."
    };\n";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createData()
    {
        $ret  = "    var data = [";
        $sep = "";
        foreach ((array) $this->params["fields"] as $line => $fields) {
            foreach ((array) $fields as $field) {
                $ret .= $sep."\n        {";
                $ret .= "\n            data: ".$this->_createLine($line, $field);
                $ret .= ",\n            label: '".$this->header[$field]."'";
                $ret .= ",\n            yaxis: ".$line;
                $ret .= "\n        }";
                $sep = ",";
            }
        }
        $ret .= "\n    ];\n";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createPlaceholder()
    {
        $ret  = "    var placeholder = $('#".$this->params["tag"]."');\n";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createZoom()
    {
        $ret .= "zoom: { ";
        if ($this->params["doZoom"]) {
            $ret .= "interactive: true";
        }
        $ret .= " }";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createGrid()
    {
        $ret .= "grid: { ";
        $ret .= "backgroundColor: '".$this->params["background"]."'";
        if ($this->params["doToolTip"]) {
            $ret .= ", hoverable: true";
        }
        $ret .= " }";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createPan()
    {
        $ret .= "pan: { ";
        if ($this->params["doPan"]) {
            $ret .= "interactive: true";
        }
        $ret .= " }";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createPlot()
    {
        $ret .= 'var plot = $.plot(placeholder, data, options);';
        $ret .= "\n";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createHover()
    {
        if (!$this->params["doToolTip"]) {
            return "";
        }
        $ret  = "    function showTooltip(x, y, contents) {
        $('<div id=\"tooltip\">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 10,
            left: x + 10,
            border: '1px solid #fdd',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo(\"body\").fadeIn(200);
    }
    var previousPoint = null;
    $('#".$this->params["tag"]."').bind('plothover', function (event, pos, item) {
        if (item) {
            if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;
                $('#tooltip').remove();
                var x = item.datapoint[0].toFixed(2),
                   y = item.datapoint[1].toFixed(2);
                showTooltip(item.pageX, item.pageY, y);
            }
        }
        else {
            $('#tooltip').remove();
            previousPoint = null;
        }
    });\n";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createSelectZoom()
    {
        if (!$this->params["doSelect"]) {
            return "";
        }
        $ret .= "    placeholder.bind('plotselected', function (event, ranges) {
        var select = $('#flotSel').attr('checked');
        if (select)
            plot = $.plot(placeholder, data,
                          $.extend(true, {}, options, {
                              xaxis: { min: ranges.xaxis.from, ";
        $ret .= "max: ranges.xaxis.to }
                          }));
    });
    $('#flotSel').click(function () {selectSwitch();});
    function selectSwitch() {
        var select = $('#flotSel').attr('checked');
        if (select) {
            document.getElementById('flotZoom').checked = false;
            document.getElementById('flotPan').checked = false;
            options.selection.mode = 'x';
            options.zoom.interactive = false;
            options.pan.interactive = false;
        } else {
            options.selection.mode = null;
        }
        ".$this->_createPlot();
        $ret .= "    }
    selectSwitch();\n";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createPanPan()
    {
        if (!$this->params["doPan"]) {
            return "";
        }
        $ret .= "    $('#flotPan').click(function () {selectPan();});
    function selectPan() {
        var select = $('#flotPan').attr('checked');
        if (select) {
            document.getElementById('flotSel').checked = false;
            options.pan.interactive = true;
            options.selection.mode = null;
        } else {
            options.pan.interactive = false;
        }
        ".$this->_createPlot();
        $ret .= "    };
    selectPan();\n";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _createPanZoom()
    {
        if (!$this->params["doZoom"]) {
            return "";
        }
        $ret .= "    $('#flotZoom').click(function () {selectZoom();});
    function selectZoom() {
        var select = $('#flotZoom').attr('checked');
        if (select) {
            document.getElementById('flotSel').checked = false;
            options.zoom.interactive = true;
            options.selection.mode = null;
        } else {
            options.zoom.interactive = false;
        }
        ".$this->_createPlot()."    };
    selectZoom();\n";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    public function graph()
    {
        $ret  = '<script id="source" language="javascript" type="text/javascript">';
        $ret .= "\n\$(function () {\n";
        $ret .= $this->_createData();
        $ret .= $this->_createOptions();
        $ret .= $this->_createPlaceholder();
        $ret .= "    ".$this->_createPlot();
        $ret .= $this->_createHover();
        $ret .= $this->_createSelectZoom();
        $ret .= $this->_createPanPan();
        $ret .= $this->_createPanZoom();
        $ret .= "});\n";
        $ret .= "</script>\n";
        return $ret;
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    public function body()
    {
        $this->y2 = !empty($this->params["fields"][2]);
        $yaxis = $this->params["unitTypes"][1]." (".$this->params["units"][1].")";
        if ($this->y2) {
            $yaxis2  = $this->params["unitTypes"][2];
            $yaxis2 .= " (".$this->params["units"][2].")";
        } else {
            $yaxis2 = "&nbsp;";
        }
        $ret = $this->_bodyCss().'
    <div id="flotDiv">
        <table id="flotTable">
            <tr>
                <td class="yTitle">&nbsp;</td>
                <td class="flotTitle">
                    '.$this->params["title"].'
                </td>
                <td class="yTitle2">&nbsp;</td>
            </tr>
            <tr>
                <td class="yTitle flotRotate">'.$yaxis.'</td>
                <td>
                    <div id="placeholder"></div>
                </td>
                <td class="y2Title flotRotate">'.$yaxis2.'</td>
            </tr>
            <tr>
                <td class="yTitle" style="white-space:nowrap;">'
                    .$this->_bodyControls().'</td>
                <td>
                    <div id="legend"></div>
                </td>
                <td class="yTitle2">&nbsp;</td>
            </tr>
        </table>
    </div>
';
        $ret .= $this->graph();
        return $ret;
    }

    /**
    * Returns the calculated width of the graph
    *
    * @return Width of the graph
    */
    private function _graphWidth()
    {
        return $this->params["width"] - $this->params["margin"]["left"]
            - $this->params["margin"]["right"];
    }
    /**
    * Returns the calculated width of the graph
    *
    * @return Width of the graph
    */
    private function _graphHeight()
    {
        return $this->params["height"] - $this->params["margin"]["top"]
            - $this->params["margin"]["bottom"];
    }
    /**
    * This function gets the style sheet for the graph
    *
    * @return String the text to output
    */
    private function _bodyControls()
    {
        if ($this->params["doSelect"]) {
            $controls .= '<label for="flotSel">';
            $controls .= '<input id="flotSel" type="checkbox">Select</input>';
            $controls .= '</label>';
            $controls .= "<br/>\n";
        }
        if ($this->params["doPan"]) {
            $controls .= '<label for="flotPan">';
            $controls .= '<input id="flotPan" type="checkbox">Pan</input>';
            $controls .= '</label>';
            $controls .= "<br/>\n";
        }
        if ($this->params["doPan"]) {
            $controls .= '<label for="flotZoom">';
            $controls .= '<input id="flotZoom" type="checkbox">Zoom</input>';
            $controls .= '</label>';
            $controls .= "<br/>\n";
        }
        return $controls;
    }
    /**
    * This function gets the style sheet for the graph
    *
    * @return String the text to output
    */
    private function _bodyCss()
    {
        return '
    <style>
        #placeholder {
            width: '.$this->_graphWidth().'px;
            height: '.$this->_graphHeight().'px;
            margin: 10px auto;
        }
        #legend {
            height: '.$this->params["margin"]["bottom"].'px;
            margin: 0px auto;
            padding-left: 30px;
        }
        .flotRotate {
            /* This is css3 */
            rotation: 90deg !important;
            /* This is for mozilla */
            -webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
            /* This is for IE  */
            filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
        }
        .yTitle {
            width: '.$this->params["margin"]["left"].'px !important;
        }
        .y2Title {
            width: '.$this->params["margin"]["right"].'px !important;
        }
        .flotTitle {
            text-align: center;
            font-weight: bold;
            height: '.$this->params["margin"]["top"].'px;
        }
        #flotDiv {
            width: '.$this->params["width"].'px;
            margin: auto;
        }
        #flotTable {
            background: #DDD;
            margin: 10px;
        }
        #flotTable td {
            color: 000;
        }
    </style>';
    }

}

?>
