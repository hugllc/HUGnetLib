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
HUGnet.Device = Backbone.Model.extend({
    idAttribute: 'id',
    defaults:
    {
        id: null,
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
        localParams: {},
        setparams: {},
        InputTables: 0,
        OutputTables: 0,
        ProcessputTables: 0,
        DaughterBoards: {"": "None"},
        LatePoll: false,
        type: 'unknown',
        input: [],
        output: [],
        process: [],
        Publish: 1,
        actions: '',
        ViewButtonID: '',
        RefreshButtonID: '',
        target: '',
    },
    lock: false,
    inputCache: null,
    outputCache: null,
    processCache: null,
    powerCache: null,
    /**
    * This function initializes the object
    */
    initialize: function(attrib)
    {
        _.bindAll(this, "input", "output", "process", "power");
        if (this.get("DeviceID") === '000000') {
            var id = this.get("id");
            if (id != null) {
                this.set("DeviceID", id.toString(16));
            }
        }
    },
    /**
    * This function initializes the object
    */
    fix: function(attributes)
    {
    },
    /**
    * This returns an input from this device
    */
    input: function(id, fetch)
    {
        if ((this.inputCache == null) || (typeof this.inputCache != 'object')) {
            this.inputCache = new HUGnet.DeviceInputs({
                "baseurl": this.url()
            });
            fetch = true;
        }
        if (fetch) {
            this.inputCache.fetch();
        }
        if (_.isNumber(id)) {
            return this.inputCache.get(id);
        }
        return this.inputCache;
    },
    /**
    * This returns an output from this device
    */
    output: function(id, fetch)
    {
        if ((this.outputCache == null) || (typeof this.outputCache != 'object')) {
            this.outputCache = new HUGnet.DeviceOutputs({
                "baseurl": this.url()
            });
            fetch = true;
        }
        if (fetch) {
            this.outputCache.fetch();
        }
        if (_.isNumber(id)) {
            return this.outputCache.get(id);
        }
        return this.outputCache;
    },
    /**
    * This returns a process from this device
    */
    process: function(id, fetch)
    {
        if ((this.processCache == null) || (typeof this.processCache != 'object')) {
            this.processCache = new HUGnet.DeviceProcesses({
                "baseurl": this.url()
            });
            fetch = true;
        }
        if (fetch) {
            this.processCache.fetch();
        }
        if (_.isNumber(id)) {
            return this.processCache.get(id);
        }
        return this.processCache;
    },
    /**
    * This returns a power from this device
    */
    power: function(id, fetch)
    {
        if ((this.powerCache == null) || (typeof this.powerCache != 'object')) {
            this.powerCache = new HUGnet.DevicePowers({
                "baseurl": this.url()
            });
            fetch = true;
        }
        if (fetch) {
            this.powerCache.fetch();
        }
        if (_.isNumber(id)) {
            return this.powerCache.get(id);
        }
        return this.powerCache;
    },
    /**
    * This returns a power from this device
    */
    history: function(models, options)
    {
        if (!_.isObject(options)) {
            options = {};
        }
        options.baseurl = this.url();
        return new HUGnet.Histories(models, options);
    },
    /**
    * This returns a power from this device
    */
    annotations: function(options)
    {
        if (!_.isObject(options)) {
            options = {};
        }
        options.baseurl = this.url();
        return new HUGnet.Annotations(options);
    },
    /**
    * Sets the data to be sent back to the server
    *
    * @return JSON string
    */
    saveData: function()
    {
        var data = this.toJSON();
        data.params = this.get("setparams");

        return data;
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
    config: function (load)
    {
        var type = "GET";
        if (load) {
            type = "POST";
        }
        var id = this.get('id');
        if (id !== 0) {
            var self = this;
            $.ajax({
                type: type,
                url: this.url() + "/config",
                dataType: 'json',
                cache: false,
                data: {},
            }).done(
                function (data)
                {
                    if (_.isObject(data)) {
                        self.unset('update');
                        self.set(data);
                        self.trigger('configdone');
                        self.trigger('sync', self);
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
                type: 'POST',
                url: this.url()+"/firmware",
                dataType: 'json',
                cache: false,
                data: {
                }
            }).done(
                function (data)
                {
                    if (_.isObject(data)) {
                        self.unset('update');
                        self.set(data);
                        self.config();
                        self.trigger('configdone');
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
    },
   /**
    * Gets infomration about a device.  This is retrieved from the database only.
    *
    * @param id The id of the device to get
    *
    * @return null
    */
    fctsetup: function()
    {
        var id = this.get('id');
        if ((id !== 0) && !this.lock) {
            var myself = this;
            $.ajax({
                type: 'GET',
                url: this.url()+"/fct",  // fctsetup
                cache: false,
                dataType: 'json',
                data:
                {
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
     * Gets infomration about a device.  This is retrieved from the database only.
     *
     * @param id The id of the device to get
     *
     * @return null
     */
    fctapply: function()
    {
        var id = this.get('id');
        if ((id !== 0) && !this.lock) {
            var myself = this;
            $.ajax({
                type: 'POST',
                url: this.url()+"/fct", // fctapply
                cache: false,
                dataType: 'json',
                data:
                {
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
     * Gets infomration about a device.  This is retrieved from the database only.
     *
     * @param id The id of the device to get
     *
     * @return null
     */
    exporturl: function()
    {
        return this.url()+"/export";
    },
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
HUGnet.Devices = Backbone.Collection.extend({
    urlPart: '/device',
    model: HUGnet.Device,
    baseurl: '',
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
    createVirtual: function (type)
    {
        var self = this;
        var ret = $.ajax({
            type: 'POST',
            url: this.url()+"/new",
            dataType: 'json',
            cache: false,
            data: {
                "data": { type: type }
            }
        }).done(
            function (data)
            {
                if (_.isObject(data)) {
                    self.trigger('created');
                    self.model.add(data);
                } else {
                    self.trigger('newfail');
                }
            }
        ).fail(
            function (data)
            {
                self.trigger('newfail');
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
    importurl: function()
    {
        return this.url()+"/import";
    },
});
