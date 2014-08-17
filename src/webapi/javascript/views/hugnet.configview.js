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
                    if (chan.unitType) {
                        label = label+" ("+chan.unitType+")";
                    }
                    _.each(
                        ports,
                        function (port, key)
                        {
                            var pval = port.split(" ");
                            var id = $.trim(pval[0]);
                            // Inkscape replaces a '+' with '_', so we will, too.
                            id = id.replace(/\+/g, '_');
                            // This escapes these characters correctly for the selector
                            id = id.replace(/([ #;?&,.*~\':"!^$[\]()=>|\/@])/g,'\\$1');
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
    url: '/HUGnetLib/HUGnetLibAPI.php',
    template: '#DeviceConfigViewTemplate',
    tTemplate: '#DeviceConfigViewTitleTemplate',
    tagName: 'div',
    set: false,
    devImage: null,
    events: {
        'click .close': 'close',
        'click [name="NewFunction"]': 'newfunction',
        'click [name="Save"]': 'save',
        'click [name="Apply"]': 'apply'
    },
    initialize: function (options)
    {
        if (options) {
            if (options.set) this.set = options.set;
            if (options.url) this.url = options.url;
        }
        var set = "";
        if (this.set) {
            set = "Set";
            this.template = '#DeviceConfigSetViewTemplate';
            this.tTemplate = '#DeviceConfigSetViewTitleTemplate';
            this.modelset = new HUGnet.Device(this.model.toJSON());
            this.modelset.urlRoot = this.url;
            // This gets the latest setup
            this.modelset.fctsetup();
        } else {
            this.modelset = this.model;
        }
        this.model.lock = true;
        this.datachannelsmodel = new HUGnet.DeviceDataChannels();
        var datachannels = this.modelset.get('dataChannels');
        this.datachannelsmodel.reset(datachannels);
        this.datachannels = new HUGnet.DeviceDataChannelsView({
            model: this.datachannelsmodel,
            template: "#Config"+set+"ViewDataChannelListTemplate",
            rowTemplate: "#Config"+set+"ViewDataChannelEntryTemplate"
        });
        this.controlchannelsmodel = new HUGnet.DeviceControlChannels();
        var controlchannels = this.modelset.get('controlChannels');
        this.controlchannelsmodel.reset(controlchannels);
        this.controlchannels = new HUGnet.DeviceControlChannelsView({
            model: this.controlchannelsmodel,
            template: "#Config"+set+"ViewControlChannelListTemplate",
            rowTemplate: "#Config"+set+"ViewControlChannelEntryTemplate"
        });
        this.functionsmodel = new HUGnet.DeviceFunctions([], { 
            devid: this.modelset.get("id") 
        });
        this.functionsmodel.fetch();
        this.functions = new HUGnet.DeviceFunctionsView({
            model: this.functionsmodel,
            template: "#Config"+set+"ViewFunctionListTemplate",
            rowTemplate: "#Config"+set+"ViewFunctionEntryTemplate"
        });
        this.modelset.on(
            'change',
            this.channelRegen,
            this
        );
        this.functionsmodel.on(
            'change',
            this.render,
            this
        );
        this.devImage = new DeviceConfigImageView({
            model: this.modelset
        });
    },
    channelRegen: function (model, collection, view)
    {
        var datachannels = this.modelset.get('dataChannels');
        this.datachannelsmodel.reset(datachannels);
        var controlchannels = this.modelset.get('controlChannels');
        this.controlchannelsmodel.reset(controlchannels);
        this.render();
    },
    /**
    * This function initializes the object
    */
    newfunction: function()
    {
        this.functionsmodel.create();
    },
    /**
    * This function initializes the object
    */
    save: function()
    {
        this.apply();
        this.close();
    },
    /**
    * This function initializes the object
    */
    apply: function()
    {
        var params = {
            fcts: this.functionsmodel.toJSON()
        };
        var self = this;
        this.functions.$('table:first > tbody > tr').each(function(ind, element) {
            // This checks that we only include this one once.
            var id = parseInt($(this).find('[name="id"]').val(), 10);
            if (id != undefined) {
                if ($(this).find('[name="delete"]').prop("checked")) {
                    self.functionsmodel.remove(id);
                }
                
                var row   = this;
                var model = self.functionsmodel.get(id);
                if (model != undefined) {
                    _.each(["name", "driver"],
                        function(sel, i) {
                            model.set(sel, $(row).find('[name="'+sel+'"]').val());
                        }
                    );
                }
                var extra = [];
                $(row).find('[name^="extra"]').each(function(ind, element) {
                    extra[ind] = $(this).val();
                });
                model.set("extra", extra);
            }
        });
        this.functionsmodel.save();
        this.modelset.fctsetup();
    },
    close: function ()
    {
        this.modelset.off('change', this.channelRegen, this);
        this.model.lock = false;
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
        var data = this.modelset.toJSON();
        _.extend(data, HUGnet.viewHelpers);
        data.dataChannels = '<div id="DeviceDataChannelsDiv"></div>';
        data.controlChannels = '<div id="DeviceControlChannelsDiv"></div>';
        data.functions = '<div id="DeviceFunctionsDiv"></div>';
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        this.$("#DeviceDataChannelsDiv").html(this.datachannels.render().el);
        this.$("#DeviceControlChannelsDiv").html(this.controlchannels.render().el);
        this.$("#DeviceFunctionsDiv").html(this.functions.render().el);
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
            this.modelset.toJSON()
        );
    },
});

