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
HUGnet.DeviceProcess = Backbone.Model.extend({
    defaults:
    {
        dev: null,
        process: null,
        id: null,
        type: "Unknown",
        location: '',
        extra: {},
        extraDesc: {},
        extraText: {},
        extraDefault: {},
        extraValues: {},
        tableEntry: {},
        fullEntry: {},
        otherTables: {},
        lastTable: "None",
        driver: 'SDEFAULT',
        params: {},
    },
    idAttribute: 'process',
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
    settable: function(table)
    {
        var self = this;
        var dev = this.get('dev');
        $.ajax({
            type: 'POST',
            url: this.url(),
            cache: false,
            dataType: 'json',
            data: {
                "task": "deviceprocess",
                "action": "settable",
                "id": parseInt(dev, 10).toString(16)+"."+this.get("process"),
                "data": { id: parseInt(table) }
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
                    self.trigger('savefail', "set table failed on server");
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
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DeviceProcesses = Backbone.Collection.extend({
    urlPart: '/process',
    model: HUGnet.DeviceProcess,
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
        return parseInt(model.get("process"), 10);
    },
});
