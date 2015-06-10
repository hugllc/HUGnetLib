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
 * @subpackage Devices
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
* @subpackage Devices
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
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
        'click .powerList': 'powerList',
        'submit #inputForm': 'saveInput',
        'change #inputForm select': 'saveInput',
        'change #deviceForm select': 'apply'
    },
    initialize: function (options)
    {
        _.bindAll(this, "saveSuccess", "saveFail");
        this.model.lock = true;
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
        this._template = _.template($(this.template).html());
        this._tTemplate = _.template($(this.tTemplate).html());
        this.inputsmodel = this.model.input();
        this.outputsmodel = this.model.output();
        this.processesmodel = this.model.process();
        this.powersmodel = this.model.power();
    },
    inputList: function ()
    {
        var view = new HUGnet.DeviceInputsView({ model: this.inputsmodel });
        this.popup(
            view,
            "Inputs for device " + this.model.get('id').toString(16).toUpperCase()
        );
    },
    outputList: function ()
    {
        var view = new HUGnet.DeviceOutputsView({ model: this.outputsmodel });
        this.popup(
            view,
            "Outputs for device " + this.model.get('id').toString(16).toUpperCase()
        );
    },
    processList: function ()
    {
        var view = new HUGnet.DeviceProcessesView({ model: this.processesmodel });
        this.popup(
            view,
            "Processes for device " + this.model.get('id').toString(16).toUpperCase()
        );
    },
    powerList: function ()
    {
        var view = new HUGnet.DevicePowersView({ model: this.powersmodel });
        this.popup(
            view,
            "Power Ports for device " + this.model.get('id').toString(16).toUpperCase()
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
        var params = {};
        var self = this;
        _.each(["InfoLink", "ImageURL", "LogURL", "PushHistory", "DaughterBoard", "Watchdog"],
            function(sel, i) {
                params[sel] = self.$('.params_'+sel).val();
            }
        );
        this.model.save({
                DeviceName: this.$(".DeviceName").val(),
                DeviceLocation: this.$(".DeviceLocation").val(),
                DeviceJob: this.$(".DeviceJob").val(),
                PollInterval: this.$(".PollInterval").val(),
                Role: this.$(".Role").val(),
                Active: (this.$(".Active")) ? this.$(".Active").val() : 1,
                Publish: (this.$(".Publish")) ? this.$(".Publish").val() : 1,
                setparams: params
            },
            {
                "success" : self.saveSuccess, "error": self.saveFail
            }
        );
        this.setTitle();
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
            this.model.lock = false;
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
        this.$el.html(this._template(data));
        if (this.model.get("PowerTables") == 0) {
            this.$(".powerList").hide();
        }
        if (this.model.get("InputTables") == 0) {
            this.$(".inputList").hide();
        }
        if (this.model.get("OutputTables") == 0) {
            this.$(".outputList").hide();
        }
        if (this.model.get("ProcessTables") == 0) {
            this.$(".processList").hide();
        }
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
        return this._tTemplate(this.model.toJSON());
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
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var DeviceEntryView = Backbone.View.extend({
    tagName: 'tr',
    template: '#DeviceEntryTemplate',
    parent: null,
    progress: undefined,
    events: {
        'change .action': 'action',
        'click .refresh': 'refresh',
        'click .properties': 'properties',
        'click .configview': 'configview',
        'click .configsetview': 'configsetview'
    },
    initialize: function (options)
    {
        this.model.bind('change', this.render, this);
        this.model.bind('remove', this.remove, this);
        this.model.bind('configfail', this.refreshFail, this);
        this.parent = options.parent;
        if (options.url) {
            this.url = options.url;
        }
        this._template = _.template($(this.template).html());
    },
    action: function (e)
    {
        var action = this.$('.action').val();
        //this.$('.action').val('option:first');
        this.$('.action')[0].selectedIndex = 0;
        if (action === 'refresh') {
            this.refresh(e);
        } else if (action === 'properties') {
            this.model.fetch();
            this.properties(e);
        } else if (action === 'configview') {
            this.model.fetch();
            this.configview(e);
        } else if (action === 'configsetview') {
            this.model.fetch();
            this.configsetview(e);
        } else if (action === 'loadfirmware') {
            this.loadfirmware(e);
        } else if (action === 'loadconfig') {
            this.loadconfig(e);
        } else if (action === 'export') {
            this.export(e);
        }
    },
    refresh: function (e)
    {
        this._setupProgress("Reading Config in "+this.model.get("DeviceID"));
        this.model.config();
    },
    export: function (e)
    {
        this.parent.iframe.attr('src', this.model.exporturl);
    },
    loadconfig: function (e)
    {
        this._setupProgress("Loading Config in "+this.model.get("DeviceID"));
        this.model.on('configdone', this._teardownProgress, this);
        this.model.config(1);
    },
    loadfirmware: function (e)
    {
        this._setupProgress("Loading Firmware in "+this.model.get("DeviceID"));
        this.model.loadfirmware();
    },
    refreshFail: function ()
    {
        this._teardownProgress();
        //alert("Failed to get the configuration for the device");
    },
    properties: function (e)
    {
        var view = new DevicePropertiesView({ model: this.model });
        this.parent.popup(view);
    },
    configview: function (e)
    {
        var view = new DeviceConfigView({ 
            model: this.model, 
            url: this.url
        });
        this.parent.popup(view);
    },
    configsetview: function (e)
    {
        var view = new DeviceConfigView({ 
            model: this.model, 
            url: this.url, 
            set: true 
        });
        this.parent.popup(view);
    },
    _setupProgress: function(title)
    {
        if (typeof this.progress !== "object") {
            this.progress = new HUGnet.Progress({
                modal: false,
                draggable: true,
                width: 300,
                title: title,
                dialogClass: "window",
                zIndex: 500,
            });
            this.progress.update(false);
        }
    },
    _teardownProgress: function()
    {
        if (this.progress !== undefined) {
            this.progress.update(1);
            this.progress.remove();
            delete this.progress;
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
        this._teardownProgress();
        var data = this.model.toJSON();
        this.$el.html(this._template(data));
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
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DevicesView = Backbone.View.extend({
    url: '/HUGnetLib/HUGnetLibAPI.php',
    template: "#DeviceListTemplate",
    iframe: undefined,
    progress: undefined,
    timer: null,
    events: {
        'click .loadall': 'loadAll',
        'click .newtest': 'createTest',
        'click .newfastvirtual': 'createFastVirtual',
        'click .newslowvirtual': 'createSlowVirtual',
        'click .importDevice': '_importDevice',
    },
    initialize: function (options)
    {
        this.model.each(this.insert, this);
        this.model.on('add', this.insert, this);
        this._template = _.template($(this.template).html());
    },
    _importProgress: function(title)
    {
        if (typeof this.progress !== "object") {
            this.progress = new HUGnet.Progress({
                modal: false,
                draggable: true,
                width: 300,
                title: title,
                dialogClass: "window no-close",
                zIndex: 500,
            });
            this.progress.update(false);
        }
    },
    _teardownProgress: function()
    {
        if (this.progress !== undefined) {
            this.progress.update(1);
            this.progress.remove();
            delete this.progress;
        }
    },
    _importDevice: function ()
    {
        if (this.$("#importDevice input[type=file]").val() == "") {
            return;
        }
        var form = $("#importDevice");
        form.attr({
            action: this.model.importurl(),
            method: 'post',
            enctype: 'multipart/form-data',
            encoding: 'multipart/form-data',
            target: "exportDevice"
        });
        form.submit();
        this._importProgress("Importing the device");
        this._importWait();
    },
    _importWait: function ()
    {
        var text = this.iframe.contents().text();
        var self = this;
        if (text != "") {
            self._teardownProgress();
            self.timer = null;
            self.$("#importDevice input[type=file]").val("");
            var id = parseInt(text, 16);
            this.model.add({id: id});
            this.model.get(id).refresh();
        } else {
            self.timer = setTimeout(
                function () {
                    self._importWait();
                },
                500
            );
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
        data.url = this.url;
        this.$el.html(this._template(data));
        //this.model.each(this.renderEntry);
        this.$("table").tablesorter({ widgets: ['zebra'] });
        this.$("table").trigger('update');
        this.iframe = $('<iframe>', { name: 'exportDevice', id:'exportDevice' }).hide();
        this.$el.append(this.iframe);
        return this;
    },
    insert: function (model, collection, options)
    {
        var view = new DeviceEntryView({ model: model, parent: this, url: this.url });
        this.$('tbody').append(view.render().el);
        this.$("table").trigger('update');
    },
    loadAll: function ()
    {
        this.model.fetch();
    },
    createTest: function ()
    {
        this.createVirtual("test");
    },
    createFastVirtual: function ()
    {
        this.createVirtual("fastaverage");
    },
    createSlowVirtual: function ()
    {
        this.createVirtual("slowaverage");
    },
    createVirtual: function (type)
    {
        this.model.createVirtual(type);
    },
    popup: function (view)
    {
        this.$el.append(view.render().el);
        view.$el.dialog({
            modal: true,
            draggable: true,
            width: 900,
            resizable: false,
            title: view.title(),
            dialogClass: "window",
            zIndex: 500,
            position: { my: "top center", at: "top center", of: this.$el }
        });
    }
});
