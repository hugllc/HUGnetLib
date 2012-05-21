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
HUGnet.DataPointsView = Backbone.View.extend({
    template: { run: "#DataPointListRunTemplate", view: "#DataPointListViewTemplate" },
    rowClass: [ 'odd', 'even' ],
    tagName: 'div',
    pause: 1,
    rows: 0,
    autorefresh: 0,
    mode: 'run',
    type: 'test',
    parent: undefined,
    id: undefined,
    table: undefined,
    data: {},
    device: {},
    header: {},
    fields: {},
    classes: {},
    events: {
        'click .startPoll': 'startPoll',
        'click .stopPoll': 'stopPoll',
        'click .autorefresh': 'setRefresh',
    },
    initialize: function (options)
    {
        this.data = options.data;
        this.setMode(options.mode);
        var device;
        var i;
        this.header = {};
        this.fields = {};
        this.device = {};
        this.classes = {};
        for (i in this.data) {
            device = parseInt(this.data[i].device, 16);
            if (device > 0) {
                this.device[device] = device;
            }
            this.header[i] = this.data[i].name;
            this.fields[i] = this.getField(i, this.data[i].field);
            this.classes[i] = this.data[i].class;
        }
        this.pause = (options.pause !== undefined) ? options.pause - 0 : this.pause;
        this.type = (options.type !== undefined) ? options.type : this.type;
        this.parent = options.parent;
        this.id = options.id;
        this.model = new Histories(
            null,
            {
                device: this.device,
                id: this.id,
                mode: this.mode,
                type: this.type,
            }
        );
        this.table = new Table({
            model: this.model,
            header: this.header,
            fields: this.fields,
            classes: this.classes,
        });
    },
    setRefresh: function ()
    {
        if (this.$('.autorefresh').prop("checked")) {
            this.autorefresh = this.$('.autorefresh').val() - 0;
        } else {
            this.autorefresh = 0;
        }
        console.log(this.autorefresh);
        this.model.setRefresh(this.autorefresh);
    },
    setMode: function (mode)
    {
        if (mode == "view") {
            this.mode = "view";
        } else {
            this.mode = "run";
        }
    },
    getField: function (index, field)
    {
        if (parseInt(field) == field) {
            return "Data" + index;
        }
        return field;
    },
    startPoll: function()
    {
        if (this.mode === 'run') {
            this.$('.stopPoll').show();
            this.$('.startPoll').hide();
            this.$('.exit').hide();
            this.model.pause = this.pause;
            this.model.startPoll();
        }
    },
    stopPoll: function()
    {
        if (this.mode === 'run') {
            this.$('.stopPoll').hide();
            this.$('.startPoll').show();
            this.$('.exit').show();
            this.model.stopPoll();
        }
    },
    exit: function()
    {
        this.reset();
        this.stopPoll();
        this.model.mode = 'shutdown';
        this.remove();
    },
    reset: function()
    {
        this.model.clear();
        this.rows = 0;
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
        this.$el.html(
            this.table.render().el
        );
        return this;
    },
    renderEntry: function (view)
    {
        view.render();
    }
});