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
 * @author     Simon Goble <sgoble@hugllc.com>
 * @copyright  2015 Hunt Utilities Group, LLC
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
* @author     Simon Goble <sgoble@hugllc.com>
* @copyright  2015 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.DevicePower = Backbone.Model.extend({
    defaults:
    {
        dev: null,
        power: null,
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
        data: {},
    },
    statusChan: null,
    currentChan: null,
    voltageChan: null,
    idAttribute: 'power',
    channels: {},
    /**
    * This function initializes the object
    */
    initialize: function(attrib)
    {
        _.bindAll(this, "settable", "connected", "current");
        this.channels = this.collection.device.get("dataChannels");
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
        var xhr = new XMLHttpRequest();

        xhr.open('PUT', this.url()+'/settable');
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onload = function() {
            if ((xhr.status === 200) || (xhr.status === 202)){
                var data = JSON.parse(xhr.responseText);
                if (_.isObject(data)) {
                    self.trigger('saved');
                    self.set(data);
                    self.trigger('fetchdone');
                    self.trigger('sync');
                } else {
                    self.trigger('savefail');
                }
            } else {
                self.trigger('savefail');
            }
        };
        xhr.send(JSON.stringify(parseInt(table)));
    },
    /**
     * This function returns whether this power port is connected or not
     *
     * @return boolean true if connected, false otherwise
     */
    updated: function()
    {
        var data = this.get("data");
        return ((typeof data == "object") && data.Date);
    },
    /**
     * This function returns whether this power port is connected or not
     *
     * @return boolean true if connected, false otherwise
     */
    connected: function()
    {
        var data = this.get("data");
        if (this.updated()) {
            if (data.online) {
                return true;
            }
            return false;
        }
        return null;
    },
    /**
     * This function returns whether this power port is connected or not
     *
     * @return boolean true if connected, false otherwise
     */
    current: function()
    {
        var data = this.get("data");
        if ((typeof data == "object") && data.Date) {
            return parseInt(data.current, 10);
        }
        return null;
    },
    /**
     * This function returns whether this power port is connected or not
     *
     * @return boolean true if connected, false otherwise
     */
    voltage: function()
    {
        var data = this.get("data");
        if ((typeof data == "object") && data.Date) {
            return parseInt(data.voltage, 10);
        }
        return null;
    },
    /**
     * This function returns whether this power port is connected or not
     *
     * @return boolean true if connected, false otherwise
     */
    capacity: function()
    {
        var data = this.get("data");
        if ((typeof data == "object") && data.Date) {
            return parseInt(data.capacity, 10);
        }
        return null;
    },
    /**
     * This function returns whether this power port is connected or not
     *
     * @return boolean true if connected, false otherwise
     */
    charge: function()
    {
        var data = this.get("data");
        if ((typeof data == "object") && data.Date) {
            return parseInt(data.charge, 10);
        }
        return null;
    },
    /**
     * This function returns whether this power port is connected or not
     *
     * @return boolean true if connected, false otherwise
     */
    status: function()
    {
        var data = this.get("data");
        if ((typeof data == "object") && data.Date) {
            return parseInt(data.status, 10);
        }
        return null;
    },
    /**
     * This function returns a text description of the status
     */
    statustxt: function()
    {
       var returnvar = "Unknown";

       switch(this.status()){
          case 0 :
             returnvar = "Unknown";
             break;
          case 1 :
             returnvar = "No driver, supplying current to the bus anyway";
             break;
          case 2 :
             returnvar = "No driver";
             break;
          case 3 :
             returnvar = "Online";
             break;
          case 4 :
             returnvar = "Offline";
             break;
          case 5 :
             returnvar = "Empty";
             break;
          case 6 :
             returnvar = "Error";
             break;
          default :
             returnvar = "Unknown";
             break;
       }

       return returnvar;
    },
    /**
     * This function returns whether this power port is connected or not
     *
     * @return boolean true if connected, false otherwise
     */
    batstatus: function()
    {
        var data = this.get("data");
        if ((typeof data == "object") && data.Date) {
            return parseInt(data.batstatus, 10);
        }
        return null;
    },
    /**
     * This function returns a text description of the bat status
     */
    batstatustxt: function()
    {
       var returnvar = "Unknown";

       switch(this.batstatus()){
          case 0 :
             returnvar = "No Battery";
             break;
          case 1 :
             returnvar = "Offline";
             break;
          case 2 :
             returnvar = "Float Charge";
             break;
          case 3 :
             returnvar = "Bulk Charge";
             break;
          case 4 :
             returnvar = "Unknown";
             break;
          case 5 :
             returnvar = "Error";
             break;
          case 6 :
             returnvar = "Discharging";
             break;
          default :
             returnvar = "Unknown";
             break;
       }

       return returnvar;
    },
    /**
     * This function returns whether this power port is connected or not
     *
     * @return boolean true if connected, false otherwise
     */
    error: function()
    {
        var data = this.get("data");
        if ((typeof data == "object") && data.Date) {
            return parseInt(data.error, 10);
        }
        return null;
    },
    /**
     * This function returns a text description of the error status on the port
    */ 
    errortxt: function()
    {
       var returnvar = "Unknown";

       switch(this.error()){
          case 0 :
             returnvar = "No Error";
             break;
          case 1 :
             returnvar = "Overcurrent";
             break;
          case 2 :
             returnvar = "MCU Failure";
             break;
          default :
             returnvar = "Unknown";
             break;
       }

       return returnvar;
    }, 
    /**
     * This function returns whether this power port is connected or not
     *
     * @return boolean true if connected, false otherwise
     */
    currentPerc: function()
    {
        var current = this.current();
        return current/31000;
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
HUGnet.DevicePowers = Backbone.Collection.extend({
    urlPart: '/power',
    model: HUGnet.DevicePower,
    device: null,
    initialize: function (options)
    {
        if (options) {
            if (options.baseurl) this.baseurl = options.baseurl;
            if (options.device) this.device = options.device;
        }
    },
    url: function ()
    {
        return this.baseurl + this.urlPart;
    },
    comparator: function (model)
    {
        return parseInt(model.get("power"), 10);
    },
});
