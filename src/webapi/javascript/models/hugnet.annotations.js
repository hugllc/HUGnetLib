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
HUGnet.Annotation = Backbone.Model.extend({
    idAttribute: 'id',
    defaults:
    {
        id: null,
        date: 0,
        test: 0,
        type: "Unknown",
        testdate: 0,
        text: "",
        author: ""
    },
    /**
    * This function initializes the object
    */
    initialize: function(attrib)
    {
    },
    /**
    * This function initializes the object
    */
    fix: function(attributes)
    {
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
HUGnet.Annotations = Backbone.Collection.extend({
    urlPart: '/annotation',
    model: HUGnet.Annotation,
    baseurl: '',
    refresh: 300,
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
            this.update();
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
    fetch: function (test, since, until)
    {
        var self = this;

        var since = Math.round(since / 1000);
        var until = Math.round(until / 1000);

        var xhr = new XMLHttpRequest();
        xhr.open('GET', encodeURI(this.url()+'?until='+until+'&since='+since));
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                if ((data !== null) && _.isObject(data)) {
                    self.each(
                        function (model, collection, options)
                        {
                            var date = model.get("testdate");
                            if ((date > until) || (date < since)) {
                                model.collection.remove(model);
                            }
                        },
                        self
                    );
                    self.add(data);
                } else {
                    self.trigger('fetchfail');
                }
            } else {
            }
        };
        xhr.send();
    },
    update: function()
    {
        this.forEach(
            function (element, index, list)
            {
                element.refresh();
            }
        );
    }
});
