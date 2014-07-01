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
* @subpackage Tests
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.ServerConfig = Backbone.View.extend({
    tabs: undefined,
    url: "/HUGnetLib/HUGnetLibAPI.php",
    id: "tabs-serverconfig",
    itables: null,
    initialize: function (options)
    {
        if (options) {
            if (options.url) {
                this.url = options.url;
            }
            if (options.id) {
                this.id = options.id;
            }
        }
        this.itables = new HUGnet.InputTables();
        this.itables.fetch();
        
        this.devices = new HUGnet.DevicesView({
            model: options.devices,
            url: this.url
        });
        this.gateways = new HUGnet.GatewaysView({
            model: options.gateways,
            url: this.url
        });
        this.inputTables = new HUGnet.InputTablesView({
            model: this.itables,
            url: this.url
        });
        this.imageConfig = new HUGnet.ImageConfigView({
            model: options.images,
            url: this.url
        });
        /*
        var otables = new HUGnet.OutputTables();
        otables.fetch();
        this.outputTables = new HUGnet.OutputTablesView({
            model: otables,
            url: this.url
        });
        var ptables = new HUGnet.ProcessTables();
        ptables.fetch();
        this.processTables = new HUGnet.ProcessTablesView({
            model: ptables,
            url: this.url
        });
        */
        this.render();
    },
    render: function ()
    {
        var self = this;
        this.$el.html(
            '<div id="'+this.id+'"><ul></ul></div>'
        );
        this.tabs = $('#'+this.id).tabs({
            collapsible: false,
            cookie: {
                // store a session cookie
                expires: 10
            },
            activate: function(event, ui) {
                self.$(".tablesorter").trigger("update");
            }
        });
        this.tabs.find( ".ui-tabs-nav" ).append('<li><a href="#'+this.id+'-devices">Device Information</a></li>');
        this.tabs.append('<div id="'+this.id+'-devices"></div>');
        $('#'+this.id+'-devices').html(this.devices.render().el);

        this.tabs.find( ".ui-tabs-nav" ).append('<li><a href="#'+this.id+'-gateways">Gateway Information</a></li>');
        this.tabs.append('<div id="'+this.id+'-gateways"></div>');
        $('#'+this.id+'-gateways').html(this.gateways.render().el);
        
        this.tabs.find( ".ui-tabs-nav" ).append('<li><a href="#'+this.id+'-imageConfig">Images</a></li>');
        this.tabs.append('<div id="'+this.id+'-imageConfig"></div>');
        $('#'+this.id+'-imageConfig').html(this.imageConfig.render().el);
        
        this.tabs.find( ".ui-tabs-nav" ).append('<li><a href="#'+this.id+'-inputTable">Input Tables</a></li>');
        this.tabs.append('<div id="'+this.id+'-inputTable"></div>');
        $('#'+this.id+'-inputTable').html(this.inputTables.render().el);
        /*
        this.tabs.find( ".ui-tabs-nav" ).append('<li><a href="#'+this.id+'-outputTable">Output Tables</a></li>');
        this.tabs.append('<div id="'+this.id+'-outputTable"></div>');
        $('#'+this.id+'-outputTable').html(this.outputTables.render().el);
        this.tabs.find( ".ui-tabs-nav" ).append('<li><a href="#'+this.id+'-processTable">Process Tables</a></li>');
        this.tabs.append('<div id="'+this.id+'-processTable"></div>');
        $('#'+this.id+'-processTable').html(this.processTables.render().el);
        */

        this.tabs.tabs("refresh");
        this.tabs.tabs("option", "active", 0);
    }
});

