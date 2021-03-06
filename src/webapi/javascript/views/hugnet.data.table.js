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
 * @subpackage DataPoints
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
* @subpackage DataPoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var Row = Backbone.View.extend({
    model: HUGnet.History,
    tagName: 'tr',
    template: '#DataPointTemplate',
    fields: {},
    classes: {},
    events: {
    },
    initialize: function (options)
    {
        this.fields = options.fields;
        this.classes = options.classes;
        this.model.bind('update', this.render, this);
        this.model.bind('remove', this.remove, this);
        this._template = _.template($(this.template).html());
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
        var out = "";
        var i;
        var data = this.model.toJSON();
        var d = new Date();
        d.setTime(data.UnixDate);
        data.Date = d.formatHUGnet();
        for (i in this.fields) {
            out += this._template(
                { data: data[this.fields[i]], fieldClass: this.classes[i] }
            );
        }
        this.$el.html(out);
        return this;
    }
});
/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage DataPoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var Header = Backbone.View.extend({
    tagName: 'tr',
    template: '#DataPointHeaderTemplate',
    fields: {},
    classes: {},
    header: {},
    events: {
    },
    rows: 0,
    initialize: function (options)
    {
        this.classes = options.classes;
        this.header = options.header;
        this._template = _.template($(this.template).html());
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
        var header = "";
        var i;
        for (i in this.header) {
            header += this._template(
                { header: this.header[i], classes: this.classes[i] }
            );
        }
        this.$el.html(header);
        return this;
    }
});
/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage DataPoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DataTable = Backbone.View.extend({
    model: HUGnet.Histories,
    tagName: 'table',
    template: '#DataPointTableTemplate',
    fields: {},
    classes: {},
    views: [],
    maxRows: 15,
    rows: 0,
    events: {
    },
    initialize: function (options)
    {
        this.model.bind('sync', this.render, this);
        this.fields = options.fields;
        this.classes = options.classes;
        this.header = new Header({
            header: options.header,
            classes: options.classes
        });
        this._setup();
        this._dateFormats();
//        this._template = _.template($(this.template).html());
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
        this.rows = 0;
        this.$el.html(
            '<caption></caption><thead></thead><tbody></tbody><tfoot></tfoot>'
        );
        this.$head = this.$("thead");
        this.$body = this.$("tbody");
        this.$foot = this.$("tfoot");
        this.$caption = this.$("caption");
        this.$head.html(this.header.render().el);
        _.each(
            this.model.last(this.maxRows),
            function (model)
            {
                this.insert(model);
            },
            this
        );
        this.zebra();
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
    _setup: function ()
    {
        this.$el.html(
            '<caption></caption><thead></thead><tbody></tbody><tfoot></tfoot>'
        );
        this.$head = this.$("thead");
        this.$body = this.$("tbody");
        this.$foot = this.$("tfoot");
        this.$caption = this.$("caption");
        this.$el.trigger('update');
        return this;
    },
    _dateFormats: function ()
    {
        Date.prototype.formatHUGnet = function()
        {
            var m = this.getUTCMonth() + 1;
            var d = this.getUTCDate();
            var Y = this.getUTCFullYear();
            var H = this.getUTCHours();
            var i = this.getUTCMinutes();
            var s = this.getUTCSeconds();

            if (H < 10) {
                H = "0" + H;
            }
            if (i < 10) {
                i = "0" + i;
            }
            if (s < 10) {
                s = "0" + s;
            }
            if (m < 10) {
                m = "0" + m;
            }
            if (d < 10) {
                d = "0" + d;
            }

            return Y + "-" + m + "-" + d + " " + H + ":" + i + ":" + s;
        };
    },
    insert: function (model, collection, options)
    {
        var view = new Row({
            model: model, fields: this.fields, classes: this.classes
        });
        this.$el.prepend(view.render().el);
    },
    remove: function (model, collection, options)
    {
        if (_.isObject(model)) {
            if (this.views[model.id]) {
                this.views[model.id].remove();
                delete this.views[model.id];
            }
        }

    },
    zebra: function ()
    {
        this.$("tr:odd").removeClass('even').addClass("odd");
        this.$("tr:even").removeClass('odd').addClass("even");
    }
});
