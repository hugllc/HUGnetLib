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
* @subpackage Tests
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DeviceList = Backbone.View.extend({
    tabs: undefined,
    id: "devices-tabs",
    readonly: false,
    data: {},
    filter: {publish: 1},
    GatewayKey: null,
    url: '/HUGnetLib/HUGnetLibAPI.php',
    tabTemplate: '<li style="white-space: nowrap;"><a href="#{href}">#{label}</a> <span name="#{href}" class="ui-icon ui-icon-close">Remove Tab</span></li>',
    initialize: function (options)
    {
        if (options) {
            if (options.url) {
                this.url = options.url;
            }
            if (options.id) {
                this.id = options.id;
            }
            if (options.readonly) {
                this.readonly = options.readonly;
            }
            if (typeof options.filter === 'object') {
                this.filter = options.filter;
            }
        }
        this.devices = new HUGnet.DeviceListView({
            model: options.devices,
            gateways: options.gateways,
            url: this.url,
            readonly: this.readonly,
            filter: this.filter,
        });
        this.render();
    },
    render: function ()
    {
        this.$el.html('<div id="'+this.id+'"><ul></ul></div>');
        var self = this;
        this.tabs = $('#'+this.id).tabs({
            active: 0,
            cookie: {
                // store a session cookie
                expires: 10
            },
            activate: function(event, ui) 
            {
                self.$(".tablesorter").trigger("update");
                var tag = ui.newPanel.selector;
                tag = tag.replace("#", "");
                if (self.data[tag]) {
                    self.data[tag].trigger("update");
                }
            }
        });
        var tag = this.id+'-views';
        this.tabs.find( ".ui-tabs-nav" ).append('<li><a href="#'+tag+'">Device List</a></li>');
        this.tabs.append( '<div id="'+tag+'"></div>' );
        $('#'+tag).html(this.devices.render().el);
        this.tabs.tabs("refresh");
        this.tabs.tabs("option", "active", 0);

        /* Further tabs will have a close button */
        //this.tabs.tabs("option", "tabTemplate", '<li style="white-space: nowrap;"><a href="#{href}">#{label}</a> <span name="#{href}" class="ui-icon ui-icon-close">Remove Tab</span></li>');
        /* close icon: removing the tab on click */
        var tabs = this.tabs;
        $(document).on( "click", "#"+this.id+" span.ui-icon-close", function(event, ui) {
            var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
            self.data[panelId].exit();
            delete self.data[panelId];
            $( "#" + panelId ).remove();
            tabs.tabs( "refresh" );
        });

        this.devices.bind(
            "view",
            function (device)
            {
                this.deviceTab(device);
            },
            this
        );
        this.devices.bind(
            "export",
            function (device)
            {
                this.exportTab(device);
            },
            this
        );
    },
    deviceTab: function (device)
    {
        var self = this;
        var tag = this.id + device.get("id");
        if (this.data[tag] !== undefined) {
            return;
        }
        this.data[tag] = new HUGnet.DataView({
            parent: tag,
            model: device,
            TestID: 1,
            url: this.url
        });
        var title = 'Device "' + device.get("DeviceID") + '" History';

        //this.tabs.tabs("add", tag, title);
        this.tabs.find( ".ui-tabs-nav" ).append('<li><a href="#'+tag+'">'+title+'</a> <span name="#{href}" class="ui-icon ui-icon-close">Remove Tab</span></li>');
        this.tabs.append( "<div id='"+tag+"'></div>" );
        $("#"+tag).html(this.data[tag].render().el);
        this.tabs.tabs("refresh");
        this.tabs.tabs("option", "active", -1);
    },
    exportTab: function (device)
    {
        var self = this;
        var tag = "tabs-export" + device.get("id");
        if (this.data[tag] !== undefined) {
            return;
        }
        this.data[tag] = new HUGnet.ExportView({
            parent: tag,
            model: device,
            TestID: 1,
            url: this.url
        });
        var title = 'Export Device "' + device.get("DeviceName") + '" Data';

        //this.tabs.tabs("add", tag, title);
        this.tabs.find( ".ui-tabs-nav" ).append('<li><a href="#'+tag+'">'+title+'</a> <span name="#{href}" class="ui-icon ui-icon-close">Remove Tab</span></li>');
        this.tabs.append( "<div id='"+tag+"'></div>" );
        $("#"+tag).html(this.data[tag].render().el);
        this.tabs.tabs("refresh");
        this.tabs.tabs("option", "active", -1);
    }
});
