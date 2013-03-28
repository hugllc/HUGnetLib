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
        'click .inputList': 'inputList',
        'click .outputList': 'outputList',
        'click .processList': 'processList',
        'submit #inputForm': 'saveInput',
        'change #inputForm select': 'saveInput',
        'change #deviceForm select': 'apply'
    },
    initialize: function (options)
    {
        this.datachannelsmodel = new HUGnet.DeviceDataChannels();
        var datachannels = this.model.get('dataChannels');
        this.datachannelsmodel.reset(datachannels);
        this.datachannels = new HUGnet.DeviceDataChannelsView({
            model: this.datachannelsmodel
        });
        this.datachannelsmodel.on(
            'change',
            function (model, collection, view)
            {
                this.model.set('dataChannels', this.datachannelsmodel.toJSON());
            },
            this
        );
        this.controlchannelsmodel = new HUGnet.DeviceControlChannels();
        var controlchannels = this.model.get('controlChannels');
        this.controlchannelsmodel.reset(controlchannels);
        this.controlchannels = new HUGnet.DeviceControlChannelsView({
            model: this.controlchannelsmodel
        });
        this.datachannelsmodel.on(
            'change',
            function (model, collection, view)
            {
                this.model.set('dataChannels', this.datachannelsmodel.toJSON());
            },
            this
        );
        this.controlchannelsmodel.on(
            'change',
            function (model, collection, view)
            {
                this.model.set('controlChannels', this.controlchannelsmodel.toJSON());
            },
            this
        );
        this.model.on(
            'change',
            function (model, collection, view)
            {
                var datachannels = this.model.get('dataChannels');
                this.datachannelsmodel.reset(datachannels);
                var controlchannels = this.model.get('controlChannels');
                this.controlchannelsmodel.reset(controlchannels);
                this.render();
            },
            this
        );
        this.model.on(
            'sync',
            function (model, collection, view)
            {
                var datachannels = this.model.get('dataChannels');
                this.datachannelsmodel.reset(datachannels);
                var controlchannels = this.model.get('controlChannels');
                this.controlchannelsmodel.reset(controlchannels);
                this.render();
            },
            this
        );
        this.model.on('savefail', this.saveFail, this);
        this.model.on('saved', this.saveSuccess, this);

    },
    inputList: function ()
    {
        this.inputsmodel = new HUGnet.DeviceInputs();
        var inputs = this.model.get('InputTables');
        var dev = this.model.get('id');
        for (var i = 0; i < inputs; i++) {
            this.inputsmodel.add({dev: dev, input: i});
        }
        this.inputsmodel.invoke('fetch');

        var view = new HUGnet.DeviceInputsView({ model: this.inputsmodel });
        this.popup(
            view,
            "Inputs for device " + this.model.get('id').toString(16).toUpperCase()
        );
    },
    outputList: function ()
    {
        this.outputsmodel = new HUGnet.DeviceOutputs();
        var outputs = this.model.get('OutputTables');
        var dev = this.model.get('id');
        for (var o = 0; o < outputs; o++) {
            this.outputsmodel.add({dev: dev, output: o});
        }
        this.outputsmodel.invoke('fetch');

        var view = new HUGnet.DeviceOutputsView({ model: this.outputsmodel });
        this.popup(
            view,
            "Outputs for device " + this.model.get('id').toString(16).toUpperCase()
        );
    },
    processList: function ()
    {
        this.processesmodel = new HUGnet.DeviceProcesses();
        var processes = this.model.get('ProcessTables');
        var dev = this.model.get('id');
        for (var p = 0; p < processes; p++) {
            this.processesmodel.add({dev: dev, process: p});
        }
        this.processesmodel.invoke('fetch');
        var view = new HUGnet.DeviceProcessesView({ model: this.processesmodel });
        this.popup(
            view,
            "Processes for device " + this.model.get('id').toString(16).toUpperCase()
        );
    },
    save: function (e)
    {
        this.apply(e);
        this._close = true;
    },
    apply: function (e)
    {
        this.setTitle( " [ Saving...] " );
        this.model.set({
            DeviceName: this.$(".DeviceName").val(),
            DeviceLocation: this.$(".DeviceLocation").val(),
            DeviceJob: this.$(".DeviceJob").val(),
            PollInterval: this.$(".PollInterval").val(),
            Role: this.$(".Role").val()
        });
        this.model.save();
    },
    saveInput: function (e)
    {
        this.model.saveInput($("#inputForm").serialize());
    },
    saveFail: function ()
    {
        this.setTitle("");
        //alert("Save Failed");
    },
    saveSuccess: function ()
    {
        this.setTitle("");
        if (this._close) {
            this.model.off('change', this.render, this);
            this.model.off('savefail', this.saveFail, this);
            this.model.off('saved', this.saveSuccess, this);
            this.remove();
        }
    },
    setTitle: function (extra)
    {
        if (this.$el.is(':data(dialog)')) {
            this.$el.dialog( "option", "title", this.title() + extra );
        }
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
        data.dataChannels = '<div id="DeviceDataChannelsDiv"></div>';
        data.controlChannels = '<div id="DeviceControlChannelsDiv"></div>';
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        this.$("#DeviceDataChannelsDiv").html(this.datachannels.render().el);
        this.$("#DeviceControlChannelsDiv").html(this.controlchannels.render().el);
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
    },
    popup: function (view, title)
    {
        this.$el.append(view.render().el);
        view.$el.dialog({
            modal: true,
            draggable: true,
            width: 700,
            resizable: false,
            title: title,
            dialogClass: "window",
            zIndex: 800
        });
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
        'change .action': 'action',
        'click .refresh': 'refresh',
        'click .properties': 'properties'
    },
    initialize: function (options)
    {
        this.model.bind('change', this.render, this);
        this.model.bind('sync', this.render, this);
        this.model.bind('remove', this.remove, this);
        this.model.bind('configfail', this.refreshFail, this);
        this.parent = options.parent;
    },
    action: function (e)
    {
        var action = this.$('.action').val();
        this.$('.action').val('option:first');
        //this.$('.action')[0].selectedIndex = 0;
        if (action === 'refresh') {
            this.refresh(e);
        } else if (action === 'properties') {
            this.model.refresh();
            this.properties(e);
        } else if (action === 'loadfirmware') {
            this.loadfirmware(e);
        } else if (action === 'loadconfig') {
            this.loadconfig(e);
        }
    },
    refresh: function (e)
    {
        this.$el.addClass("working");
        this.model.config();
    },
    loadconfig: function (e)
    {
        this.$el.addClass("working");
        this.model.loadconfig();
    },
    loadfirmware: function (e)
    {
        this.$el.addClass("working");
        this.model.loadfirmware();
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
            draggable: true,
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
                if (this.$el.is(':data(dialog)')) {
                    this.$el.dialog( "option", "title", this.title() );
                }
            },
            view
        );
    }
});
