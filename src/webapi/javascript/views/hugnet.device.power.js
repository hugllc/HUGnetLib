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
 * @subpackage DevicePowers
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
* @subpackage DevicePowers
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.8
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var DevicePowerPropertiesView = Backbone.View.extend({
    template: '#DevicePowerPropertiesTemplate',
    tTemplate: '#DevicePowerPropertiesTitleTemplate',
    tagName: 'div',
    _close: false,
    events: {
        'click .save': 'saveclose',
        'change select.type': 'save',
        'change select.id': 'save',
        'change #setTable': 'settable'
    },
    initialize: function (options)
    {
        _.bindAll(this, "saveSuccess", "saveFail");
        this.model.on('change', this.render, this);
        this._template = _.template($(this.template).html());
        this._tTemplate = _.template($(this.tTemplate).html());
    },
    saveSuccess: function (e)
    {
        if (this._close) {
            this.model.off('change', this.render, this);
            this.remove();
        }
    },
    saveFail: function (msg)
    {
        this.setTitle("");
        //alert("Input Faled: " + msg);
    },
    settable: function (e)
    {
        var value = this.$("#setTable").val();
        this.$("#setTable").val(0);
        this.model.settable(value);
    },
    saveclose: function (e)
    {
        this._close = true;
        this.save(e);
    },
    save: function (e)
    {
        this.setTitle( " [ Saving...] " );
        var data = this.$('form').serializeObject();
        this.model.save(
            data,
            { "success": this.saveSuccess, "error": this.saveFail, wait: true }
        );
        this.setTitle();
    },
    setTitle: function (extra)
    {
        if (extra == "undefined") {
            extra = "";
        }
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
        var i;
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
        var data = this.model.toJSON();
        return this._tTemplate(data);
    }
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage DevicePowers
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.8
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var DevicePowerEntryView = Backbone.View.extend({
    model: HUGnet.DevicePower,
    tagName: 'tr',
    template: '#DevicePowerEntryTemplate',
    parent: null,
    events: {
        'click .properties': 'properties'
    },
    initialize: function (options)
    {
        this.model.bind('change', this.render, this);
        this.model.bind('remove', this.remove, this);
        this.parent = options.parent;
        this._template = _.template($(this.template).html());
    },
    properties: function (e)
    {
        var view = new DevicePowerPropertiesView({ model: this.model });
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
        var data = this.model.toJSON();
        this.$el.html(this._template(data));
        return this;
    }
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage DevicePowers
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.8
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DevicePowersView = Backbone.View.extend({
    model: HUGnet.DevicePowers,
    template: "#DevicePowerListTemplate",
    rows: 0,
    events: {
    },
    initialize: function (options)
    {
        //this.model.bind('add', this.insert, this);
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
        /* insert all of the models */
        this.model.each(this.insert, this);
        this.$("tr").removeClass("odd").removeClass("even");
        this.$("tr:odd").addClass("odd");
        this.$("tr:even").addClass("even");
        return this;
    },
    insert: function (model, key)
    {
        var view = new DevicePowerEntryView({ model: model, parent: this });
        this.$('table:first').children('tbody').append(view.render().el);
        //this.$('tbody').append(view.render().el);
    },
    popup: function (view)
    {
        this.$el.append(view.render().el);
        view.$el.dialog({
            modal: true,
            draggable: true,
            width: 500,
            resizable: false,
            title: view.title(),
            dialogClass: "window",
            zIndex: 1000
        });
    }
});
