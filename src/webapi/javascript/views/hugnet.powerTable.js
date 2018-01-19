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
 * @subpackage PowerTables
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
* @subpackage PowerTables
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.8
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var PowerTablePropertiesView = Backbone.View.extend({
    template: '#PowerTablePropertiesTemplate',
    tTemplate: '#PowerTablePropertiesTitleTemplate',
    tagName: 'div',
    events: {
        'click .SavePowerTable': 'save',
    },
    initialize: function (options)
    {
        _.bindAll(this, "saveSuccess", "saveFail");
        this._template = _.template($(this.template).html());
        this._tTemplate = _.template($(this.tTemplate).html());
    },
    save: function (e)
    {
        this.setTitle( " [ Saving...] " );
        var i, power = {};
        var data = this.$('form').serializeArray();
        for (i in data) {
            power[data[i].name] = data[i].value;
        }
        power.params = this.model.get('params');
        for (i in power.params) {
            power[i] = power['params['+i+']'];
            power.params[i]['value'] = power['params['+i+']'];
            delete power['params['+i+']'];
        }
        this.model.save(
            power,
            {
                "success" : this.saveSuccess, "error": this.saveFail, wait: true
            }

        );
        this.setTitle();
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
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage PowerTables
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.8
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var PowerTableEntryView = Backbone.View.extend({
    tagName: 'tr',
    template: '#PowerTableEntryTemplate',
    parent: null,
    events: {
        'change .action': 'action',
        'click .refresh': 'refresh',
        'click .properties': 'properties'
    },
    initialize: function (options)
    {
        this.model.bind('change', this.render, this);
        this.model.bind('remove', this.remove, this);
        this.model.bind('configfail', this.refreshFail, this);
        this.parent = options.parent;
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
        this.model.fetch();
        var view = new PowerTablePropertiesView({ model: this.model });
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
* @subpackage PowerTables
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.8
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.PowerTablesView = Backbone.View.extend({
    template: "#PowerTableListTemplate",
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
        this._template = _.template($(this.template).html());
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
        this.$el.html(this._template(data));
        //this.model.each(this.renderEntry);
        this.$("table").tablesorter({ widgets: ['zebra'] });
        this.$("table").trigger('update');
        return this;
    },
    insert: function (model, collection, options)
    {
        var view = new PowerTableEntryView({ model: model, parent: this });
        this.$('tbody').append(view.render().el);
        this.$("table").trigger('update');
    },
    create: function ()
    {
        this.model.create(
            {
                name: "New Table"
            },
            { wait: true }
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
    }
});
