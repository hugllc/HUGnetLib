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
HUGnet.History = Backbone.Model.extend({
    idAttribute: 'Date',
    defaults:
    {
        id: null,
        Date: null,
        UnixDate: null,
        DataIndex: null,
        deltaT: 0,
        converted: false,
        TestID: null,
        Type: 'history',
        Data0: null,
        Data1: null,
        Data2: null,
        Data3: null,
        Data4: null,
        Data5: null,
        Data6: null,
        Data7: null,
        Data8: null,
        Data9: null,
        Data10: null,
        Data11: null,
        Data12: null,
        Data13: null,
        Data14: null,
        Data15: null,
        Data16: null,
        Data17: null,
        Data18: null,
        Data19: null
    },
    initialize: function ()
    {
        this.set("UnixDate", this.get("Date") * 1000);
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
HUGnet.Histories = Backbone.Collection.extend({
    urlPart: '/history',
    baseurl: '',
    model: HUGnet.History,
    LastHistory: 0,
    refresh: null,
    pause: 1,
    limit: 50,
    getLimit: 500,
    count: 0,
    type: "30SEC",
    since: 0,
    until: 0,
    doPoll: false,
    initialize: function (models, options)
    {
        this.reset(models, { silent: true });

        this.bind('add', this.addExtra, this);
        this.bind('sync', this.trim, this);
        if (_.isObject(options)) {
            if (options.baseurl) this.baseurl = options.baseurl;
            this.type = (options.type !== undefined) ? options.type : this.type;
            this.mode = options.mode;
            this.limit = (options.limit !== undefined) ? parseInt(options.limit, 10) : this.limit;
        }
    },
    url: function ()
    {
        return this.baseurl + this.urlPart;
    },
    latest: function (period)
    {
        this.reset();
        this.limit = 0;
        var date = new Date;
        var now = date.getTime()
        this.since = now - (period * 60 * 1000);
        this.LastHistory = this.since;
        this.until = now;
        this.fetch()
    },
    comparator: function (model)
    {
        return model.get("UnixDate");
    },
    percdone: function ()
    {
        var d = (this.until - this.since);
        if (d === 0) {
            return 1;
        }
        return (this.LastHistory - this.since) / d;
    },
    getPeriod: function (since, until)
    {
        this.reset();
        this.limit = 0;
        this.since = since;
        this.LastHistory = this.since;
        this.until = until;
        this.fetch();
    },
    addExtra: function (model, collection, options)
    {
        var last = model.get("UnixDate");
        if (last > this.LastHistory) {
            this.LastHistory = last;
        }
    },
    trim: function (model, collection, options)
    {
        if (this.limit > 0) {
            while (this.length > this.limit) {
                this.shift();
            }
        }
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
    fetch: function (options)
    {
        var self = this;
        var limit = this.getLimit;
        if ((limit > this.limit) && (this.limit !== 0)) {
            limit = this.limit;
        }
        if (typeof options != 'object') {
            options = {};
        }

        var since = Math.round(this.LastHistory / 1000);
        var until = Math.round(this.until / 1000);

        var xhr = new XMLHttpRequest();
        xhr.open('GET', encodeURI(this.url()+'/'+this.type+'?until='+until+'&since='+since+'&limit='+limit+'&order=asc'));
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onload = function() {
            if (xhr.status === 200) {
                if (typeof options.success == 'function') {
                    options.success();
                }
                var data = JSON.parse(xhr.responseText);
                if ((data !== null) && _.isObject(data)) {
                    self.add(data);
                    if ((data.length < self.getLimit) || (self.limit === limit)) {
                        self.trigger('fetchdone');
                        self.trigger('sync');
                    } else {
                        self.trigger('fetchagain', self.percdone());
                        self.fetch();
                    }
                } else {
                    self.trigger('fetchfail');
                }
            } else {
                if (typeof options.error == 'function') {
                    options.error();
                }
            }
        };
        xhr.send();
    },
    clear: function ()
    {
        /* This erases everything and triggers 'remove' events to the views go away */
        this.remove(this.models);
        this.LastHistory = 0;
    }
});
