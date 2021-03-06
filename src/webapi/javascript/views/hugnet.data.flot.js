/**
 * hugnet.device.js
 *
 * <pre>
 * HUGnetLib is a user interface for the HUGnet
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
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
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
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
        datefield: 'UnixDate',
        units: 'Unknown'
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
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
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
                        color: parseInt(key, 10),
                        fieldname: value,
                        datefield: this.datefield,
                        units: options.units[key]
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
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DataFlot = Backbone.View.extend({
    model: HUGnet.Histories,
    tagName: 'div',
    template: '#DataPointTableTemplate',
    parent: 'unknown',
    fields: {},
    classes: {},
    $plot: null,
    checkboxes: [],
    annotationColor: "#C00",
    previousPoint: null,
    hoversetup: false,
    clicksetup: false,
    zoom: false,
    readonly: true,
    url: "",
    events: {
       'click #flot-choice input': 'render',
       'click #toggle': 'toggle'
    },
    initialize: function (options)
    {
 
        delete options.model;
        if (options.readonly !== undefined) this.readonly = options.readonly;
        this.url = options.url;
        this.fields = options.fields;
        this.classes = options.classes;
        this.parent = options.parent;
        this.annotations = options.annotations;
        // This sets the legend to the correct value for this instance
        this.points = new HUGnet.FlotPoints(null, options);
        this.model.on('sync', this.render, this);
        this._setup();
        this.$graph.on('update', this.update, this);
    },
    update: function ()
    {
        if (this.$plot) {
            this.$plot.draw();
        }
    },
    toggle: function ()
    {
        _.each(
            this.checkboxes,
            function(val, key) {
                this.$("#"+val).prop('checked', !this.$("#"+val).prop('checked'));
            },
            this
        );
        this.render();
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
        var markings = this._markingSetup();
        var options = {
            series: { lines: { show: true }, points: { show: false} },
            xaxis: { mode: 'time', timeformat: '%m/%d %y<br/>%H:%M:%S', axisLabel: "Time (UTC)" },
            yaxis: { },
            yaxes: [ {}, {}, {}, {}, {}, {} ],
            legend: {
                position: 'nw', container: this.$legend, noColumns: 4
            },
            // selection: { mode: 'x' },
            grid: { 
                backgroundColor: '#EEE', hoverable: true, clickable: true, markings: markings 
            },
            zoom: { interactive: this.zoom },
            pan: { interactive: this.zoom }
        };
        this.points.clear();
        this.points.fromHistory(this.model);
        var data = [];
        var index = 0;
        var max = options.yaxes.length;
        var axis = {};
        var self = this;
        var datasets = this.points.toJSON();
        this.$el.find("input:checked").each(function () {
            var key = $(this).attr("name");
            if (key && datasets[key]) {
                if (axis[datasets[key].units] == undefined) {
                    axis[datasets[key].units] = index;
                    index++
                    if (index >= max) {
                        index = max - 1;
                    }
                }
                var yaxis = axis[datasets[key].units];
                // The plus 1 is because the axes are 1 based, while the config is 0 based
                datasets[key].yaxis = yaxis + 1;
                data.push(datasets[key]);
                var units = '('+datasets[key].units+')';
                if (options.yaxes[yaxis].axisLabel == undefined) {
                    options.yaxes[yaxis].axisLabel = units;
                } else if (options.yaxes[yaxis].axisLabel != units) {
                    options.yaxes[yaxis].axisLabel = 'Multiple Units';
                }
            }
        });
        this.$plot = $.plot(this.$graph, data, options);
        this._hoversetup();
        this._clicksetup();
        return this;
    },
    renderTooltip: function (x, y, contents) {
        this.$tooltip = $('<div id="flot-tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 5,
            left: x + 20,
            border: '1px solid #fdd',
            padding: '3px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
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
            '<div id="flot-graph"></div>'
        ).appendTo(this.$el);
        this.$legend = $(
            '<div id="flot-legend"></div>'
        ).appendTo(this.$el);
        this.$el.trigger('update');

        var i = 0;
        var datasets = this.points.toJSON();
        // insert checkboxes
        this.$choice = $('<ul id="flot-choice"></ul>').prependTo(this.$el);
        this.checkboxes = [];
        _.each(
            datasets,
            function(val, key) {
                this.$choice.append(
                    '<li><input type="checkbox" name="' + key +
                    '" checked="checked" id="id' + key + '">' +
                    '<label for="id' + key + '">' + val.label + '</label></li>'
                );
                var index = this.checkboxes.length;
                this.checkboxes[index] = 'id'+key;
            },
            this
        );
        this.$choice.append(
            '<li><button id="toggle">Toggle</button></li>'
        );
        return this;
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
    _markingSetup: function ()
    {
        var markings = [];
        if (!this.zoom) {
            var index = 0;
            var self = this;
            this.annotations.each(
                function (model, collection, options)
                {
                    var date = model.get("testdate") * 1000;
                    markings[index] = {
                        color: self.annotationColor, 
                        lineWidth: 1, 
                        xaxis: { from: date, to: date }
                    };
                    index++;
                },
                this
            );
        }
        return markings;
    },
    _clicksetup: function ()
    {
        var self = this;
        if (!this.clicksetup && !this.readonly) {
            this.$graph.on("plotclick", function (event, pos, item) {
                if (item) {
                    var model = self.annotations.add({
                        test: self.parent.model.get("id"),
                        testdate: (item.datapoint[0] / 1000),
                        type: "device",
                    });
                    var view = new AnnotationPropertiesView({
                        model: model,
                        url: self.url
                    });
                    self.$el.append(view.render().el);
                    view.$el.dialog({
                        modal: true,
                        draggable: true,
                        width: 800,
                        resizable: false,
                        title: view.title(),
                        dialogClass: "window",
                        zIndex: 500
                    });
                    self.annotations.on("change", self._annotationChange, self);
                }
            });
            this.clicksetup = true;
        }
    },
    _annotationChange: function ()
    {
        this.annotations.off("change", this._annotationChange, this);
        this.render();
    },
    _hoversetup: function ()
    {
        var prevPoint = null;
        var prevId = null;
        var self = this;

        if (!this.hoversetup) {
            this.$graph.on("plothover", function (event, pos, item) {
                if (item) {
                    if ((prevPoint != item.dataIndex) || (prevId != item.series.id)) {
                        prevPoint = item.dataIndex;
                        prevId    = item.series.id;
                        $('#flot-tooltip').remove();
                        var x = HUGnet.viewHelpers.sqlUTCDate(item.datapoint[0]);

                        var text = '<div class="bold">'+item.series.label+'</div>';
                        text = text + x + "<br />"+item.datapoint[1];
                        
                        self.renderTooltip(item.pageX, item.pageY, text);
                    }
                }
                else {
                    $('#flot-tooltip').remove();
                    prevPoint = null;
                    prevId    = null;
                }
            });
            this.hoversetup = true;
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
