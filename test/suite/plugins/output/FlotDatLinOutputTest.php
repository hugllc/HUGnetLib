<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** Get our classes */
require_once CODE_BASE.'plugins/output/FlotDatLinOutput.php';
/** This is a required class */
require_once 'OutputPluginTestBase.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class FlotDatLinOutputTest extends OutputPluginTestBase
{

    /**
    * Sets up the fixture, for example, open a network connection.
    * This method is called before a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function setUp()
    {
    }

    /**
    * Tears down the fixture, for example, close a network connection.
    * This method is called after a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function tearDown()
    {
        unset($this->o);
    }

    /**
    * Data provider for testRegisterPlugin
    *
    * @return array
    */
    public static function dataRegisterPlugin()
    {
        return array(
            array("FlotDatLinOutput"),
        );
    }
    /**
    * Data provider for testRow
    *
    * @return array
    */
    public static function dataRow()
    {
        return array(
            array(
                array(),
                array(),
                array(array()),
                array(),
                array(),
            ),
            array(
                array(
                    "fields" => array(
                        1 => array("a"),
                        2 => array("b"),
                    ),
                    "dateField" => "c"
                ),
                array("a" => 5, "b" => 6, "c" => 1),
                array(
                    array("a" => 1, "b" => 2, "c" => 3),
                    array("a" => 3, "b" => 4, "c" => 5),
                    array("a" => 5, "b" => null, "c" => 7),
                    array("b" => 6, "c" => 9),
                ),
                array(
                    1 => array(
                        "a" => array(
                            3000 => 1.0,
                            5000 => 3.0,
                            7000 => 5.0,
                            9000 => null,
                        ),
                    ),
                    2 => array(
                        "b" => array(
                            3000 => 2.0,
                            5000 => 4.0,
                            7000 => null,
                            9000 => 6.0,
                        ),
                    ),
                ),
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $params  The parameters to use
    * @param array $preload The array to preload into the class
    * @param mixed $row     The row to use
    * @param array $expect  The expected return
    *
    * @dataProvider dataRow
    *
    * @return null
    */
    public function testRow($params, $preload, $row, $expect)
    {
        $obj = new FlotDatLinOutput($params, $preload);
        foreach ($row as $r) {
            $obj->row($r);
        }
        $this->assertAttributeSame($expect, "graphData", $obj, "Data is wrong");
    }

    /**
    * Data provider for test2string
    *
    * @return array
    */
    public static function dataBody()
    {
        return array(
            array(
                array(),
                array(),
                array(),
                '
    <style>
        #placeholder {
            width: 500px;
            height: 420px;
            margin: 10px auto;
        }
        #legend {
            height: 60px;
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
            width: 50px !important;
        }
        .y2Title {
            width: 50px !important;
        }
        .flotTitle {
            text-align: center;
            font-weight: bold;
            height: 20px;
        }
        #flotDiv {
            width: 600px;
            margin: auto;
        }
        #flotTable {
            background: #DDD;
            margin: 10px;
        }
        #flotTable td {
            color: 000;
        }
    </style>
    <div id="flotDiv">
        <table id="flotTable">
            <tr>
                <td class="yTitle">&nbsp;</td>
                <td class="flotTitle">
'.'                    '.'
                </td>
                <td class="yTitle2">&nbsp;</td>
            </tr>
            <tr>
                <td class="yTitle flotRotate"> ()</td>
                <td>
                    <div id="placeholder"></div>
                </td>
                <td class="y2Title flotRotate">&nbsp;</td>
            </tr>
            <tr>
                <td class="yTitle" style="white-space:nowrap;"><label '
        .'for="flotSel"><input id="flotSel" type="checkbox">Select</input>'
        .'</label><br/>
<label for="flotPan"><input id="flotPan" type="checkbox">Pan</input></label><br/>
<label for="flotZoom"><input id="flotZoom" type="checkbox">Zoom</input></label><br/>
</td>
                <td>
                    <div id="legend"></div>
                </td>
                <td class="yTitle2">&nbsp;</td>
            </tr>
        </table>
    </div>
<script id="source" language="javascript" type="text/javascript">
$(function () {
    var data = [
    ];
    var options = {
        xaxis: { mode: \'time\', label: \'Test\', timeformat: \'%m/%d %y %H:%M\' },
        legend: { position: \'nw\', container: \'#legend\', noColumns: 3 },
        selection: { mode: \'x\' },
        grid: { backgroundColor: \'#EEE\', hoverable: true },
        zoom: { interactive: true },
        pan: { interactive: true }
    };
    var placeholder = $(\'#placeholder\');
    var plot = $.plot(placeholder, data, options);
    function showTooltip(x, y, contents) {
        $(\'<div id="tooltip">\' + contents + \'</div>\').css( {
            position: \'absolute\',
            display: \'none\',
            top: y + 10,
            left: x + 10,
            border: \'1px solid #fdd\',
            padding: \'2px\',
            \'background-color\': \'#fee\',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    }
    var previousPoint = null;
    $(\'#placeholder\').bind(\'plothover\', function (event, pos, item) {
        if (item) {
            if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;
                $(\'#tooltip\').remove();
                var x = item.datapoint[0].toFixed(2),
                   y = item.datapoint[1].toFixed(2);
                showTooltip(item.pageX, item.pageY, y);
            }
        }
        else {
            $(\'#tooltip\').remove();
            previousPoint = null;
        }
    });
    placeholder.bind(\'plotselected\', function (event, ranges) {
        var select = $(\'#flotSel\').attr(\'checked\');
        if (select)
            plot = $.plot(placeholder, data,
                          $.extend(true, {}, options, {
                              xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
                          }));
    });
    $(\'#flotSel\').click(function () {selectSwitch();});
    function selectSwitch() {
        var select = $(\'#flotSel\').attr(\'checked\');
        if (select) {
            document.getElementById(\'flotZoom\').checked = false;
            document.getElementById(\'flotPan\').checked = false;
            options.selection.mode = \'x\';
            options.zoom.interactive = false;
            options.pan.interactive = false;
        } else {
            options.selection.mode = null;
        }
        var plot = $.plot(placeholder, data, options);
    }
    selectSwitch();
    $(\'#flotPan\').click(function () {selectPan();});
    function selectPan() {
        var select = $(\'#flotPan\').attr(\'checked\');
        if (select) {
            document.getElementById(\'flotSel\').checked = false;
            options.pan.interactive = true;
            options.selection.mode = null;
        } else {
            options.pan.interactive = false;
        }
        var plot = $.plot(placeholder, data, options);
    };
    selectPan();
    $(\'#flotZoom\').click(function () {selectZoom();});
    function selectZoom() {
        var select = $(\'#flotZoom\').attr(\'checked\');
        if (select) {
            document.getElementById(\'flotSel\').checked = false;
            options.zoom.interactive = true;
            options.selection.mode = null;
        } else {
            options.zoom.interactive = false;
        }
        var plot = $.plot(placeholder, data, options);
    };
    selectZoom();
});
</script>
',
            ),
            array(
                array(
                    "doZoom" => false,
                    "doToolTip" => false,
                    "doPan" => false,
                    "doSelect" => false,
                    "title" => "Hello",
                ),
                array(),
                array(),
                '
    <style>
        #placeholder {
            width: 500px;
            height: 420px;
            margin: 10px auto;
        }
        #legend {
            height: 60px;
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
            width: 50px !important;
        }
        .y2Title {
            width: 50px !important;
        }
        .flotTitle {
            text-align: center;
            font-weight: bold;
            height: 20px;
        }
        #flotDiv {
            width: 600px;
            margin: auto;
        }
        #flotTable {
            background: #DDD;
            margin: 10px;
        }
        #flotTable td {
            color: 000;
        }
    </style>
    <div id="flotDiv">
        <table id="flotTable">
            <tr>
                <td class="yTitle">&nbsp;</td>
                <td class="flotTitle">
                    Hello
                </td>
                <td class="yTitle2">&nbsp;</td>
            </tr>
            <tr>
                <td class="yTitle flotRotate"> ()</td>
                <td>
                    <div id="placeholder"></div>
                </td>
                <td class="y2Title flotRotate">&nbsp;</td>
            </tr>
            <tr>
                <td class="yTitle" style="white-space:nowrap;"></td>
                <td>
                    <div id="legend"></div>
                </td>
                <td class="yTitle2">&nbsp;</td>
            </tr>
        </table>
    </div>
<script id="source" language="javascript" type="text/javascript">
$(function () {
    var data = [
    ];
    var options = {
        xaxis: { mode: \'time\', label: \'Test\', timeformat: \'%m/%d %y %H:%M\' },
        legend: { position: \'nw\', container: \'#legend\', noColumns: 3 },
        selection: {  },
        grid: { backgroundColor: \'#EEE\' },
        zoom: {  },
        pan: {  }
    };
    var placeholder = $(\'#placeholder\');
    var plot = $.plot(placeholder, data, options);
});
</script>
',
            ),
            array(
                array(
                    "margin" => array(
                        "top"    => 20,
                        "bottom" => 180,
                        "left"   => 70,
                        "right"  => 70,
                    ),
                    "width" => 600,
                    "height" => 500,
                    "doLegend" => true,
                    "units" => array(1 => "C", 2 => "F"),
                    "unitTypes" => array(1 => "Temperature", 2 => "Temperature"),
                    "dateField" => "Date",
                    "fields" => array(
                        1 => array("Data0", "Data2"),
                        2 => array("Data1"),
                    ),
                    "title" => "Test Graph",
                ),
                array(
                    "Date" => "Date",
                    "Data0" => "First",
                    "Data1" => "Second",
                    "Data2" => "Third",
                ),
                array(
                    array("Date" => 1, "Data0" => 5, "Data1" => 6, "Data2" => 7),
                    array("Date" => 3, "Data0" => 7, "Data1" => 8, "Data2" => 9),
                ),
                '
    <style>
        #placeholder {
            width: 460px;
            height: 300px;
            margin: 10px auto;
        }
        #legend {
            height: 180px;
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
            width: 70px !important;
        }
        .y2Title {
            width: 70px !important;
        }
        .flotTitle {
            text-align: center;
            font-weight: bold;
            height: 20px;
        }
        #flotDiv {
            width: 600px;
            margin: auto;
        }
        #flotTable {
            background: #DDD;
            margin: 10px;
        }
        #flotTable td {
            color: 000;
        }
    </style>
    <div id="flotDiv">
        <table id="flotTable">
            <tr>
                <td class="yTitle">&nbsp;</td>
                <td class="flotTitle">
                    Test Graph
                </td>
                <td class="yTitle2">&nbsp;</td>
            </tr>
            <tr>
                <td class="yTitle flotRotate">Temperature (C)</td>
                <td>
                    <div id="placeholder"></div>
                </td>
                <td class="y2Title flotRotate">Temperature (F)</td>
            </tr>
            <tr>
                <td class="yTitle" style="white-space:nowrap;"><label '
        .'for="flotSel"><input id="flotSel" type="checkbox">Select</input>'
        .'</label><br/>
<label for="flotPan"><input id="flotPan" type="checkbox">Pan</input></label><br/>
<label for="flotZoom"><input id="flotZoom" type="checkbox">Zoom</input></label><br/>
</td>
                <td>
                    <div id="legend"></div>
                </td>
                <td class="yTitle2">&nbsp;</td>
            </tr>
        </table>
    </div>
<script id="source" language="javascript" type="text/javascript">
$(function () {
    var data = [
        {
            data: [[1000, 5], [3000, 7]],
            label: \'First\',
            yaxis: 1
        },
        {
            data: [[1000, 7], [3000, 9]],
            label: \'Third\',
            yaxis: 1
        },
        {
            data: [[1000, 6], [3000, 8]],
            label: \'Second\',
            yaxis: 2
        }
    ];
    var options = {
        xaxis: { mode: \'time\', label: \'Test\', timeformat: \'%m/%d %y %H:%M\' },
        legend: { position: \'nw\', container: \'#legend\', noColumns: 3 },
        selection: { mode: \'x\' },
        grid: { backgroundColor: \'#EEE\', hoverable: true },
        zoom: { interactive: true },
        pan: { interactive: true }
    };
    var placeholder = $(\'#placeholder\');
    var plot = $.plot(placeholder, data, options);
    function showTooltip(x, y, contents) {
        $(\'<div id="tooltip">\' + contents + \'</div>\').css( {
            position: \'absolute\',
            display: \'none\',
            top: y + 10,
            left: x + 10,
            border: \'1px solid #fdd\',
            padding: \'2px\',
            \'background-color\': \'#fee\',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    }
    var previousPoint = null;
    $(\'#placeholder\').bind(\'plothover\', function (event, pos, item) {
        if (item) {
            if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;
                $(\'#tooltip\').remove();
                var x = item.datapoint[0].toFixed(2),
                   y = item.datapoint[1].toFixed(2);
                showTooltip(item.pageX, item.pageY, y);
            }
        }
        else {
            $(\'#tooltip\').remove();
            previousPoint = null;
        }
    });
    placeholder.bind(\'plotselected\', function (event, ranges) {
        var select = $(\'#flotSel\').attr(\'checked\');
        if (select)
            plot = $.plot(placeholder, data,
                          $.extend(true, {}, options, {
                              xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
                          }));
    });
    $(\'#flotSel\').click(function () {selectSwitch();});
    function selectSwitch() {
        var select = $(\'#flotSel\').attr(\'checked\');
        if (select) {
            document.getElementById(\'flotZoom\').checked = false;
            document.getElementById(\'flotPan\').checked = false;
            options.selection.mode = \'x\';
            options.zoom.interactive = false;
            options.pan.interactive = false;
        } else {
            options.selection.mode = null;
        }
        var plot = $.plot(placeholder, data, options);
    }
    selectSwitch();
    $(\'#flotPan\').click(function () {selectPan();});
    function selectPan() {
        var select = $(\'#flotPan\').attr(\'checked\');
        if (select) {
            document.getElementById(\'flotSel\').checked = false;
            options.pan.interactive = true;
            options.selection.mode = null;
        } else {
            options.pan.interactive = false;
        }
        var plot = $.plot(placeholder, data, options);
    };
    selectPan();
    $(\'#flotZoom\').click(function () {selectZoom();});
    function selectZoom() {
        var select = $(\'#flotZoom\').attr(\'checked\');
        if (select) {
            document.getElementById(\'flotSel\').checked = false;
            options.zoom.interactive = true;
            options.selection.mode = null;
        } else {
            options.zoom.interactive = false;
        }
        var plot = $.plot(placeholder, data, options);
    };
    selectZoom();
});
</script>
',
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param mixed $header  The header data
    * @param mixed $rows    The rows to add
    * @param array $expect  The expected return
    *
    * @dataProvider dataBody
    *
    * @return null
    */
    public function testBody($preload, $header, $rows, $expect)
    {
        $obj = new FlotDatLinOutput($preload);
        $obj->header($header);
        foreach ($rows as $r) {
            $obj->row($r);
        }
        $graph = $obj->body();
        $this->assertSame($expect, $graph);
    }

    /**
    * Data provider for testHeader
    *
    * @return array
    */
    public static function dataHeader()
    {
        return array(
            array(
                array(),
                array("a" => "First"),
                array("a" => "First"),
            ),
            array(
                array("a" => 1, "b" => 2),
                array(),
                array("a" => 1, "b" => 2),
            ),
            array(
                array("a" => 1, "b" => 2),
                array("a" => "q"),
                array("a" => 1, "b" => 2),
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param mixed $array   The array to feed the header
    * @param array $expect  The expected return
    *
    * @dataProvider dataHeader
    *
    * @return null
    */
    public function testHeader($preload, $array, $expect)
    {
        $obj = new FlotDatLinOutput(null, $preload);
        $obj->header($array);
        $this->assertAttributeSame($expect, "header", $obj);
    }

    /**
    * Data provider for testPre
    *
    * @return array
    */
    public static function dataPre()
    {
        return array(
            array(
                array(),
                "",
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param array $expect  The expected return
    *
    * @dataProvider dataPre
    *
    * @return null
    */
    public function testPre($preload, $expect)
    {
        $obj = new FlotDatLinOutput($preload);
        $this->assertSame($expect, $obj->pre());
    }

    /**
    * Data provider for testPost
    *
    * @return array
    */
    public static function dataPost()
    {
        return array(
            array(
                array(),
                "",
            ),
        );
    }
    /**
    * Tests for verbosity
    *
    * @param array $preload The array to preload into the class
    * @param array $expect  The expected return
    *
    * @dataProvider dataPost
    *
    * @return null
    */
    public function testPost($preload, $expect)
    {
        $obj = new FlotDatLinOutput($preload);
        $this->assertSame($expect, $obj->post());
    }

}

?>
