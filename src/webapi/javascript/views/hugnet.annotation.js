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
 * @subpackage Annotations
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
* @subpackage Annotations
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var AnnotationPropertiesView = Backbone.View.extend({
    template: '#AnnotationPropertiesTemplate',
    tTemplate: '#AnnotationPropertiesTitleTemplate',
    tagName: 'div',
    events: {
        'click [name="save"]': 'save',
        'click [name="cancel"]': 'cancel',
    },
    initialize: function (options)
    {
        this.model.lock = true;
        this.model.on('savefail', this.saveFail, this);
        this.model.on('saved', this.saveSuccess, this);

    },
    save: function (e)
    {
        this.apply(e);
        this._close = true;
    },
    cancel: function (e)
    {
        this.model.collection.remove(this.model);
        this._close = true;
        this.saveSuccess();
    },
    apply: function (e)
    {
        this.setTitle( " [ Saving...] " );
        this.model.set({
            text: this.$('[name="text"]').val(),
            author: this.$('[name="author"]').val(),
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
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
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
* @subpackage Annotations
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var AnnotationEntryView = Backbone.View.extend({
    url: '/HUGnetLib/HUGnetLibAPI.php',
    tagName: 'tr',
    template: '#AnnotationEntryTemplate',
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
        this.model.bind('sync', this.render, this);
        this.model.bind('remove', this.remove, this);
        this.model.bind('configfail', this.refreshFail, this);
        this.parent = options.parent;
        if (options.url) {
            this.url = options.url;
        }
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
        var view = new AnnotationPropertiesView({ model: this.model });
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
        _.extend(data, HUGnet.viewHelpers);
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
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
* @subpackage Annotations
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.AnnotationsView = Backbone.View.extend({
    url: '/HUGnetLib/HUGnetLibAPI.php',
    template: "#AnnotationsTemplate",
    progress: undefined,
    timer: null,
    events: {
        'click .newtest': 'createTest',
        'click .newfastvirtual': 'createFastVirtual',
        'click .newslowvirtual': 'createSlowVirtual',
        'click .importAnnotation': '_importAnnotation',
    },
    initialize: function (options)
    {
        this.model.each(this.insert, this);
        this.model.on('add', this.insert, this);
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
        data.url = this.url;
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        //this.model.each(this.renderEntry);
        this.$("table").tablesorter({ widgets: ['zebra'] });
        this.$("table").trigger('update');
        return this;
    },
    insert: function (model, collection, options)
    {
        var view = new AnnotationEntryView({ model: model, parent: this, url: this.url });
        this.$('tbody').append(view.render().el);
        this.$("table").trigger('update');
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
