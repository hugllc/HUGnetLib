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
HUGnet.DataView = Backbone.View.extend({
    template: '#DataViewTemplate',
    tagName: 'div',
    pause: 10,
    rows: 0,
    id: undefined,
    table: undefined,
    plot: undefined,
    limit: 50,
    data: {},
    device: {},
    header: {},
    fields: {},
    classes: {},
    since: 0,
    until: 0,
    polling: false,
    events: {
        'click #autorefresh': 'setRefresh',
        'submit': 'submit',
    },
    initialize: function (options)
    {
        this.setup(options);
    },
    setup: function (options)
    {
        this.data = options.data;
        var device;
        var i;
        this.header = {};
        this.fields = {};
        this.device = {};
        this.classes = {};
        this.header[0] = 'Date';
        this.fields[0] = 'Date';
        this.classes[0] = '';
        var sensors = this.model.get('sensors');
        var index = 1;
        for (i in sensors) {
            if ((sensors[i].storageType !== 'ignore') && (sensors[i].dataType !== 'ignore')) {
                this.header[index] = sensors[i].location + ' ('+sensors[i].units+')';
                this.fields[index] = 'Data' + i;
                this.classes[index] = '';
                index++;
            }
        }
        this.pause = (options.pause !== undefined) ? parseInt(options.pause) : this.pause;
        this.limit = (options.limit !== undefined) ? parseInt(options.limit) : this.limit;
        this.type = (options.type !== undefined) ? options.type : this.type;
        this.history = new HUGnet.Histories(
            null,
            {
                device: this.device,
                id: this.model.get('id'),
                mode: this.mode,
                type: this.type,
                limit: this.limit,
            }
        );
        this.history.on(
            'sync',
            function ()
            {
                this.$("#data-records").text(this.history.length);
            },
            this
        );
        this.history.fetch();
        this.table = new HUGnet.DataTable({
            model: this.history,
            header: this.header,
            fields: this.fields,
            classes: this.classes,
        });
        this.setupPlot();
    },
    setupPlot: function ()
    {
        this.plot = new HUGnet.DataFlot({
            model: this.history,
            header: this.header,
            fields: this.fields,
            classes: this.classes,
        });
    },
    setRefresh: function ()
    {
        if (this.$('#autorefresh').prop("checked")) {
            this.autorefresh = this.$('#autorefresh').val() - 0;
            this.startPoll();
        } else {
            this.stopPoll();
            this.autorefresh = 0;
        }
    },
    submit: function ()
    {
        this.stopPoll();
        if (!this.polling) {
            this.since = Date.parse(this.$('#since').val());
            this.until = Date.parse(this.$('#until').val());
            this.history.getPeriod(this.since, this.until);
            var progress = new HUGnet.Progress({
                modal: false,
                draggable: true,
                width: 300,
                title: "Building Data Array",
                dialogClass: "window",
                zIndex: 500,
            });
            this.history.on('fetchagain', progress.update, progress);
            this.history.on('sync', progress.remove, progress);
        }
    },
    getField: function (index, field)
    {
        if (parseInt(field) == field) {
            return "Data" + index;
        }
        return field;
    },
    exit: function()
    {
        this.reset();
        this.stopPoll();
        this.remove();
    },
    reset: function()
    {
        this.history.clear();
        this.rows = 0;
    },
    startPoll: function()
    {
        if (!this.polling) {
            this.polling = true;
            this.$('input[type="submit"]').prop('disabled', true);
            this.history.on("fetchfail", this._poll, this);
            this.history.on("fetchdone", this._poll, this);
            this.history.latest();
        }
    },
    stopPoll: function()
    {
        if (this.polling) {
            this.history.off("fetchfail", this._poll, this);
            this.history.off("fetchdone", this._poll, this);
            this.history.on("fetchfail", this._finishFetch, this);
            this.history.on("fetchdone", this._finishFetch, this);
            this.$('#autorefresh').prop("disabled", true);
            this.$('#autorefresh').prop("checked", false)
        }
    },
    _finishFetch: function ()
    {
        this.$('input[type="submit"]').prop('disabled', false);
        this.$('#autorefresh').prop("disabled", false);
        this.history.off("fetchfail", this._finishFetch, this);
        this.history.off("fetchdone", this._finishFetch, this);
        this.polling = false;
    },
    _poll: function ()
    {
        var self = this;
        setTimeout(
            function () {
                self.history.latest();
            },
            (this.pause * 1000)
        );
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
        data.limit = this.limit;
        data.since = this.since;
        data.until = this.until;
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        this.$('#since').datetimepicker();
        this.$('#until').datetimepicker();
        this.$el.append(this.plot.el);
        this.$el.append(this.table.render().el);
        return this;
    },
    renderEntry: function (view)
    {
        view.render();
    }
});
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
HUGnet.DataPollView = HUGnet.DataView.extend({
    template: '#DataPollTemplate',
    pause: 1,
    events: {
        'click .startPoll': 'startPoll',
        'click .stopPoll': 'stopPoll',
    },
    initialize: function (options)
    {
        this.setup(options);
    },
    startPoll: function()
    {
        this.$('.stopPoll').show();
        this.$('.startPoll').hide();
        this.$('.exit').hide();
        this.history.on("pollfail", this._poll, this);
        this.history.on("polldone", this._poll, this);
        this.history.poll();
    },
    stopPoll: function()
    {
        console.log("stopping");
        this.history.off("pollfail", this._poll, this);
        this.history.off("polldone", this._poll, this);
        this.$('.stopPoll').hide();
        this.$('.startPoll').show();
        this.$('.exit').show();
    },
    _poll: function ()
    {
        var self = this;
        setTimeout(
            function () {
                self.history.poll();
            },
            (this.pause * 1000)
        );
    },
});