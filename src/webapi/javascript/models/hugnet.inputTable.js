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
HUGnet.InputTable = Backbone.Model.extend({
    defaults:
    {
        id: null,
        name: null,
        arch: null,
        desc: null,
        params: {}
    },
    idAttribute: 'id',
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
                    "task": "inputTable",
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
                    "task": "inputTable",
                    "action": "put",
                    "id": id.toString(16),
                    "data": self.toJSON()
                }
            }).done(
                function (data)
                {
                    if ((data !== undefined) && (data !== null) && (typeof data === "object")) {
                        self.trigger('saved');
                        self.set(data);
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
HUGnet.InputTables = Backbone.Collection.extend({
    url: '/HUGnetLib/HUGnetLibAPI.php',
    model: HUGnet.InputTable,
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
                "task": "inputTable", "action": "list"
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
