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
 * @subpackage Devices
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
* @subpackage Devices
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var DevicePropertiesView = Backbone.View.extend({
    template: '#DevicePropertiesTemplate',
    tTemplate: '#DevicePropertiesTitleTemplate',
    tagName: 'div',
    events: {
        'click .SaveDevice': 'save',
        'submit #sensorForm': 'saveSensor',
        'change #sensorForm select': 'saveSensor'
    },
    initialize: function (options)
    {
        this.model.on('change', this.render, this);
        this.model.on('savefail', this.saveFail, this);
        this.model.on('saved', this.saveSuccess, this);

        this.sensorsmodel = new HUGnet.DeviceSensors();
        var sensors = this.model.get('sensors');
        this.sensorsmodel.reset(sensors);
        this.sensors = new HUGnet.DeviceSensorsView({
            model: this.sensorsmodel
        });
        this.sensorsmodel.on(
            'change',
            function (model, collection, view)
            {
                this.model.set('sensors', this.sensorsmodel.toJSON());
            },
            this
        );
        this.channelsmodel = new HUGnet.DeviceChannels();
        var channels = this.model.get('channels');
        this.channelsmodel.reset(channels);
        this.channels = new HUGnet.DeviceChannelsView({
            model: this.channelsmodel
        });
        this.channelsmodel.on(
            'change',
            function (model, collection, view)
            {
                this.model.set('channels', this.channelsmodel.toJSON());
            },
            this
        );
    },
    save: function (e)
    {
        this.setTitle( " [ Saving...] " );
        this.model.set({
            DeviceName: this.$(".DeviceName").val(),
            DeviceLocation: this.$(".DeviceLocation").val(),
            DeviceJob: this.$(".DeviceJob").val(),
            PollInterval: this.$(".PollInterval").val()
        });
        this.model.save();
    },
    saveSensor: function (e)
    {
        this.model.saveSensor($("#sensorForm").serialize());
    },
    saveFail: function ()
    {
        this.setTitle();
        //alert("Save Failed");
    },
    saveSuccess: function ()
    {
        this.model.off('change', this.render, this);
        this.model.off('savefail', this.saveFail, this);
        this.model.off('saved', this.saveSuccess, this);
        this.remove();
        //alert("Save Succeeded");
    },
    setTitle: function (extra)
    {
        this.$el.dialog( "option", "title", this.title() + extra );
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

        var data = this.model.toJSON();
        _.extend(data, HUGnet.viewHelpers);
        data.channels = '<div id="DeviceChannelsDiv"></div>';
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        this.$("#DeviceChannelsDiv").html(this.channels.render().el);
        this.setTitle();
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
    title: function ()
    {
        return _.template(
            $(this.tTemplate).html(),
            this.model.toJSON()
        );
    }
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage Devices
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var DeviceEntryView = Backbone.View.extend({
    tagName: 'tr',
    template: '#DeviceEntryTemplate',
    parent: null,
    events: {
        'click .refresh': 'refresh',
        'click .properties': 'properties'
    },
    initialize: function (options)
    {
        this.model.bind('change', this.render, this);
        this.model.bind('remove', this.remove, this);
        this.model.bind('configfail', this.refreshFail, this);
        this.parent = options.parent;
    },
    refresh: function (e)
    {
        this.$el.addClass("working");
        this.model.config();
    },
    refreshFail: function ()
    {
        this.$el.removeClass("working");
        //alert("Failed to get the configuration for the device");
    },
    properties: function (e)
    {
        var view = new DevicePropertiesView({ model: this.model });
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
            _.template(
                $(this.template).html(),
                this.model.toJSON()
            )
        );
        this.$el.trigger('update');
        return this;
    }
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage Devices
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DevicesView = Backbone.View.extend({
    template: "#DeviceListTemplate",
    tagName: "table",
    events: {
    },
    initialize: function (options)
    {
        this.model.each(this.insert, this);
        this.model.bind('add', this.insert, this);
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
            _.template(
                $(this.template).html(),
                this.model.toJSON()
            )
        );
        //this.model.each(this.renderEntry);
        this.$el.tablesorter({ widgets: ['zebra'] });
        this.$el.trigger('update');
        return this;
    },
    insert: function (model, collection, options)
    {
        var view = new DeviceEntryView({ model: model, parent: this });
        this.$('tbody').append(view.render().el);
        this.$el.trigger('update');
        this.$('.tablesorter').trigger('update');
    },
    popup: function (view)
    {
        this.$el.append(view.render().el);
        view.$el.dialog({
            modal: true,
            draggable: false,
            width: 700,
            resizable: false,
            title: view.title(),
            dialogClass: "window",
            zIndex: 500
        });
        view.model.bind(
            'change',
            function ()
            {
                this.$el.dialog( "option", "title", this.title() );
            },
            view
        );
    }
});
