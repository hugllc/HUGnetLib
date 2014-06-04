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
 * @subpackage Images
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
* @subpackage Images
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.Image = Backbone.Model.extend({
    idAttribute: 'id',
    defaults:
    {
        id: 0,
        name: '',
        image: '',
        imagetype: '',
        height: 0,
        width: 0,
        desc: '',
        baseavg: '15MIN',
        points: '',
        length: 0,
        averageTypes: {},
        data: {},
        publish: 1
    },
    points: {},
    lock: false,
    /**
    * This function initializes the object
    */
    initialize: function(attrib)
    {
        this.points = new HUGnet.ImagePoints({});
        this._resetPoints();
        this.points.on("change", function() { this.trigger("change"); }, this);
    },
    /**
    * This function initializes the object
    */
    newpoint: function()
    {
        this.points.add({pretext: "New Point"});
        this.flushpoints();
    },
    /**
    * This function initializes the object
    */
    flushpoints: function()
    {
        this.set("points", this.points.toJSON());
        this._resetPoints();
    },
    _resetPoints: function()
    {
        this.points.reset(this.get('points'));
        this.set("length", this.points.length);
    },
    /**
    * Gets infomration about a device.  This is retrieved from the database only.
    *
    * @param id The id of the device to get
    *
    * @return null
    */
    fetch: function()
    {
        var id = this.get('id');
        if ((id !== 0) && !this.lock) {
            var myself = this;
            $.ajax({
                type: 'GET',
                url: this.url(),
                cache: false,
                dataType: 'json',
                data:
                {
                    "task": "image",
                    "action": "get",
                    "id": id
                }
            }).done(
                function (data)
                {
                    myself.set(data);
                    myself._resetPoints();
                }
            );
        }
    },
    /**
    * Gets infomration about a device.  This is retrieved from the database only.
    *
    * @param id The id of the device to get
    *
    * @return null
    */
    refresh: function()
    {
        var id = this.get('id');
        if ((id !== 0) && !this.lock) {
            var self = this;
            $.ajax({
                type: 'GET',
                url: this.url(),
                dataType: 'json',
                cache: false,
                data:
                {
                    "task": "image",
                    "action": "get",
                    "id": id,
                }
            }).done(
                function (data)
                {
                    if ((data !== undefined) && (data !== null) && (typeof data === "object")) {
                        self.trigger('refresh');
                        self.set(data);
                        self._resetPoints();
                        self.trigger('fetchdone');
                        self.trigger('sync', self);
                    } else {
                        self.trigger('refreshfail', "saved failed on server");
                    }
                }
            ).fail(
                function ()
                {
                    self.trigger('refreshfail', "failed to contact server");
                }
            );
        }
    },
    /**
    * Gets infomration about a device.  This is retrieved from the database only.
    *
    * @param id The id of the device to get
    *
    * @return null
    */
    getReading: function(date, type)
    {
        var id = this.get('id');
        if ((id !== 0) && !this.lock) {
            if (!date) {
                var date = 0;
            }
            if (!type) {
                var type = "";
            }
            var self = this;
            $.ajax({
                type: 'GET',
                url: this.url(),
                dataType: 'json',
                cache: false,
                data:
                {
                    "task": "image",
                    "action": "getreading",
                    "id": id,
                    "data": {
                        "date": (date / 1000),
                        "type": type,
                    }
                }
            }).done(
                function (data)
                {
                    if ((data !== undefined) && (data !== null) && (typeof data === "object")) {
                        self.set("data", data);
                        self.trigger('datasync', self);
                    } else {
                        self.trigger('datasyncfail', "getReading failed on server");
                    }
                }
            ).fail(
                function ()
                {
                    self.trigger('datasyncfail', "failed to contact server");
                }
            );
        }
    },
    /**
    * Gets infomration about a device.  This is retrieved from the database only.
    *
    * @param id The id of the device to get
    *
    * @return null
    */
    save: function()
    {
        var id = this.get('id');
        if (id !== 0) {
            var self = this;
            self.set("points", self.points.toJSON())
            var data = self.toJSON();
            $.ajax({
                type: 'POST',
                url: this.url(),
                dataType: 'json',
                cache: false,
                data:
                {
                    "task": "image",
                    "action": "put",
                    "id": id,
                    "data": data
                }
            }).done(
                function (data)
                {
                    if ((data !== undefined) && (data !== null) && (typeof data === "object")) {
                        self.trigger('saved');
                        self.set(data);
                        self._resetPoints();
                        self.trigger('fetchdone');
                        self.trigger('sync', self);
                    } else {
                        self.trigger('savefail', "saved failed on server");
                    }
                }
            ).fail(
                function ()
                {
                    self.trigger('savefail', "failed to contact server");
                }
            );
        }
    },
    /**
     * Gets infomration about a device.  This is retrieved from the database only.
     *
     * @param id The id of the device to get
     *
     * @return null
     */
    removeImg: function()
    {
        var id = this.get('id');
        if (id !== 0) {
            var self = this;
            self.set("points", self.points.toJSON())
            var data = self.toJSON();
            $.ajax({
                type: 'GET',
                url: this.url(),
                   dataType: 'json',
                   cache: false,
                   data:
                   {
                        "task": "image",
                        "action": "delete",
                        "id": id,
                   }
            }).done(
                function (data)
                {
                    if (data == "success") {
                        self.trigger('destroy', self, self.collection, {});
                        self.trigger('sync', self, self.collection);
                    } else {
                        self.trigger('savefail', "delete failed on server");
                    }
                }
            ).fail(
                function ()
                {
                    self.trigger('savefail', "failed to contact server");
                }
            );
        }
    },
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage Images
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.Images = Backbone.Collection.extend({
    url: '/HUGnetLib/HUGnetLibAPI.php',
    model: HUGnet.Image,
    refresh: 300,
    start: 0,
    limit: 20,
    timer: null,
    initialize: function (options)
    {
        if (options) {
            if (options.url) this.url = options.url;
        }
        this.on('add', this.update, this);
    },
    comparator: function (model)
    {
        return parseInt(model.get("id"), 10);
    },
    startRefresh: function (refresh)
    {
        if (this.timer == null) {
            refresh && (this.refresh = refresh);
            this._refreshSetTimeout();
        }
    },
    stopRefresh: function ()
    {
        if (this.timer != null) {
            clearTimeout(this.timer);
            this.timer = null;
        }
    },
    _refresh: function ()
    {
        if (this.timer != null) {
            this.update();
            this._refreshSetTimeout();
        }
    },
    _refreshSetTimeout: function ()
    {
        var self = this;
        this.timer = setTimeout(
            function () {
                self._refresh();
            },
            (this.refresh * 1000)
        );
    },
    /**
    * Gets information about a device.  This is retrieved directly from the device
    *
    * This function is for use of the device list
    *
    * @param id The id of the device to get
    *
    * @return null
    */
    fetch: function ()
    {
        var self = this;
        var ret = $.ajax({
            type: 'GET',
            url: this.url,
            dataType: 'json',
            cache: false,
            data: {
                "task": "image", 
                "action": "list", 
                "data": {
                    "limit": self.limit,
                    "start": self.start
                }
            }
        });
        ret.done(
            function (data)
            {
                self.add(data);
                if (data.length < self.limit) {
                    self.start = 0;
                } else {
                    self.start += data.length;
                    self.fetch();
                }
            }
        );
    },
    update: function (model, collection, options)
    {
        if (typeof model == "object") {
            model.refresh();
        }
    }
});
