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
 * @subpackage DataPoints
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
* @subpackage DataPoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var History = Backbone.Model.extend({
    idAttribute: 'Date',
    defaults: function ()
    {
        return {
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
            Data19: null,
        };
    },
    initialize: function ()
    {
        this.set("UnixDate", this.get("Date"));
    },
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage DataPoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
window.Histories = Backbone.Collection.extend({
    url: '/HUGnetLib/index.php',
    model: History,
    id: undefined,
    LastHistory: 0,
    refresh: null,
    pause: 1,
    limit: 50,
    type: "test",
    doPoll: false,
    initialize: function (models, options)
    {
        this.reset(null, { silent: true });
        this.bind('add', this.addExtra, this);
        this.pause = options.pause;
        this.id = options.id;
        this.mode = options.mode;
    },
    setRefresh: function (refresh)
    {
        this.refresh = refresh;
        if (refresh > 0) {
            this.fetch();
        }
    },
    addExtra: function (model, collection, options)
    {
        var last = model.get("UnixDate");
        if (last > this.LastHistory) {
            this.LastHistory = last;
        }
        while (this.length > this.limit) {
            /* Remove the oldest record */
            this.shift();
        }
    },
    /*
    comparator: function (data)
    {
        return data.get("Date");
    },
    */
    _pollAgain: function ()
    {
        /*
        var self = this;
        if (this.doPoll) {
            setTimeout(
                function () {
                    self.poll();
                },
                (this.pause * 1000)
            );
        }
        */
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
    fetch: function ()
    {
        var self = this;
        $.ajax({
            type: 'GET',
            url: this.url,
            dataType: 'json',
            cache: false,
            data: {
                "task": "history",
                "id": this.id,
                "since": this.LastHistory,
                "limit": this.limit,
                "TestID": (this.type == "test") ? 1 : 0,
            },
        }).done(
            function (data)
            {
                if ((data !== undefined) && (data !== null) && (typeof data === "object")) {
                    var i;
                    for (i in data) {
                        self.add(data[i]);
                    }
                }
            }
        );
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
    poll: function ()
    {
        var self = this;
        $.ajax({
            type: 'GET',
            url: this.url,
            dataType: 'json',
            cache: false,
            data: {
                "task": "poll",
                "id": this.id,
                "TestID": (this.type == "test") ? 1 : 0,
            },
        }).done(
            function (data)
            {
                self._pollAgain();
                if ((data !== undefined) && (data !== null) && (typeof data === "object")) {
                    self.add(data);
                }
            }
        );
    },
    stopPoll: function ()
    {
        this.doPoll = false;
    },
    startPoll: function ()
    {
        this.doPoll = true;
        this.poll();
    },
    clear: function ()
    {
        /* This erases everything and triggers 'remove' events to the views go away */
        this.remove(this.models);
    },
});
