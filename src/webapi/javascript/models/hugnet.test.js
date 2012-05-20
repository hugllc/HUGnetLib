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
var Test = Backbone.Model.extend({
    defaults: {
        id: null,
        name: 'No Name',
        created: 0,
        modified: 0,
        fields: {},
        notes: '',
        url: '/HUGnetLib/index.php',
    },
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
        var id = this.get('id');
        if (id !== 0) {
            var myself = this;
            $.ajax({
                type: 'GET',
                url: this.get('url'),
                dataType: 'json',
                cache: false,
                data:
                {
                    "task": "test",
                    "action": "get",
                    "id": id
                },
            }).done(
                function (data)
                {
                    if (data && (data !== null) && _.isObject(data)) {
                        myself.set(data);
                    }
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
        if (id !== null) {
            var self = this;
            $.ajax({
                type: 'POST',
                url: this.get('url'),
                dataType: 'json',
                cache: false,
                data:
                {
                    "task": "test",
                    "action": "post",
                    "id": id,
                    "test": self.toJSON(),
                },
            }).done(
                function (data)
                {
                    if (data == "success") {
                        self.fetch();
                        self.trigger('saved', this);
                    } else {
                        self.trigger('savefail', 'Save failed on server');
                    }
                }
            ).fail(
                function ()
                {
                    self.trigger('savefail', 'Could not contact server');
                }
            );
        } else {
            this.trigger('savefail', 'id can not be null');
        }
    },
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
window.Tests = Backbone.Collection.extend({
    url: '/HUGnetLib/index.php',
    model: Test,
    comparator: function (device)
    {
        return device.get("id");
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
                "task": "test", "action": "getall"
            },
        }).done(
            function (data)
            {
                if (data && (data !== null) && _.isObject(data)) {
                    self.add(data);
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
    new: function ()
    {
        var self = this;
        $.ajax({
            type: 'GET',
            url: this.url,
            dataType: 'json',
            cache: false,
            data: {
                "task": "test", "action": "new"
            },
        }).done(
            function (data)
            {
                if (data && (data !== null) && _.isObject(data)) {
                    self.add(data);
                } else {
                    self.trigger('savefail', 'Save failed on server');
                }
            }
        ).fail(
            function ()
            {
                self.trigger('savefail', 'Could not contact server');
            }
        );
    },
});
