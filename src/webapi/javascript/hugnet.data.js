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
                Date: null,
                DataIndex: null,
            };
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
            var id = '';
            var sep = '';
            var i;
            for (i in this.device) {
                id += sep + this.device[i].toString(16);
                sep = ',';
            }
            var self = this;
            $.ajax({
                type: 'GET',
                url: this.url,
                dataType: 'json',
                data: {
                    "task": "poll",
                    "id": id
                },
            }).done(
                function (data)
                {
                    self._pollAgain();
                    if ((data !== undefined) && (data !== null) && (typeof data === "object")) {
                        if (self.DataIndex !== data['DataIndex']) {
                            self.DataIndex = data['DataIndex'];
                            var point = new DataPoint(data);
                            self.add(point);
                        }
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
            if (data === undefined) {
                this.model.remove();
                this.remove();
                return null;
            }
            data["Date"] = this.model.get("Date");
            data["DataIndex"] = this.model.get("DataIndex");
            var point;
            for (i in this.fields) {
                point = data[this.fields[i]]
                if (point === undefined) {
                    point = null;
                }
                header += _.template(
                    $(this.template).html(),
                    { data: point, fieldClass: this.classes[i] }
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
        rowClass: [ 'odd', 'even' ],
        tagName: 'div',
        pause: 1,
        rows: 0,
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
            this.$('.exit').hide();
            this.model.pause = this.pause;
            this.model.startPoll();
        },
        stopPoll: function()
        {
            this.$('.stopPoll').hide();
            this.$('.startPoll').show();
            this.$('.exit').show();
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