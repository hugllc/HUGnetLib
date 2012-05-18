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
$(function() {
    "use strict";
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
    var DataPoint = Backbone.Model.extend({
        idAttribute: 'Date',
        defaults: function ()
        {
            return {
                id: null,
                Date: null,
                DataIndex: null,
            };
        },
        initialize: function ()
        {
            this.set("UnixDate", this.get("Date"));
        },
        /**
        * Gets infomration about a device.  This is retrieved from the database only.
        *
        * @param id The id of the device to get
        *
        * @return null
        */
        fetch: function()
        {
        },
        /**
        * Gets infomration about a device.  This is retrieved from the database only.
        *
        * @param id The id of the device to get
        *
        * @return null
        */
        save: function()
        {
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
    var DataPoints = Backbone.Collection.extend({
        url: '/HUGnetLib/index.php',
        model: DataPoint,
        device: undefined,
        id: undefined,
        DataIndex: null,
        LastHistory: 0,
        mode: null,
        refresh: null,
        pause: 1,
        limit: 50,
        type: "test",
        doPoll: false,
        initialize: function (models, options)
        {
            this.reset(null, { silent: true });
            this.bind('add', this.addExtra, this);
            this.device = options.device;
            this.pause = options.pause;
            this.id = options.id;
            this.mode = options.mode;
            if (this.mode != 'run') {
                this.fetch();
            }
        },
        setRefresh: function (refresh)
        {
            this.refresh = refresh;
            if (refresh > 0) {
                this.fetch();
            }
        },
        addExtra: function (model, collection, options)
        {
            var last = model.get("UnixDate");
            if (last > this.LastHistory) {
                this.LastHistory = last;
            }
            while (this.length > this.limit) {
                /* Remove the oldest record */
                this.shift();
            }
        },
        /*
        comparator: function (data)
        {
            return data.get("Date");
        },
        */
        _pollAgain: function ()
        {
            var self = this;
            if (this.doPoll) {
                setTimeout(
                    function () {
                        self.poll();
                    },
                    (this.pause * 1000)
                );
            }
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
        fetch: function ()
        {
            var self = this;
            $.ajax({
                type: 'GET',
                url: this.url,
                dataType: 'json',
                cache: false,
                data: {
                    "task": "history",
                    "id": this.id,
                    "since": this.LastHistory,
                    "limit": this.limit,
                    "TestID": (this.type == "test") ? 1 : 0,
                },
            }).done(
                function (data)
                {
                    if ((self.refresh > 0) && (self.mode === 'view')) {
                        setTimeout(
                            function () {
                                self.fetch();
                            },
                            (self.refresh * 1000)
                        );
                    }
                    if ((data !== undefined) && (data !== null) && (typeof data === "object")) {
                        var i;
                        for (i in data) {
                            self.add(data[i]);
                        }
                    }
                }
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
        poll: function ()
        {
            var self = this;
            $.ajax({
                type: 'GET',
                url: this.url,
                dataType: 'json',
                cache: false,
                data: {
                    "task": "poll",
                    "id": this.id,
                    "TestID": (this.type == "test") ? 1 : 0,
                },
            }).done(
                function (data)
                {
                    self._pollAgain();
                    if ((data !== undefined) && (data !== null) && (typeof data === "object")) {
                        self.add(data);
                    }
                }
            );
        },
        stopPoll: function ()
        {
            this.doPoll = false;
        },
        startPoll: function ()
        {
            this.doPoll = true;
            this.poll();
        },
        clear: function ()
        {
            /* This erases everything and triggers 'remove' events to the views go away */
            this.remove(this.models);
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
    var DataPointEntryView = Backbone.View.extend({
        model: DataPoint,
        tagName: 'tr',
        template: '#DataPointTemplate',
        fields: {},
        classes: {},
        events: {
        },
        initialize: function (options)
        {
            this.fields = options.fields;
            this.classes = options.classes;
            this.model.bind('pollsuccess', this.render, this);
            this.model.bind('remove', this.remove, this);
            Date.prototype.formatHUGnet = function()
            {
                var m = this.getMonth();
                var d = this.getDate();
                var Y = this.getFullYear();
                var H = this.getHours();
                var i = this.getMinutes();
                var s = this.getSeconds();

                if (H < 10) {
                    H = "0" + H;
                }
                if (i < 10) {
                    i = "0" + i;
                }
                if (s < 10) {
                    s = "0" + s;
                }
                if (m < 10) {
                    m = "0" + m;
                }
                if (d < 10) {
                    d = "0" + d;
                }

                return Y + "-" + m + " " + d + " " + H + ":" + i + ":" + s;
            }
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
            var header = "";
            var i;
            var data = this.model.toJSON();
            var d = new Date();
            d.setTime(data["UnixDate"] * 1000);
            data["Date"] = d.formatHUGnet();
            for (i in this.fields) {
                header += _.template(
                    $(this.template).html(),
                    { data: data[this.fields[i]], fieldClass: this.classes[i] }
                );
            }
            this.$el.html(header);
            this.$el.trigger('update');
            return this;
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
    window.DataPointsView = Backbone.View.extend({
        template: { run: "#DataPointListRunTemplate", view: "#DataPointListViewTemplate" },
        hTemplate: "#DataPointHeaderTemplate",
        rowClass: [ 'odd', 'even' ],
        tagName: 'div',
        pause: 1,
        rows: 0,
        autorefresh: 0,
        mode: 'run',
        type: 'test',
        parent: undefined,
        id: undefined,
        data: {},
        device: {},
        header: {},
        fields: {},
        classes: {},
        events: {
            'click .startPoll': 'startPoll',
            'click .stopPoll': 'stopPoll',
            'click .autorefresh': 'setRefresh',
        },
        initialize: function (options)
        {
            this.data = options.data;
            this.setMode(options.mode);
            var device;
            var i;
            this.header = {};
            this.fields = {};
            this.device = {};
            this.classes = {};
            for (i in this.data) {
                device = parseInt(this.data[i].device, 16);
                if (device > 0) {
                    this.device[device] = device;
                }
                this.header[i] = this.data[i].name;
                this.fields[i] = this.getField(i, this.data[i].field);
                this.classes[i] = this.data[i].class;
            }
            this.model = new DataPoints(
                null,
                {
                    device: this.device,
                    id: this.id,
                    mode: this.mode,
                    type: this.type,
                }
            );
            this.model.bind('add', this.insert, this);
            this.pause = (options.pause !== undefined) ? options.pause - 0 : this.pause;
            this.type = (options.type !== undefined) ? options.type : this.type;
            this.parent = options.parent;
            this.id = options.id;
        },
        setRefresh: function ()
        {
            if (this.$('.autorefresh').prop("checked")) {
                this.autorefresh = this.$('.autorefresh').val() - 0;
            } else {
                this.autorefresh = 0;
            }
            console.log(this.autorefresh);
            this.model.setRefresh(this.autorefresh);
        },
        setMode: function (mode)
        {
            if (mode == "view") {
                this.mode = "view";
            } else {
                this.mode = "run";
            }
        },
        getField: function (index, field)
        {
            if (parseInt(field) == field) {
                return "Data" + index;
            }
            return field;
        },
        startPoll: function()
        {
            if (this.mode === 'run') {
                this.$('.stopPoll').show();
                this.$('.startPoll').hide();
                this.$('.exit').hide();
                this.model.pause = this.pause;
                this.model.startPoll();
            }
        },
        stopPoll: function()
        {
            if (this.mode === 'run') {
                this.$('.stopPoll').hide();
                this.$('.startPoll').show();
                this.$('.exit').show();
                this.model.stopPoll();
            }
        },
        exit: function()
        {
            this.reset();
            this.stopPoll();
            this.model.mode = 'shutdown';
            this.remove();
        },
        reset: function()
        {
            this.model.clear();
            this.rows = 0;
        },
        /**
        * Gets infomration about a device.  This is retrieved directly from the device
        *
        * This function is for use of the device list
        *
        * @return null
        */
        render: function ()
        {
            var header = "";
            var i;
            for (i in this.header) {
                header += _.template($(this.hTemplate).html(), { header: this.header[i] });
            }
            this.$el.html(
                _.template(
                    $(this.template[this.mode]).html(),
                    { header: header, pause: this.pause, id: this.id, mode: this.mode }
                )
            );
            this.$('.stopPoll').hide();
            this.model.each(this.renderEntry);
            this.$el.trigger('update');
            return this;
        },
        insert: function (model, collection, options)
        {
            var view = new DataPointEntryView({
                model: model, fields: this.fields, classes: this.classes
            });
            if  (view.render() !== null) {
                this.$('tbody').prepend(view.el);
                /* this puts on our row class */
                view.$el.addClass(this.rowClass[this.rows % this.rowClass.length]);
                this.rows++;
            }
        },
        renderEntry: function (view)
        {
            view.render();
        }
    });

}());