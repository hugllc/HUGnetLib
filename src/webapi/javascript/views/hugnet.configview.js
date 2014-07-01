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
var DeviceConfigImageView = Backbone.View.extend({
    tagName: 'div',
    url: null,
    urlbase: '/HUGnetLib/images/config/',
    initialize: function (options)
    {
        var img = this.model.get("configImage");
        if (typeof img == "string") {
            this.url = this.urlbase + img;
        }
    },
    render: function ()
    {
        if (typeof this.url == "string") {
            var myself = this;
            this.$el.load(
                this.url,
                function ()
                {
                    myself.renderChannels(myself.model.get("dataChannels"), "InputKey");
                    myself.renderChannels(myself.model.get("controlChannels"), "OutputKey");
                }
            );
        } else {
            this.$el.html("No Image Available");
        }
        return this;
    },
    renderChannels: function (channels, colorKey)
    {
        var color = $('text#'+colorKey).css('fill');
        _.each(
            channels,
            function (chan, key)
            {
                if (typeof chan.port == "string") {
                    var ports = chan.port.split(",");
                    var label = chan.label;
                    _.each(
                        ports,
                        function (port, key)
                        {
                            var pval = port.split(" ");
                            var id = $.trim(pval[0]);
                            // This escapes these characters correctly for the selector
                            id = id.replace(/([ #;?&,.+*~\':"!^$[\]()=>|\/@])/g,'\\$1');
                            var el = $('text#'+id+' tspan');
                            var text = label;
                            if (pval[1] != undefined) {
                                text = text + " " + pval[1];
                            }
                            el.text(text);
                            el.css('fill', color);
                            if (typeof index == "number") {
                                index++;
                            }
                        }
                    );
                }
            },
            this
        );
    }
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
var DeviceConfigView = Backbone.View.extend({
    template: '#DeviceConfigViewTemplate',
    tTemplate: '#DeviceConfigViewTitleTemplate',
    tagName: 'div',
    devImage: null,
    events: {
        'click .close': 'close',
    },
    initialize: function (options)
    {
        this.model.lock = true;
        this.datachannelsmodel = new HUGnet.DeviceDataChannels();
        var datachannels = this.model.get('dataChannels');
        this.datachannelsmodel.reset(datachannels);
        this.datachannels = new HUGnet.DeviceDataChannelsView({
            model: this.datachannelsmodel,
            template: "#ConfigViewDataChannelListTemplate",
            rowTemplate: "#ConfigViewDataChannelEntryTemplate"
        });
        this.controlchannelsmodel = new HUGnet.DeviceControlChannels();
        var controlchannels = this.model.get('controlChannels');
        this.controlchannelsmodel.reset(controlchannels);
        this.controlchannels = new HUGnet.DeviceControlChannelsView({
            model: this.controlchannelsmodel,
            template: "#ConfigViewControlChannelListTemplate",
            rowTemplate: "#ConfigViewControlChannelEntryTemplate"
        });
        this.model.on(
            'change',
            this.channelRegen,
            this
        );
        this.devImage = new DeviceConfigImageView({
            model: this.model
        });
    },
    channelRegen: function (model, collection, view)
    {
        var datachannels = this.model.get('dataChannels');
        this.datachannelsmodel.reset(datachannels);
        var controlchannels = this.model.get('controlChannels');
        this.controlchannelsmodel.reset(controlchannels);
        this.render();
    },
    close: function ()
    {
        this.model.off('change', this.channelRegen, this);
        this.devImage.remove();
        this.remove();
    },
    setTitle: function (extra)
    {
        if (this.$el.is(':data(dialog)')) {
            this.$el.dialog( "option", "title", this.title() + extra );
        }
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
        data.dataChannels = '<div id="DeviceDataChannelsDiv"></div>';
        data.controlChannels = '<div id="DeviceControlChannelsDiv"></div>';
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        this.$("#DeviceDataChannelsDiv").html(this.datachannels.render().el);
        this.$("#DeviceControlChannelsDiv").html(this.controlchannels.render().el);
        this.$("#image").html(this.devImage.render().el);
        return this;
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
    title: function ()
    {
        return _.template(
            $(this.tTemplate).html(),
            this.model.toJSON()
        );
    },
});

