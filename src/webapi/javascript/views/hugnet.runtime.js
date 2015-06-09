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
 * @subpackage DataPoints
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
* @subpackage DataPoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.RuntimeView = Backbone.View.extend({
    template: '#RuntimeConfigTemplate',
    url: '/HUGnetLib/HUGnetLibAPI.php',
    tagName: 'div',
    datacollectors: null,
    events: {
        'click .runtests': 'run',
        'click .stoptests': 'run'
    },
    initialize: function (options)
    {
        _.bindAll(this, "paused", "running", "_initstatus");
        this.$('.runtests').hide();
        this.$('.stoptests').hide();
        if (options) {
            options.url && (this.url = options.url);
            if (options.datacollectors) {
                this.datacollectors = options.datacollectors;
                this.datacollectors.on("testrunning", this.running, this);
                this.datacollectors.on("testpaused", this.paused, this);
            }
        }
        this._initstatus();
    },
    /**
    * Gets infomration about a device.  This is retrieved directly from the device
    *
    * This function is for use of the device list
    *
    * @return null
    */
    render: function ()
    {
        var data = {}; //this.model.toJSON();
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        return this;
    },
    run: function ()
    {
        if (typeof this.datacollectors == "object") {
            this.datacollectors.run("run");
        }
    },
    _initstatus: function ()
    {
        if (typeof this.datacollectors == "object") {
            if (this.status()) {
                return;
            }
        }
        setTimeout(this._initstatus, 1000);
    },
    status: function ()
    {
        if (typeof this.datacollectors == "object") {
            return this.datacollectors.run("status");
        }
        return false;
    },
    running: function ()
    {
        this.$('.runtests').hide();
        this.$('.stoptests').show();
    },
    paused: function ()
    {
        this.$('.runtests').show();
        this.$('.stoptests').hide();
    },
});
