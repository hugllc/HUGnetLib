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
 * @subpackage Tests
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
* @subpackage Tests
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var GatewayListEntryView = Backbone.View.extend({
    model: HUGnet.Gateway,
    template: '#GatewayListHeaderTemplate',
    parent: null,
    events: {
        'click .view': 'view',
        'click .export': 'export'
    },
    initialize: function (options)
    {
        this.model.bind('change', this.render, this);
        this.model.bind('remove', this.remove, this);
        this.parent = options.parent;
    },
    view: function (e)
    {
        this.parent.trigger("view", this.model);
    },
    export: function (e)
    {
        this.parent.trigger("export", this.model);
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
        _.extend(data, HUGnet.viewHelpers);
        var template = '<h3 data-gatewaykey="<%= id %>">'+$(this.template).html()+'</h3><div></div>';
        this.$el.html(
            _.template(
                template,
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
* @subpackage Tests
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.GatewayList = Backbone.View.extend({
    template: "#GatewayListTemplate",
    url: '/HUGnetLib/HUGnetLibAPI.php',
    readonly: false,
    devices: null,
    devlist: null,
    refresh: 60,
    GatewayKeys: {},
    events: {
    },
    initialize: function (options)
    {
        if (options) {
            if (options.url) {
                this.url = options.url;
            }
            if (options.readonly) {
                this.readonly = options.readonly;
            }
            if (options.devices) {
                this.devices = options.devices;
            }
        }
        this.model.each(this.insert, this);
        this.model.bind('add', this.insert, this);
        this.model.bind('savefail', this.saveFail, this);
        this.model.startRefresh();
        this.devices.on("change", function() {
            this.$("#gatewaylist" ).accordion("refresh");
        }, this);
        
        this.devlist = new HUGnet.DeviceTableView({
            model: this.devices,
            url: this.url,
            template: "#GatewayListDeviceViewEntryTemplate",
            header: "#GatewayListDeviceHeaderTemplate",
        });
        this.devlist.render();
    },
    activate: function (event, ui, self)
    {
        var GatewayKey = parseInt(ui.newHeader.attr("data-gatewaykey"), 10);
        if (ui.newHeader.attr("id") != undefined) {
            var filter = {Publish: 1, GatewayKey: GatewayKey};
            self.devices.fetch(filter);
            self.devlist.filter(filter);
            ui.newPanel.html(self.devlist.el);
            this.$("#gatewaylist" ).accordion("refresh");
        }
    },
    saveFail: function (msg)
    {
        //alert("Save Failed: " + msg);
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
        var self = this;
        _.extend(data, HUGnet.viewHelpers);
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        this.$( "#gatewaylist" ).accordion({
            header: "h3",
            collapsible: true,
            active: false,
            activate: function (event, ui) {self.activate(event, ui, self)}
        });
        return this;
    },
    insert: function (model, collection, options)
    {
        var view = new GatewayListEntryView({ model: model, parent: this });
        this.$('#gatewaylist').append(view.render().el);
        this.$("#gatewaylist" ).accordion("refresh");
    }
});
