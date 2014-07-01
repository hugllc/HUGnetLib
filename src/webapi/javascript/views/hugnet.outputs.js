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
var OutputsViewData = Backbone.View.extend({
    model: HUGnet.History,
    tagName: 'div',
    template: '#OutputsViewDataTemplate',
    fields: {},
    classes: {},
    events: {
        'click .refresh': 'refresh',
    },
    initialize: function (options)
    {
        this.model.bind('update', this.render, this);
        this.model.bind('remove', this.remove, this);
    },
    refresh: function (e)
    {
        this.model.refresh();
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
    render: function ()
    {
        var data = this.model.toJSON();
        _.extend(data, HUGnet.viewHelpers);
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        return this;
    }
});

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
HUGnet.OutputsView = Backbone.View.extend({
    template: '#OutputsViewTemplate',
    tagName: 'div',
    pause: 10,
    rows: 0,
    id: undefined,
    dataTable: undefined,
    plot: undefined,
    controlchannelsmodel: undefined,
    controlchannels: undefined,
    units: [],
    events: {
    },
    initialize: function (options)
    {
        this.url = options.url;
        this.data = options.data;
        this.parent = options.parent;
        this.dataTable = new OutputsViewData({
            model: this.model
        });
        var device;
        var i;
        
        this.controlchannelsmodel = new HUGnet.DeviceControlChannels();
        var controlchannels = this.model.get('controlChannels');
        var dev = this.model.get('id');
        _.each(
            controlchannels,
            function (value, key, list)
            {
                controlchannels[key].dev = dev;
            }, 
            this
        );
        this.controlchannelsmodel.reset(controlchannels);
        this.controlchannels = new HUGnet.OutputsControlChannelsView({
            model: this.controlchannelsmodel,
            url: this.url
        });
        //        this.on("update", this.update, this);
    },
    exit: function()
    {
        this.remove();
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
        var data = this.model.toJSON();
        _.extend(data, HUGnet.viewHelpers);
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        this.$("#OutputsViewData").html(this.dataTable.render().el);
        this.$("#OutputsControlChannelsDiv").html(this.controlchannels.render().el);
        return this;
    },
    renderEntry: function (view)
    {
        view.render();
    },
});
