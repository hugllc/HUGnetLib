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
HUGnet.Device = Backbone.Model.extend({
    idAttribute: 'id',
    defaults:
    {
        id: 0,
        DeviceID: '000000',
        DeviceName: '',
        HWPartNum: '',
        FWPartNum: '',
        FWVersion: '',
        RawSetup: '',
        Active: 0,
        GatewayKey: 0,
        ControllerKey: 0,
        ControllerIndex: 0,
        DeviceLocation: '',
        DeviceJob: '',
        Driver: '',
        PollInterval: 0,
        ActiveSensors: 0,
        DeviceGroup: 'FFFFFF',
        inputs: {},
        outputs: {},
        processes: {},
        dataChannels: {},
        params: {},
        InputTables: 0,
        OutputTables: 0,
        ProcessputTables: 0,

        actions: '',
        ViewButtonID: '',
        RefreshButtonID: '',
        target: '',
    },
    /**
    * This function initializes the object
    */
    initialize: function(attrib)
    {
        if (this.get("DeviceID") === '000000') {
            this.set("DeviceID", this.get("id").toString(16));
        }
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
        var id = this.get('id');
        if (id !== 0) {
            var myself = this;
            $.ajax({
                type: 'GET',
                url: this.url(),
                cache: false,
                dataType: 'json',
                data:
                {
                    "task": "device",
                    "action": "get",
                    "id": id.toString(16)
                }
            }).done(
                function (data)
                {
                    myself.set(data);
                }
            );
        }
    },
    /**
    * Sets the data to be sent back to the server
    *
    * @return JSON string
    */
    saveData: function()
    {
        var data = this.toJSON();
        data.params = [];
        return data;
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
        if (id !== 0) {
            var self = this;
            $.ajax({
                type: 'POST',
                url: this.url(),
                dataType: 'json',
                cache: false,
                data:
                {
                    "task": "device",
                    "action": "get",
                    "id": id.toString(16),
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
            $.ajax({
                type: 'POST',
                url: this.url(),
                dataType: 'json',
                cache: false,
                data:
                {
                    "task": "device",
                    "action": "put",
                    "id": id.toString(16),
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
    config: function ()
    {
        var id = this.get('id');
        if (id !== 0) {
            var self = this;
            $.ajax({
                type: 'GET',
                url: this.url(),
                dataType: 'json',
                cache: false,
                data: {
                    "task": "device",
                    "action": "config",
                    "id": id.toString(16)
                }
            }).done(
                function (data)
                {
                    if (_.isObject(data)) {
                        self.unset('update');
                        self.set(data);
                        self.trigger('configdone');
                        self.trigger('sync');
                    } else {
                        self.trigger('configfail');
                    }
                }
            ).fail(
                function (data)
                {
                    self.trigger('configfail');
                }
            );
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
    loadconfig: function ()
    {
        var id = this.get('id');
        if (id !== 0) {
            var self = this;
            $.ajax({
                type: 'GET',
                url: this.url(),
                dataType: 'json',
                cache: false,
                data: {
                    "task": "device",
                    "action": "loadconfig",
                    "id": id.toString(16)
                }
            }).done(
                function (data)
                {
                    if (_.isObject(data)) {
                        self.unset('update');
                        self.set(data);
                        self.config();
                    } else {
                        self.trigger('configfail');
                    }
                }
            ).fail(
                function (data)
                {
                    self.trigger('configfail');
                }
            );
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
    loadfirmware: function ()
    {
        var id = this.get('id');
        if (id !== 0) {
            var self = this;
            $.ajax({
                type: 'GET',
                url: this.url(),
                dataType: 'json',
                cache: false,
                data: {
                    "task": "device",
                    "action": "loadfirmware",
                    "id": id.toString(16)
                }
            }).done(
                function (data)
                {
                    if (_.isObject(data)) {
                        self.unset('update');
                        self.set(data);
                        self.config();
                    } else {
                        self.trigger('configfail');
                    }
                }
            ).fail(
                function ()
                {
                    self.trigger('configfail');
                }
            );
        }
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
HUGnet.Devices = Backbone.Collection.extend({
    url: '/HUGnetLib/HUGnetLibAPI.php',
    model: HUGnet.Device,
    initialize: function (options)
    {
        if (options) {
            if (options.url) this.url = options.url;
        }
    },
    comparator: function (model)
    {
        return model.get("id");
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
                "task": "device", "action": "list"
            }
        });
        ret.done(
            function (data)
            {
                self.add(data);
            }
        );
    }
});
