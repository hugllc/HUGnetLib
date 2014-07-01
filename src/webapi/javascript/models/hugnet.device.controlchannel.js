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
 * @subpackage Tests
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
* @subpackage Tests
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DeviceControlChannel = Backbone.Model.extend({
    defaults:
    {
        channel: null,
        output: null,
        dev: null,
        type: "Unknown",
        label: 'No Name',
        value: null,
    },
    idAttribute: 'channel',
    getValue: function()
    {
        var dev = this.get('dev');
        var self = this;
        $.ajax({
            type: 'GET',
            url: this.url(),
            cache: false,
            dataType: 'json',
            data:
            {
                "task": "devicecontrolchan",
                "action": "get",
                "id": parseInt(dev, 10).toString(16)+"."+this.get("channel")
            }
        }).done(
            function (data)
            {
                if ((data !== undefined) && (data !== null)) {
                    self.set('value', data);
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
    setValue: function(value)
    {
        var dev = this.get('dev');
        var self = this;
        $.ajax({
            type: 'GET',
            url: this.url(),
            cache: false,
            dataType: 'json',
            data:
            {
                "task": "devicecontrolchan",
                "action": "set",
                "id": parseInt(dev, 10).toString(16)+"."+this.get("channel"),
                "data": parseInt(value, 10)
            }
        }).done(
            function (data)
            {
                if ((data !== undefined) && (data !== null)) {
                    self.set('value', data);
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
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage Tests
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DeviceControlChannels = Backbone.Collection.extend({
    url: '/HUGnetLib/HUGnetLibAPI.php',
    model: HUGnet.DeviceControlChannel,
    comparator: function (model)
    {
        return parseInt(model.get("channel"), 10);
    },
});
