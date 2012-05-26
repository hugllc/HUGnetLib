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
HUGnet.DeviceSensor = Backbone.Model.extend({
    defaults:
    {
        dev: null,
        sensor: null,
        id: null,
        type: "Unknown",
        location: 'No Name',
        dataType: 'raw',
        units: 'Unknown',
        decimals: 0,
        driver: 'SDEFAULT',
        params: {},
        url: '/HUGnetLib/index.php'
    },
    idAttribute: 'sensor',
    /**
    * This function initializes the object
    */
    initialize: function(attrib)
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
        var dev = this.get('dev');
        var self = this;
        $.ajax({
            type: 'GET',
            url: this.get('url'),
            cache: false,
            dataType: 'json',
            data:
            {
                "task": "sensor",
                "action": "get",
                "id": parseInt(dev, 10).toString(16),
                "sid": this.get("sensor")
            }
        }).done(
            function (data)
            {
                if ((data !== undefined) && (data !== null) && (typeof data === "object")) {
                    self.set(data);
                    self.trigger('fetchdone');
                    self.trigger('sync');
                } else {
                    self.trigger('fetchfail');
                }
            }
        ).fail(
            function (data)
            {
                self.trigger('fetchfail');
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
        var dev = this.get('dev');
        var data = this.toJSON();
        delete data.url;
        $.ajax({
            type: 'POST',
            url: this.get('url'),
            cache: false,
            dataType: 'json',
            data: {
                "task": "sensor",
                "action": "post",
                "id": parseInt(dev, 10).toString(16),
                "sid": this.get("sensor"),
                "sensor": data
            }
        }).done(
            function (data)
            {
                if (data === "success") {
                    self.trigger('saved');
                    self.fetch();
                } else {
                    self.trigger('savefail', "save failed on server");
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
* @subpackage Tests
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DeviceSensors = Backbone.Collection.extend({
    url: '/HUGnetLib/index.php',
    model: HUGnet.DeviceSensor,
    comparator: function (model)
    {
        return model.get("sensor");
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
    }
});
