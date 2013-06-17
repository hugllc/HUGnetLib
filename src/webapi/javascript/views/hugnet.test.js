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
var TestEntryView = Backbone.View.extend({
    model: HUGnet.Device,
    tagName: 'tr',
    template: '#TestEntryTemplate',
    parent: null,
    events: {
        'click .view': 'view',
        'click .export': 'export'
    },
    initialize: function (options)
    {
        this.model.bind('change', this.render, this);
        this.model.bind('remove', this.remove, this);
        this.parent = options.parent;
    },
    run: function (e)
    {
        this.parent.trigger("run", this.model);
    },
    view: function (e)
    {
        this.parent.trigger("view", this.model);
    },
    export: function (e)
    {
        this.parent.trigger("export", this.model);
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
        this.$el.trigger('update');
        return this;
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
HUGnet.TestsView = HUGnet.DeviceListView.extend({
    template: "#TestListTemplate",
    templatebase: 'TestList',
    url: '/HUGnetLib/HUGnetLibAPI.php',
    filter: {type: "test"},
    views: {},
    sorted: false,
    sorting: [[1,0]],
    viewed: 0,
    readonly: false,
    events: {
        'click .run': 'run',
        'click .stop': 'run'
    },
    initialize: function (options)
    {
        this.$('.run').hide();
        this.$('.stop').hide();
        this.$('.new').hide();
        if (options) {
            if (options.url) {
                this.url = options.url;
            }
            if (options.readonly) {
                this.readonly = options.readonly;
            }
        }
        this.model.each(this.insert, this);
        this.model.on('add', this.insert, this);
        this.model.on('sync', this.insert, this);
        this.model.on('savefail', this.saveFail, this);
        if (!this.readonly) {
            this.run('status');
        }
    },
    running: function ()
    {
        this.$('.run').hide();
        this.$('.stop').show();
    },
    paused: function ()
    {
        this.$('.run').show();
        this.$('.stop').hide();
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
    saveFail: function (msg)
    {
        //alert("Save Failed: " + msg);
    }
});
