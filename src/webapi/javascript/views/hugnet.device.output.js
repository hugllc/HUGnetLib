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
 * @subpackage DeviceOutputs
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
* @subpackage DeviceOutputs
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var DeviceOutputPropertiesView = Backbone.View.extend({
    template: '#DeviceOutputPropertiesTemplate',
    tTemplate: '#DeviceOutputPropertiesTitleTemplate',
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
        this.model.on('change', this.render, this);
        this.model.on('saved', this.saveSuccess, this);
        this.model.on('savefail', this.saveFail, this);
    },
    saveSuccess: function (e)
    {
        if (this._close) {
            this.model.off('change', this.render, this);
            this.model.off('saved', this.saveSuccess, this);
            this.model.off('savefail', this.saveFail, this);
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
        console.log(value);
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
        
        console.log(data);
        this.model.set(data);
        this.model.save();
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
        console.log(data);
        _.extend(data, HUGnet.viewHelpers);
        var i;
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
    }
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage DeviceOutputs
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var DeviceOutputEntryView = Backbone.View.extend({
    model: HUGnet.DeviceOutput,
    tagName: 'tr',
    template: '#DeviceOutputEntryTemplate',
    parent: null,
    events: {
        'click .properties': 'properties'
    },
    initialize: function (options)
    {
        this.model.bind('change', this.render, this);
        this.model.bind('remove', this.remove, this);
        this.parent = options.parent;
    },
    properties: function (e)
    {
        var view = new DeviceOutputPropertiesView({ model: this.model });
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
        this.$el.html(
            _.template(
                $(this.template).html(),
                this.model.toJSON()
            )
        );
        return this;
    }
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage DeviceOutputs
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DeviceOutputsView = Backbone.View.extend({
    model: HUGnet.DeviceOutputs,
    template: "#DeviceOutputListTemplate",
    rows: 0,
    events: {
    },
    initialize: function (options)
    {
        //this.model.bind('add', this.insert, this);
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
        /* insert all of the models */
        this.model.each(this.insert, this);
        this.$("tr").removeClass("odd").removeClass("even");
        this.$("tr:odd").addClass("odd");
        this.$("tr:even").addClass("even");
        return this;
    },
    insert: function (model, key)
    {
        var view = new DeviceOutputEntryView({ model: model, parent: this });
        this.$('tbody').append(view.render().el);
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
