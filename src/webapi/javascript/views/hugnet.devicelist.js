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
var DeviceListEntryView = Backbone.View.extend({
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
HUGnet.DeviceListView = Backbone.View.extend({
    template: "#DeviceListViewTemplate",
    templatebase: 'DeviceListView',
    url: '/HUGnetLib/HUGnetLibAPI.php',
    readonly: false,
    views: {},
    filter: {},
    sorted: false,
    sorting: [[1,0]],
    viewed: 0,
    events: {
        'click .goFilter': 'goFilter'
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
            if (typeof options.filter === 'object') {
                this.filter = options.filter;
            }
            if (options.templatebase) {
                this.templatebase = options.templatebase;
                this.template = '#' + this.templatebase + 'Template';
            }
        }
        this.model.each(this.insert, this);
        this.model.on('add', this.insert, this);
        this.model.on('sync', this.insert, this);
        this.model.setRefresh();
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
        this.$('table').tablesorter({
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
        if (this.checkFilter(model, this.filter) && (this.views[id] == undefined)) {
            this.views[id] = new DeviceListEntryView({
                model: model,
                parent: this,
                templatebase: this.templatebase
            });
            this.$('tbody').append(this.views[id].render().el);
            this.setView(id, true);
            this.$('table').trigger('update');
        }
    },
    update: function()
    {
        this.viewed = 0;
        for (var id in this.views) {
            this.setView(id);
        }
        this.$el.trigger('update');
        this.$('table').trigger('update');
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
    checkFilter: function(model, filter)
    {

        for (var key in filter) {
            var value = model.get(key);
            if (typeof filter[key] === 'string') {
                if (value.toString().indexOf(filter[key]) === -1) {
                    return false;
                }
            } else if (value != filter[key]) {
                return false;
            }
        }
        return true;
    },
    goFilter: function()
    {
        var activeFilter = this.$(".activeFilter").val();
        var fieldFilter = this.$(".fieldFilter").val();
        var searchFilter = this.$(".searchFilter").val();
        var filter = {};
        if (activeFilter === "1") {
            filter.Active = 1;
        } else if (activeFilter === "0") {
            filter.Active = 0;
        }
        if ((searchFilter !== "") && (fieldFilter !== "")) {
            filter[fieldFilter] = searchFilter;
        }
        for (var view in this.views) {
            var show = true;
            for (var key in filter) {
                if (!this.checkFilter(this.views[view].model, filter)) {
                    show = false;
                }
            }
            this.setView(view, show);
        }
    }
});
