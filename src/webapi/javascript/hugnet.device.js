/**
 * hugnet.device.js
 *
 * <pre>
 * HUGnetLab is a user interface for the HUGnet
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
 * @package    HUGnetLab
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
$(function() {
/**
 * This is the model that stores the devices.
 *
 * @category   JavaScript
 * @package    HUGnetLab
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
Device = Backbone.Model.extend({
    defaults: {
        id: 0,
        DeviceID: '000000',
        DeviceName: '',
        HWPartNum: '',
        FWPartNum: '',
        FWVersion: '',
        RawSetup: '',
        Active: 0,
        GatewayKey: 0,
        ControllerKey: 0,
        ControllerIndex: 0,
        DeviceLocation: '',
        DeviceJob: '',
        Driver: '',
        PollInterval: 0,
        ActiveSensors: 0,
        DeviceGroup: 'FFFFFF',
        sensors: {},
        params: {},
        actions: '',
        ViewButtonID: '',
        RefreshButtonID: '',
        target: '',
        url: '/HUGnetLib/index.php',
    },
    view: null,
    /**
     * This function initializes the object
     */
    initialize: function(attributes)
    {
        this.update();

    },
    /**
     * Updates the particular elements
     */
    update: function() {
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
        var id = this.get('id');
        if (id !== 0) {
            var self = this;
            $.ajax({
                type: 'GET',
                url: this.get('url'),
                dataType: 'json',
                success: function (data)
                {
                    self.set(data);
                },
                data:
                {
                    "task": "device",
                    "action": "get",
                    "id": id.toString(16)
                },
            });
        }
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
        var id = this.get('id');
        if (id !== 0) {
            var self = this;
            $.ajax({
                type: 'POST',
                url: this.get('url'),
                dataType: 'json',
                success: function (data)
                {
                    self.set(data);
                },
                data:
                {
                    "task": "device",
                    "action": "post",
                    "id": id.toString(16),
                    "device": self.toJSON(),
                },
            });
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
    config: function ()
    {
        var id = this.get('id');
        if (id !== 0) {
            var self = this;
            $.ajax({
                type: 'GET',
                url: this.get('url'),
                dataType: 'json',
                success: function (data)
                {
                    self.set(data);
                },
                data: {
                    "task": "device",
                    "action": "config",
                    "id": id.toString(16)
                },
            });
        }
    }
});

Devices = Backbone.Collection.extend({
    model: Device,
    comparator: function (device)
    {
        return device.get("id");
    }
});

DevicePropertiesView = Backbone.View.extend({
    template: '#DevicePropertiesTemplate',
    tagName: 'div',
    initialize: function (options)
    {
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
        this.$el.html(
            Mustache.render(
                $(this.template).html(),
                this.model.toJSON()
            )
        );
        return this;
    },
});

DeviceEntryView = Backbone.View.extend({
    model: Device,
    tagName: 'tr',
    template: '#DeviceEntryTemplate',
    parent: null,
    events: {
        'click .refresh': 'refresh',
        'click .properties': 'properties',
    },
    initialize: function (options)
    {
        this.model.bind('change', this.render, this);
        this.parent = options.parent;
    },
    refresh: function (e)
    {
        this.$el.addClass("working");
        this.model.config();
    },
    properties: function (e)
    {
        var view = new DevicePropertiesView({ model: this.model });
        view.$el.addClass("device");
        view.$el.addClass("popup");
        view.$el.width(600);
        this.parent.popup(view);
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
        this.$el.removeClass("working");
        this.$el.html(
            Mustache.render(
                $(this.template).html(),
                this.model.toJSON()
            )
        );
        this.$el.trigger('update');
        return this;
    },
});

DevicesView = Backbone.View.extend({
    url: '/HUGnetLib/index.php',
    template: "#DeviceListTemplate",
    events: {
        'click .cancel': 'nopopup',
    },
    initialize: function (options)
    {
        this.model = new Devices();
        this.populate();
        this.render();
    },
    nopopup: function (e)
    {
        this.pop.remove();
        delete this.pop;
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
        this.$el.html(
            Mustache.render(
                $(this.template).html(),
                this.model.toJSON()
            )
        );
        //this.model.each(this.renderEntry);
        this.$el.trigger('update');
        return this;
    },
    insert: function (model, self)
    {
        if (self == undefined) {
            self = this;
        }
        var view = new DeviceEntryView({ model: model, parent: this });
        this.$("#DeviceList").append(view.render().el);
    },
    popup: function (view)
    {
        if (this.pop == undefined) {
            this.pop = view;
            var pwidth = this.$el.width();
            var pos = this.$el.position();
            view.$el.css("top", pos.top);
            this.$el.append(view.render().el);
            var width = view.$el.width();
            if (width > $(window).width()) {
                width = $(window).width() * 0.8;
                view.$el.width(width);
            }
            view.$el.css("left", pos.left + ((pwidth - width) / 2));
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
    populate: function ()
    {
        var self = this;
        $.ajax({
            type: 'GET',
            url: this.url,
            dataType: 'json',
            success: function (data)
            {
                self.model.add(data);
                self.model.each(
                    function (model)
                    {
                        self.insert(model, self);
                    }
                );
            },
            data: {
                "task": "device", "action": "getall"
            },
        });
    },
});



});