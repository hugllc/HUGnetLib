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
var DeviceTableEntryView = Backbone.View.extend({
    model: HUGnet.Device,
    tagName: 'tr',
    template: '#DeviceListViewEntryTemplate',
    parent: null,
    events: {
        'click .view': 'view',
        'click .export': 'export'
    },
    initialize: function (options)
    {
        if (options) {
            if (options.url) {
                this.url = options.url;
            }
            if (options.templatebase) {
                this.template = '#' + options.templatebase + 'EntryTemplate';
            }
        }
        this.model.on('change', this.render, this);
        this.model.on('remove', this.remove, this);
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
* @subpackage Tests
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DeviceTableView = Backbone.View.extend({
    tagName: "table",
    template: "#DeviceListViewEntryTemplate",
    header: "#DeviceListViewHeaderTemplate",
    url: '/HUGnetLib/HUGnetLibAPI.php',
    views: {},
    useFilter: {Publish: 1},
    sorted: false,
    sorting: [[1,0]],
    viewed: 0,
    parent: null,
    initialize: function (options)
    {
        if (options) {
            if (options.url) {
                this.url = options.url;
            }
            if (typeof options.filter === 'object') {
                this.useFilter = options.filter;
            }
            if (options.template) {
                this.template = options.template;
            }
            if (options.header) {
                this.header = options.header;
            }
            if (options.parent) {
                this.parent = options.parent;
            }
        }
        this.model.each(this.insert, this);
        this.model.on('add', this.insert, this);
        this.model.on('sync', this.insert, this);
        this.on('view', this.view, this);
        this.on('export', this.export, this);
        //this.model.startRefresh();
    },
    view: function (model)
    {
        this.parent.trigger("view", model);
    },
    export: function (model)
    {
        this.parent.trigger("export", model);
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
        this.$el.addClass("tablesorter");
        var data = this.model.toJSON();
        _.extend(data, HUGnet.viewHelpers);
        this.$el.prepend(
            _.template(
                $(this.header).html(),
                data
            )
        );
        this.$el.tablesorter({
            widgets: ['zebra'],
            widgetOptions: { zebra: [ 'even', 'odd' ] }
        });
        this.$el.trigger('update');
        this.trigger("update");
        return this;
    },
    insert: function (model, collection, options)
    {
        var id = model.get("DeviceID");
        
        var show = this.checkFilter(model, this.useFilter);
        if (this.views[id] == undefined) {
            this.views[id] = new DeviceTableEntryView({
                model: model,
                parent: this,
                templatebase: this.templatebase
            });
            this.setView(id, show);
            this.$("tbody").append(this.views[id].render().el);
            this.$el.trigger('update');
        } else {
            this.setView(id, show);
        }
    },
    update: function()
    {
        this.viewed = 0;
        for (var id in this.views) {
            var show = this.checkFilter(this.views[id].model, this.useFilter);
            this.setView(id, show);
        }
        this.$el.trigger('update');
        this.trigger('update');
    },
    setView: function(id, show)
    {
        if (show) {
            this.views[id].$el.show();
            this.viewed++;
        } else {
            this.views[id].$el.hide();
        }
    },
    filter: function (filter)
    {
        this.useFilter = filter;
        this.update();
    },
    checkFilter: function(model, filter)
    {

        for (var key in filter) {
            var value = model.get(key);
            if (value == undefined) {
                return true;
            } else if (typeof filter[key] === 'string') {
                if (value.toString().indexOf(filter[key]) === -1) {
                    return false;
                }
            } else if (value != filter[key]) {
                return false;
            }
        }
        return true;
    },
});
