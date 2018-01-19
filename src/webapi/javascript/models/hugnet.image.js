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
 * @subpackage Images
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
* @subpackage Images
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.8
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.Image = Backbone.Model.extend({
    idAttribute: 'id',
    defaults:
    {
        id: null,
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
        this.points.add({
            pretext: "New Point", 
            id: this.points.length,
            x: 30,
            y: 30
        });
        this.flushpoints();
    },
    validate: function(attributes, options)
    {
        // This sets the points correctly
        this.points.reset(attributes.points);
        delete attributes.points;
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
    imageurl: function(date, type, format)
    {
        if (_.isNumber(date)) {
            date = parseInt(date / 1000);
        } else {
            date = 'now';
        }
        if (!format) {
            format = "PNG";
        }
        return this.url()+"/image?until="+date+"&type="+encodeURIComponent(type)+"&"+encodeURIComponent("data[format]")+"="+encodeURIComponent(format)
    },
    inserturl: function()
    {
        return this.url()+"/insert"
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
            if (_.isNumber(date)) {
                date = parseInt(date / 1000);
            } else {
                date = 'now';
            }
            if (!type) {
                var type = "";
            }
            var self = this;

            var xhr = new XMLHttpRequest();
            xhr.open('GET', encodeURI(this.url()+'/reading?until='+date+'&type='+type));
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var data = JSON.parse(xhr.responseText);
                    if ((data !== null) && _.isObject(data)) {
                        self.set("data", data);
                        self.trigger('datasync', self);
                    } else {
                        self.trigger('datasyncfail', "getReading failed on server");
                    }
                } else {
                }
            };
            xhr.send();
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
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.8
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.Images = Backbone.Collection.extend({
    urlPart: '/image',
    baseurl: '',
    model: HUGnet.Image,
    refresh: 300,
    start: 0,
    limit: 20,
    timer: null,
    initialize: function (options)
    {
        if (options) {
            if (options.baseurl) this.baseurl = options.baseurl;
        }
    },
    url: function ()
    {
        return this.baseurl + this.urlPart;
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
            this.fetch();
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
});
