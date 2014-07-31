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
* @subpackage Devices
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.Annotation = Backbone.Model.extend({
    idAttribute: 'id',
    defaults:
    {
        id: 0,
        date: 0,
        test: 0,
        testdate: 0,
        text: "",
        author: ""
    },
    /**
    * This function initializes the object
    */
    initialize: function(attrib)
    {
    },
    /**
    * This function initializes the object
    */
    fix: function(attributes)
    {
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
        var myself = this;
        $.ajax({
            type: 'GET',
            url: this.url(),
            cache: false,
            dataType: 'json',
            data:
            {
                "task": "annotation",
                "action": "get",
                "id": this.get("id"),
            }
        }).done(
            function (data)
            {
                myself.set(data);
            }
        );
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
        var self = this;
        $.ajax({
            type: 'GET',
            url: this.url(),
            dataType: 'json',
            cache: false,
            data:
            {
                "task": "annotation",
                "action": "get",
                "id": this.get("id"),
            }
        }).done(
            function (data)
            {
                if ((data !== undefined) && (data !== null) && (typeof data === "object")) {
                    self.trigger('refresh');
                    self.set(data);
                    self.trigger('fetchdone');
                    self.trigger('sync');
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
        var self = this;
        $.ajax({
            type: 'POST',
            url: this.url(),
            dataType: 'json',
            cache: false,
            data:
            {
                "task": "annotation",
                "action": "put",
                "id": this.get("id"),
                "data": self.toJSON()
            }
        }).done(
            function (data)
            {
                if ((data !== undefined) && (data !== null) && (typeof data === "object")) {
                    self.trigger('saved');
                    self.set(data);
                    self.trigger('fetchdone');
                    self.trigger('sync');
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
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage Devices
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.Annotations = Backbone.Collection.extend({
    url: '/HUGnetLib/HUGnetLibAPI.php',
    model: HUGnet.Annotation,
    refresh: 300,
    timer: null,
    initialize: function (options)
    {
        if (options) {
            if (options.url) this.url = options.url;
        }
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
    * Gets infomration about a device.  This is retrieved directly from the device
    *
    * This function is for use of the device list
    *
    * @param id The id o
    *
    * @return null
    */
    fetch: function (test, since, until)
    {
        var self = this;
        var ret = $.ajax({
            type: 'GET',
            url: this.url,
            dataType: 'json',
            cache: false,
            data: {
                "task": "annotation", 
                "action": "list",
                "data": { 
                    test: test, 
                    since: since / 1000, 
                    until: until / 1000,
                }
            }
        });
        ret.done(
            function (data)
            {
                self.reset();
                self.add(data);
            }
        );
    },
    /**
    * Gets infomration about a device.  This is retrieved from the database only.
    *
    * @param id The id of the device to get
    *
    * @return null
    */
    update: function()
    {
        this.forEach(
            function (element, index, list)
            {
                element.refresh();
            }
        );
    }
});
