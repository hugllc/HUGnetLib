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
var FlotPoint = Backbone.Model.extend({
    defaults:
    {
        id: null,
        fieldname: null,
        data: [],
        label: null,
        yaxis: 1,
        datefield: 'UnixDate'
    },
    insert: function (history, offset)
    {
        var data = this.get('data');
        var field = this.get('fieldname');
        var date = this.get('datefield');
        data.push([ parseInt(history.get(date) - offset, 10), parseFloat(history.get(field)) ]);
        this.set('data', data);
    },
    remove: function (history, offset)
    {
        if (_.isObject(history)) {
            var data = this.get('data');
            var date = history.get(this.get('datefield')) - offset;
            var i;
            for (i = 0; i < data.length; i++) {
                if (data[i][0] === date) {
                    data.splice(i, 1);
                    break;
                }
            }
            this.set('data', data);
        }
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
HUGnet.FlotPoints = Backbone.Collection.extend({
    model: FlotPoint,
    datefield: 'UnixDate',
    timeOffset: 0,
    initialize: function (models, options)
    {
        if (options.timeOffset) {
            this.timeOffset = options.timeOffset;
        }
        this.reset();
        _.each(
            options.fields,
            function (value, key, list)
            {
                if ((value !== 'Date') && (value !== 'UnixDate')) {
                    this.add({
                        id: parseInt(key, 10),
                        label: options.header[key],
                        yaxis: 1,
                        color: parseInt(key, 10),
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
                this.insert(model, this.timeOffset);
            },
            this
        );
    },
    insert: function (history)
    {
        this.each(
            function (model, collection, options)
            {
                model.insert(history, this.timeOffset);
            },
            this
        );
    },
    remove: function (history)
    {
        this.each(
            function (model, collection, options)
            {
                model.remove(history, this.timeOffset);
            },
            this
        );
    },
    clear: function ()
    {
        this.each(
            function (model, collection, options)
            {
                model.set('data', []);
            },
            this
        );
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
HUGnet.DataFlot = Backbone.View.extend({
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
            position: 'nw', container: '#flot-legend', noColumns: 2
        },
        selection: { mode: 'x' },
        grid: { backgroundColor: '#EEE', hoverable: true, clickable: true }
        //zoom: { interactive: true },
        //pan: { interactive: true }
    },
    events: {
       'click #flot-choice input': 'render'
    },
    initialize: function (options)
    {
        delete options.model;
        this.fields = options.fields;
        this.classes = options.classes;
        this.points = new HUGnet.FlotPoints(null, options);
        /*
        this.model.bind('add', this.insert, this);
        this.model.bind('remove', this.remove, this);
        this.model.bind('reset', this.clear, this);
        */
        this.model.bind('sync', this.render, this);
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
        this.points.clear();
        this.points.fromHistory(this.model);
        //$.plot(this.$graph, this.points.toJSON(), this.options);
        var data = [];

        var datasets = this.points.toJSON();
        this.$el.find("input:checked").each(function () {
            var key = $(this).attr("name");
            if (key && datasets[key]) {
                data.push(datasets[key]);
            }
        });
        if (data.length === 0) {
            data = datasets;
        }
        if (data.length > 0) {
            $.plot(this.$graph, data, this.options);
        }
        return this;
    },
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
        }).appendTo(this.el).fadeIn(200);
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
    _setup: function ()
    {
        this.$graph = $(
            '<div></div>'
        ).css({
            width: "600px", height: "300px"
        }).appendTo(this.$el);
        this.$legend = $(
            '<div id="flot-legend"></div>'
        ).appendTo(this.$el);
        this.$el.trigger('update');

        this.$graph.on('update', this.hover, this);

        var i = 0;
        var datasets = this.points.toJSON();
        // insert checkboxes
        this.$choice = $('<div id="flot-choice"><div>Show</div></div>').prependTo(this.$el);
        _.each(
            datasets,
            function(val, key) {
                this.$choice.append(
                    '<br/><input type="checkbox" name="' + key +
                    '" checked="checked" id="id' + key + '">' +
                    '<label for="id' + key + '">' + val.label + '</label>'
                );
            },
            this
        );
        return this;
    },
    hover: function (event, pos, item)
    {
        if (item) {
            if (this.previousPoint !== item.datapoint) {
                this.previousPoint = item.datapoint;
                this.$tooltip.remove();
                var x = item.datapoint[0].toFixed(2);
                var text = item.datapoint[1].toFixed(2);
                this.renderTooltip(item.pageX, item.pageY, text);
            }
        } else {
            this.$tooltip.remove();
            this.previousPoint = null;
        }
    },
    insert: function (model, collection, options)
    {
        this.points.insert(model);
    },
    remove: function (model, collection, options)
    {
        this.points.remove(model);
        this.render();
    },
    clear: function (model, collection, options)
    {
        this.points.clear();
        this.render();
    }
});
