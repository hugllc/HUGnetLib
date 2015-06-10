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
 * @subpackage Gateways
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
* @subpackage Gateways
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var GatewayPropertiesView = Backbone.View.extend({
    template: '#GatewayPropertiesTemplate',
    tTemplate: '#GatewayPropertiesTitleTemplate',
    tagName: 'div',
    events: {
        'click .SaveGateway': 'save',
        'submit #inputForm': 'saveInput',
        'change #inputForm select': 'saveInput',
        'change #deviceForm select': 'apply'
    },
    initialize: function (options)
    {
        this.model.lock = true;
        this.model.on('savefail', this.saveFail, this);
        this.model.on('saved', this.saveSuccess, this);
        this._template = _.template($(this.template).html());
        this._tTemplate = _.template($(this.tTemplate).html());

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
            location: this.$(".location").val(),
            name: this.$(".name").val(),
            description: this.$(".description").val(),
            visible: (this.$(".visible")) ? this.$(".Active").val() : 1,
        });
        this.model.save();
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
            this.model.off('savefail', this.saveFail, this);
            this.model.off('saved', this.saveSuccess, this);
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
        this.$el.html(this._template(data));
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
* @subpackage Gateways
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var GatewayEntryView = Backbone.View.extend({
    url: '/HUGnetLib/HUGnetLibAPI.php',
    tagName: 'tr',
    template: '#GatewayEntryTemplate',
    parent: null,
    progress: undefined,
    events: {
        'change .action': 'action',
        'click .refresh': 'refresh',
        'click .properties': 'properties',
        'click .configview': 'configview'
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
        this.$('.action').val('option:first');
        //this.$('.action')[0].selectedIndex = 0;
        if (action === 'refresh') {
            this.refresh(e);
        } else if (action === 'properties') {
            this.model.refresh();
            this.properties(e);
        }
    },
    refresh: function (e)
    {
        this._setupProgress("Reading Config in "+this.model.get("id"));
        this.model.config();
    },
    export: function (e)
    {
        var url = this.url+"?task=device&action=export";
        url += "&id="+this.model.get("id").toString(16);
        this.parent.iframe.attr('src', url);
    },
    refreshFail: function ()
    {
        this._teardownProgress();
        //alert("Failed to get the configuration for the device");
    },
    properties: function (e)
    {
        var view = new GatewayPropertiesView({ model: this.model });
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
* @subpackage Gateways
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.GatewaysView = Backbone.View.extend({
    url: '/HUGnetLib/HUGnetLibAPI.php',
    template: "#GatewaysTemplate",
    iframe: undefined,
    progress: undefined,
    timer: null,
    events: {
        'click .newtest': 'createTest',
        'click .newfastvirtual': 'createFastVirtual',
        'click .newslowvirtual': 'createSlowVirtual',
        'click .importGateway': '_importGateway',
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
    _importGateway: function ()
    {
        if (this.$("#importGateway input[type=file]").val() == "") {
            return;
        }
        var url = this.url+"?task=device&action=import";
        var form = $("#importGateway");
        form.attr({
            action: url,
            method: 'post',
            enctype: 'multipart/form-data',
            encoding: 'multipart/form-data',
            target: "exportGateway"
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
            self.$("#importGateway input[type=file]").val("");
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
        this.iframe = $('<iframe>', { name: 'exportGateway', id:'exportGateway' }).hide();
        this.$el.append(this.iframe);
        return this;
    },
    insert: function (model, collection, options)
    {
        var view = new GatewayEntryView({ model: model, parent: this, url: this.url });
        this.$('tbody').append(view.render().el);
        this.$("table").trigger('update');
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
        var self = this;
        var ret = $.ajax({
            type: 'GET',
            url: this.url,
            dataType: 'json',
            cache: false,
            data:
            {
                "task": "device",
                "action": "new",
                "data": { type: type }
            }
        }).done(
            function (data)
            {
                if (_.isObject(data)) {
                    self.trigger('created');
                    self.model.add(data);
                } else {
                    self.trigger('newfail');
                }
            }
        ).fail(
            function (data)
            {
                self.trigger('newfail');
            }
        );
    },
    popup: function (view)
    {
        this.$el.append(view.render().el);
        view.$el.dialog({
            modal: true,
            draggable: true,
            width: 800,
            resizable: false,
            title: view.title(),
            dialogClass: "window",
            zIndex: 500
        });
    }
});
