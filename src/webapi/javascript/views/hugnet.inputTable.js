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
 * @subpackage InputTables
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
* @subpackage InputTables
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var InputTablePropertiesView = Backbone.View.extend({
    template: '#InputTablePropertiesTemplate',
    tTemplate: '#InputTablePropertiesTitleTemplate',
    tagName: 'div',
    _close: false,
    events: {
        'click .SaveInputTable': 'saveclose',
        'change select.type': 'save',
    },
    initialize: function (options)
    {
        this.model.on('change', this.render, this);
        this.model.on('savefail', this.saveFail, this);
    },
    save: function (e)
    {
        this.setTitle( " [ Saving...] " );
        var i, output = {};
        var data = this.$('form').serializeArray();
        for (i in data) {
            output[data[i].name] = data[i].value;
        }
        output.params = this.model.get('params');
        for (i in output.params) {
            output[i] = output['params['+i+']'];
            output.params[i]['value'] = output['params['+i+']'];
            delete output['params['+i+']'];
        }
        this.model.set(output);
        this.model.save();
    },
    saveclose: function (e)
    {
        this._close = true;
        this.save(e);
        this.close();
    },
    saveFail: function ()
    {
        this.setTitle();
        //alert("Save Failed");
    },
    close: function ()
    {
        if (this._close) {
            this.model.off('change', this.render, this);
            this.model.off('savefail', this.saveFail, this);
            this.model.off('saved', this.close, this);
            this.model.off('saved', this.render, this);
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
        this.model.off('saved', this.render, this);
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
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage InputTables
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var InputTableEntryView = Backbone.View.extend({
    tagName: 'tr',
    template: '#InputTableEntryTemplate',
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
        var view = new InputTablePropertiesView({ model: this.model });
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
* @subpackage InputTables
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.InputTablesView = Backbone.View.extend({
    template: "#InputTableListTemplate",
    url: '/HUGnetLib/HUGnetLibAPI.php',
    events: {
        'click .new': 'create'
    },
    initialize: function (options)
    {
        if (options) {
            if (options.url) this.url = options.url;
        }
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
        this.$("table").tablesorter({ widgets: ['zebra'] });
        this.$("table").trigger('update');
        return this;
    },
    insert: function (model, collection, options)
    {
        var view = new InputTableEntryView({ model: model, parent: this });
        this.$('tbody').append(view.render().el);
        this.$("table").trigger('update');
    },
    create: function ()
    {
        var self = this;
        var ret = $.ajax({
            type: 'GET',
            url: this.url,
            dataType: 'json',
            cache: false,
            data:
            {
                "task": "inputTable",
                "action": "put",
                "data": {
                    "name": 'New Table',
                }
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
            function ()
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
