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
 * @subpackage Devices
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
* @subpackage Devices
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.Datacollector = Backbone.Model.extend({
    idAttribute: 'id',
    defaults:
    {
        uuid: 0,
        GatewayKey: 0,
        name: "",
        ip: "",
        LastContact: 0,
        SetupString: {},
        LateCheckin: false,
        Config: {},
        Runtime: {}
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
                "task": "datacollector",
                "action": "get",
                "id": this.get("uuid"),
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
                "task": "datacollector",
                "action": "get",
                "id": this.get("uuid"),
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
                "task": "datacollector",
                "action": "put",
                "id": this.get("id"),
                "data": self.saveData()
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
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.Datacollectors = Backbone.Collection.extend({
    url: '/HUGnetLib/HUGnetLibAPI.php',
    model: HUGnet.Datacollector,
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
        var ret = $.ajax({
            type: 'GET',
            url: this.url,
            dataType: 'json',
            cache: false,
            data: {
                "task": "datacollector", "action": "list"
            }
        });
        ret.done(
            function (data)
            {
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
    refresh: function()
    {
        this.forEach(
            function (element, index, list)
            {
                element.refresh();
            }
        );
    }
});
