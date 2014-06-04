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
var ImageListEntryView = Backbone.View.extend({
    model: HUGnet.Device,
    tagName: 'tr',
    template: '#ImageListViewEntryTemplate',
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
HUGnet.ImageListView = Backbone.View.extend({
    template: "#ImageListViewTemplate",
    templatebase: 'ImageListView',
    url: '/HUGnetLib/HUGnetLibAPI.php',
    readonly: false,
    views: {},
    sorted: false,
    sorting: [[1,0]],
    viewed: 0,
    filter: {},
    events: {
    },
    initialize: function (options)
    {
        if (options) {
            if (options.url) {
                this.url = options.url;
            }
            if (options.filter) {
                this.filter = options.filter;
            }
        }
        this.model.on('add', this.insert, this);
        this.model.on('change:publish', this.showhide, this);
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
        var id = model.get("id");
        this.views[id] = new ImageListEntryView({
            model: model,
            parent: this,
            templatebase: this.templatebase
        });
        this.$('tbody').append(this.views[id].render().el);
        this.$('table').trigger('update');
        this.showhide(model);
    },
    showhide: function (model)
    {
        var id = model.get("id");
        if (this.filter.publish && this.views[id]) {
            if (model.get("publish") == this.filter.publish) {
                this.views[id].$el.show();
            } else {
                this.views[id].$el.hide();
            }
        }
    }
});
