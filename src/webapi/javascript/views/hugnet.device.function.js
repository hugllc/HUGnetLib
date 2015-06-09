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
 * @subpackage DeviceChannels
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
* @subpackage DeviceChannels
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var DeviceFunctionEntryView = Backbone.View.extend({
    model: HUGnet.DeviceChannel,
    tagName: 'tr',
    template: '#DeviceFunctionEntryTemplate',
    parent: null,
    events: {
        'change [name="label"]': 'changeLabel',
        'change [name="units"]': 'changeUnits',
        'change [name="decimals"]': 'changeDecimals',
        'change [name="dataType"]': 'changeDataType',
    },
    initialize: function (options)
    {
        if (options.template) {
            this.template = options.template;
        }
        this.model.bind('change', this.render, this);
        this.model.bind('remove', this.remove, this);
        this.parent = options.parent;
        this._template = _.template($(this.template).html());
    },
    changeLabel: function ()
    {
        this.model.set("label", this.$('[name="label"]').val());
    },
    changeUnits: function ()
    {
        this.model.set("units", this.$('[name="units"]').val());
    },
    changeDecimals: function ()
    {
        this.model.set("decimals", this.$('[name="decimals"]').val());
    },
    changeDataType: function ()
    {
        this.model.set("dataType", this.$('[name="dataType"]').val());
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
        if (typeof data.port != "string") {
            data.port = " - ";
        }
        _.extend(data, HUGnet.viewHelpers);
        this.$el.html(this._template(data));
        return this;
    }
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage DeviceChannels
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DeviceFunctionsView = Backbone.View.extend({
    model: HUGnet.DeviceFunctions,
    template: "#DeviceFunctionListTemplate",
    rowTemplate: "#DeviceFunctionEntryTemplate",
    rows: 0,
    events: {
    },
    initialize: function (options)
    {
        if (options.template) {
            this.template = options.template;
        }
        if (options.rowTemplate) {
            this.rowTemplate = options.rowTemplate;
        }
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
        _.extend(data, HUGnet.viewHelpers);
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
        var view = new DeviceFunctionEntryView({ 
            model: model, 
            parent: this,
            template: this.rowTemplate
        });
        this.$('table:first').children('tbody').append(view.render().el);
    },
    popup: function (view)
    {
        this.$el.append(view.render().el);
        view.$el.dialog({
            modal: true,
            draggable: true,
            width: 300,
            resizable: false,
            title: view.title(),
            dialogClass: "window",
            zIndex: 1000
        });
    }
});
