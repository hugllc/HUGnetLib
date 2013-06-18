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
 * @subpackage DataPoints
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
* @subpackage DataPoints
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.RuntimeView = Backbone.View.extend({
    template: '#RuntimeConfigTemplate',
    url: '/HUGnetLib/HUGnetLibAPI.php',
    tagName: 'div',
    events: {
        'click .runtests': 'run',
        'click .stoptests': 'run'
    },
    initialize: function (options)
    {
        this.$('.runtests').hide();
        this.$('.stoptests').hide();
        if (options) {
            options.url && (this.url = options.url);
        }
        this.run('status');
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
    run: function (action)
    {
        var self = this;
        if (action !== "status") {
            action = "run";
        }
        var ret = $.ajax({
            type: 'GET',
            url: this.url,
            dataType: 'json',
            cache: false,
            data:
            {
                "task": "datacollector",
                "action": action,
            }
        }).done(
            function (data)
            {
                if (data == 1) {
                    self.running();
                    self.trigger('testrunning');
                } else {
                    self.paused();
                    self.trigger('testpaused');
                }
            }
        ).fail(
            function ()
            {
                //self.statusFail();
                self.trigger('statusfail');
            }
        );
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