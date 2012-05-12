/**
 * Javascript for devices
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

var HUGnetDevice = function(id, target, url, template) {
    this.id       = id;
    this.defTemp  = '<td>{{actions}}</td><td>{{DeviceName}}</td><td>{{DeviceID}}</td><td>{{id}}</td><td>{{HWPartNum}}</td><td>{{FWPartNum}} {{FWVersion}}</td>';
    this.target   = (typeof target   !== 'undefined') ? target   : '#dev' + this.id;
    this.template = (typeof template !== 'undefined') ? template : this.defTemp;
    this.devData  = {};
    this.url      = (typeof url !== 'undefined') ? url : "/hugnetapi.php";

    this.get();
}

HUGnetDevice.prototype = {
    /**
     * This function just returns the data
     */
    data: function() {
        return this.devData;
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
    get: function()
    {
        if (this.id !== 0) {
            var self = this;
            $.ajax({
                type: 'GET',
                url: this.url,
                dataType: 'json',
                success: function (data)
                {
                    self.devData = data;
                    self.update();
                },
                data: {
                    "task": "device", "action": "get", "id": this.id.toString(16)
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
        if (this.id !== 0) {
            var self = this;
            $.ajax({
                type: 'GET',
                url: this.url,
                dataType: 'json',
                success: function (data)
                {
                    self.devData = data;
                    self.update();
                },
                data: {
                    "task": "device", "action": "config", "id": this.id.toString(16)
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
    render: function()
    {
        $(this.target).html(Mustache.render(this.template, this.devData));
    }
}
