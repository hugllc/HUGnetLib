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
        defaults: function ()
        {
            return {
                id: null,
                device: null,
                Date: null,
                DataIndex: null,
                poll: false,
                url: '/HUGnetLib/index.php',
            };
        },
        initialize: function (attrib)
        {
            if (this.get("poll")) {
                this.poll();
            }
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
            var devices = this.get('device');
            var id = '';
            var sep = '';
            var i;
            for (i in devices) {
                id += sep + devices[i].toString(16);
                sep = ',';
            }
            var self = this;
            var ret = $.ajax({
                type: 'GET',
                url: this.get('url'),
                dataType: 'json',
                data: {
                    "task": "poll",
                    "id": id
                },
            });
            ret.done(
                function (data)
                {
                    if (typeof data === "object") {
                        self.set(data);
                        self.trigger('pollsuccess');
                    } else {
                        self.trigger('pollfail');
                    }
                }
            );
            ret.fail(
                function ()
                {
                    self.trigger('pollfail');
                }
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
    var DataPoints = Backbone.Collection.extend({
        url: '/HUGnetLib/index.php',
        model: DataPoint,
        device: undefined,
        DataIndex: null,
        pause: 1,
        doPoll: false,
        initialize: function (models, options)
        {
            this.device = options.device;
            this.pause = options.pause;
        },
        comparator: function (data)
        {
            return data.get("Date");
        },
        _getPoll: function ()
        {
            var data = new DataPoint({
                id: this.length,
                device: this.device,
                poll: true,
            });
            data.bind(
                "pollsuccess",
                function ()
                {
                    this._pollAgain();
                    if (this.DataIndex !== data.get('DataIndex')) {
                        this.DataIndex = data.get('DataIndex');
                        this.trigger("add", data);
                    } else {
                        this.remove(data);
                    }
                },
                this
            );
            data.bind(
                "pollfailure",
                function ()
                {
                    this.remove(data);
                },
                this
            );
            this.add(data, {silent: true});
        },
        _pollAgain: function ()
        {
            var self = this;
            if (this.doPoll) {
                setTimeout(
                    function () {
                        self._getPoll();
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

        },
        stopPoll: function ()
        {
            this.doPoll = false;
        },
        startPoll: function ()
        {
            this.doPoll = true;
            this._getPoll();
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
            var data = this.model.get("Data");
            data["Date"] = this.model.get("Date");
            data["DataIndex"] = this.model.get("DataIndex");
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
        template: "#DataPointListTemplate",
        hTemplate: "#DataPointHeaderTemplate",
        tagName: 'div',
        pause: 1,
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
            'click .exit': 'exit',
        },
        initialize: function (options)
        {
            this.data = options.data;
            var i;
            for (i in this.data) {
                this.device[this.data[i].device] = this.data[i].device;
                this.header[i] = this.data[i].name;
                this.fields[i] = this.data[i].field;
                this.classes[i] = this.data[i].class;
            }
            this.model = new DataPoints(null, { device: this.device });
            this.model.bind('add', this.insert, this);
            if (options.pause !== undefined) {
                this.pause = options.pause - 0;
            }
            this.parent = options.parent;
            this.id = options.id;
        },
        startPoll: function()
        {
            this.$('.stopPoll').show();
            this.$('.startPoll').hide();
            this.model.pause = this.pause;
            this.model.startPoll();
        },
        stopPoll: function()
        {
            this.$('.stopPoll').hide();
            this.$('.startPoll').show();
            this.model.stopPoll();
        },
        exit: function()
        {
            this.reset();
            this.trigger('remove');
            this.remove();
        },
        reset: function()
        {
            this.model.clear();
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
                    $(this.template).html(),
                    { header: header, pause: this.pause, id: this.id }
                )
            );
            this.$('.stopPoll').hide();
            this.model.each(this.renderEntry);
            this.$el.trigger('update');
            return this;
        },
        insert: function (model)
        {
            var view = new DataPointEntryView({
                model: model, fields: this.fields, classes: this.classes
            });
            this.$("#DataPointList").prepend(view.render().el);
        },
        renderEntry: function (view)
        {
            view.render();
        }
    });

}());