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
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/OutputPluginBase.php";
require_once dirname(__FILE__)."/../../base/HUGnetDBTable.php";

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
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class JPGraphDatLinOutput extends OutputPluginBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "JPGraphDatLinOutput",
        "Type" => "output",
        "Class" => "JPGraphDatLinOutput",
        "Flags" => array("JPGraphDatLin"),
    );
    /** @var  These are the graph colors that will be used, in order */
    public $params = array(
        "colors" => array(
            "aqua", "azure4", "blue", "blueviolet", "brown3", "chartreuse", "coral",
            "cornflowerblue", "darkcyan", "darkorange", "darkorchid", "darkseagreen",
            "deeppink", "deepskyblue", "gold", "firebrick1", "magenta",
            "midnightblue", "olivedrab", "yellow"
        ),
        "margin" => array(
            "top"    => 20,
            "bottom" => 180,
            "left"   => 70,
            "right"  => 70,
        ),
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
        static $index = 0;
        $dateField = &$this->params["dateField"];
        if (count((array)$array) == 0) {
            return;
        }
        foreach ((array) $this->params["fields"] as $line => $val) {
            foreach ((array) $val as $field) {
                if (!is_null($array[$field])) {
                    $this->graphData[$line][$field][$index] = (float)$array[$field];
                } else {
                    $this->graphData[$line][$field][$index] = null;
                }
            }
            $this->graphDates[$index] = (int)$array[$dateField];
        }
        $index++;
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
    * @return String the text to output
    */
    private function _createGraph()
    {
        // Create the graph
        $this->graph = new Graph(
            $this->params["width"],
            $this->params["height"],
            "auto"
        );
        $this->graph->SetScale("datlin");
        // Set the graph title
        $this->graph->title->Set($this->params["title"]);
        // Setup the legend
        if ($this->params["doLegend"]) {
            $this->graph->legend->SetLayout(LEGEND_HOR);
            $this->graph->legend->Pos(0.5, .99, "center", "bottom");
            $this->graph->legend->SetColumns(3);
        }
        // Set the margins
        //$this->graph->img->SetMargin(70, 70, 20, 180);
        $this->graph->img->SetMargin(
            $this->params["margin"]["left"],
            $this->params["margin"]["right"],
            $this->params["margin"]["top"],
            $this->params["margin"]["bottom"]
        );
        // Set up the axis
        $this->graph->xgrid->Show(true);
        $this->graph->xaxis->SetLabelAngle(90);
        $this->graph->yaxis->title->Set(
            $this->params["unitTypes"][1]
            ." (".html_entity_decode($this->params["units"][1]).")"
        );
        // Set fonts.
        $this->graph->title->SetFont(FF_FONT1, FS_BOLD);
        $this->graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
        $this->graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);
        $this->graph->xaxis->SetPos("min");
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    private function _bodyData()
    {
        $colorIndex = 0;
        // Create the actual graph lines.
        foreach ($this->graphData as $index => $hist) {
            foreach ($hist as $field => $data) {
                $plot[$field] = new LinePlot($data, $this->graphDates);

                if ($this->params["doLegend"]) {
                    $plot[$field]->SetLegend(
                        html_entity_decode(
                            $this->header[$field],
                            ENT_COMPAT
                        )
                    );
                }
                // Set the color
                $plot[$field]->setColor($this->params["colors"][$colorIndex++]);
                // Attach it to one axis or the other.
                if ($index == 1) {
                    $this->graph->Add($plot[$field]);
                } else {
                    $this->y2 = true;
                    $this->graph->AddY2($plot[$field]);
                }
            }
        }
    }
    /**
    * This function implements the output after the data
    *
    * @return String the text to output
    */
    public function body()
    {
        if (!class_exists("Graph")) {
            // This code can not be gotten to in the test.
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }
        $this->_createGraph();
        // The classes
        $this->_bodyData();

        // Set up the second axis if we have one.
        if ($this->y2) {
            $this->graph->SetY2Scale("lin");
            $this->graph->y2axis->title->Set(
                $this->params["unitTypes"][2]
                ." (".html_entity_decode($this->params["units"][2]).")"
            );
            $this->graph->y2axis->title->SetFont(FF_FONT1, FS_BOLD);
        }

        ob_end_clean();
        $this->graph->Stroke();
    }

}

?>
