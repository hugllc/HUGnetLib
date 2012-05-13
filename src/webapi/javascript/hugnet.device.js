/**
 * hugnet.device.js
 *
 * <pre>
 * HUGnetLab is a user interface for the HUGnet
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
 * @package    HUGnetLab
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.0.1
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLab
 */
/*
var HUGnetDevice = function(id, target, url, template) {
    this.id       = id;
    this.defTemp  = '<td>{{actions}}</td><td>{{DeviceName}}</td><td>{{DeviceID}}</td><td>{{id}}</td><td>{{HWPartNum}}</td><td>{{FWPartNum}} {{FWVersion}}</td>';
    this.target   = (typeof target   !== 'undefined') ? target   : '#dev' + this.id;
    this.template = (typeof template !== 'undefined') ? template : this.defTemp;
    this.devData  = {};
    this.url      = (typeof url !== 'undefined') ? url : "/HUGnetLib/index.php";

    this.get();
}
HUGnetDevice.prototype = {
    */
var HUGnetDevice = Backbone.Model.extend({
    defaults: {
        id: 0,
        DeviceID: '000000',
        DeviceName: 'Hello',
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
        sensors: {},
        params: {},
        actions: '',
        template: '#deviceRow',
        target: '',
        url: '/HUGnetLib/index.php',
    },
    /**
     * This function initializes the object
     */
    initialize: function()
    {
        this.bind(
            "change",
            this.update,
            this
        );
        this.fetch();
    },
    /**
     * Updates the particular elements
     */
    update: function() {
        this.render();
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
            var self = this;
            $.ajax({
                type: 'GET',
                url: this.get('url'),
                dataType: 'json',
                success: function (data)
                {
                    self.set(data);
                },
                data:
                {
                    "task": "device", "action": "get", "id": id.toString(16)
                },
            });
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
                url: this.get('url'),
                dataType: 'json',
                success: function (data)
                {
                    self.set(data);
                },
                data:
                {
                    "task": "device",
                    "action": "post",
                    "id": id.toString(16),
                    "device": self.toJSON(),
                },
            });
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
                url: this.get('url'),
                dataType: 'json',
                success: function (data)
                {
                    self.set(data);
                },
                data: {
                    "task": "device", "action": "config", "id": id.toString(16)
                },
            });
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
    render: function(template)
    {
        var temp = (typeof template !== 'undefined') ? template : this.get('template');
        var self = this;
        this.set({actions: '<button onClick="self.config();">Refresh</button>'});
        $(this.get('target')).html(
            Mustache.render(
                $(temp).html(),
                this.toJSON()
            )
        );
    }
});
