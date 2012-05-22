/**
 * hugnet.device.js
 *
 * <pre>
 * HUGnetLib is a user interface for the HUGnet
 * Copyright (C) 2012 Hunt Utilities Group, LLC
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
 * @category   JavaScript
 * @package    HUGnetLib
 * @subpackage DataPoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage DataPoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var JqPlotPoint = Backbone.Model.extend({
    defaults:
    {
        id: null,
        fieldname: null,
        data: [],
        config: { },
        datefield: 'UnixDate',
    },
    insert: function (history)
    {
        var data = this.get('data');
        var field = this.get('fieldname');
        var date = this.get('datefield');
        data.push([ parseInt(history.get(date)), parseFloat(history.get(field)) ]);
        this.set('data', data);
    },
    remove: function (history)
    {
        var data = this.get('data');
        var date = history.get(this.get('datefield'));
        var i;
        for (i = 0; i < data.length; i++) {
            if (data[i][0] == date) {
                data.splice(i, 1);
                break;
            }
        }
        this.set('data', data);
    },
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage DataPoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.JqPlotPoints = Backbone.Collection.extend({
    model: JqPlotPoint,
    datefield: 'UnixDate',
    initialize: function (models, options)
    {
        this.reset();
        _.each(
            options.fields,
            function (value, key, list)
            {
                if ((value !== 'Date') && (value !== 'UnixDate')) {
                    this.add({
                        id: parseInt(key),
                        label: options.header[key],
                        fieldname: value,
                        datefield: this.datefield
                    });
                }
            },
            this
        );
    },
    /**
    * Gets infomration about a device.  This is retrieved directly from the device
    *
    * This function is for use of the device list
    *
    * @param id The id of the device to get
    *
    * @return null
    */
    fetch: function () {},
    fromHistory: function (histories)
    {
        this.clear();
        histories.each(
            function (model, collection, options)
            {
                this.insert(model);
            },
            this
        );
    },
    insert: function (history)
    {
        this.each(
            function (model, collection, options)
            {
                model.insert(history);
            }
        );
    },
    remove: function (history)
    {
        this.each(
            function (model, collection, options)
            {
                model.remove(history);
            }
        );
    },
    clear: function ()
    {
        this.each(
            function (model, collection, options)
            {
                model.set('data', []);
            }
        );
    },
    series: function ()
    {
        var data = [];
        this.each(
            function (model, collection, options)
            {
                data.push(model.get('data'));
            }
        );
        return data;
    }
});
/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage DataPoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DataJqPlot = Backbone.View.extend({
    model: HUGnet.Histories,
    tagName: 'div',
    template: '#DataPointTableTemplate',
    fields: {},
    classes: {},
    previousPoint: null,
    options: {
        series: { lines: { show: true }, points: { show: false} },
        xaxis: { mode: 'time', timeformat: '%m/%d %y<br/>%H:%M' },
        legend: {
            position: 'nw', container: '#flot-legend'
        },
        selection: { mode: 'x' },
        grid: { backgroundColor: '#EEE', hoverable: false, clickable: false },
        //zoom: { interactive: true },
        //pan: { interactive: true }
    },
    events: {
       'click #flot-choice input': 'render',
    },
    initialize: function (options)
    {
        delete options.model;
        this.fields = options.fields;
        this.classes = options.classes;
        this.points = new HUGnet.JqPlotPoints(null, options);
        this.model.bind('add', this.insert, this);
        this.model.bind('remove', this.remove, this);
        this.points.fromHistory(this.model);
        this._setup();
    },
    /**
    * Gets infomration about a device.  This is retrieved directly from the device
    *
    * This function is for use of the device list
    *
    * @param id The id of the device to get
    *
    * @return null
    */
    render: function ()
    {
        this.plot = $.jqplot('graph', this.points.series(), {
            title:'What to call it',
            axes:{
                xaxis:{
                    renderer:$.jqplot.DateAxisRenderer,
                    tickOptions:{formatString:'%b %#d, %y'},
                }
            },
        });
        return this;
    },
    /*
    renderTooltip: function (x, y, contents) {
        this.$tooltip = $('<div>' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 10,
            left: x + 10,
            border: '1px solid #fdd',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo('body').fadeIn(200);
    },*/
    /**
    * Gets infomration about a device.  This is retrieved directly from the device
    *
    * This function is for use of the device list
    *
    * @param id The id of the device to get
    *
    * @return null
    */
    _setup: function ()
    {
        this.$graph = $(
            '<div id="graph">Graph</div>'
        ).css({
            width: "600px", height: "300px"
        }).appendTo(this.$el);
        return this;
    },
    /*
    hover: function (event, pos, item)
    {
        if (item) {
            if (this.previousPoint != item.datapoint) {
                this.previousPoint = item.datapoint;
                this.$tooltip.remove();
                var x = item.datapoint[0].toFixed(2);
                var test = item.datapoint[1].toFixed(2);
                this.renderTooltip(item.pageX, item.pageY, text);
            }
        } else {
            this.$tooltip.remove();
            this.previousPoint = null;
        }
    },*/
    insert: function (model, collection, options)
    {
        this.points.insert(model);
        this.render();
    },
    remove: function (model, collection, options)
    {
        this.points.remove(model);
        this.render();
    },
});
