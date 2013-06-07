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
    data: {},
    device: {},
    header: {},
    fields: {},
    classes: {},
    units: [],
    parent: 'unknown',
    since: 0,
    until: 0,
    last: 0,
    period: 30,
    polling: false,
    iframe: undefined,
    csvlimit: 40000,
    progress: undefined,
    sinceId: "since",
    untilId: "until",
    events: {
        'click #autorefresh': 'setRefresh',
        'submit': 'submit',
        'click .exportCSV': 'exportCSV',
        'change #setPeriod': '_setPeriod',
        'change #type': 'submit'
    },
    initialize: function (options)
    {
        this.url = options.url;
        this.data = options.data;
        this.parent = options.parent;
        var device;
        var i;
        this.header = {};
        this.fields = {};
        this.device = {};
        this.classes = {};
        this.header[0] = 'Date';
        this.fields[0] = 'Date';
        this.classes[0] = '';
        var channels = this.model.get('dataChannels');
        var index = 1;
        for (i in channels) {
            if ((channels[i].dataType !== undefined)
               && (channels[i].dataType !== 'ignore')
            ) {
                this.units[index] = channels[i].units;
                this.header[index] = channels[i].label + ' ('+channels[i].units+')';
                this.fields[index] = 'Data' + i;
                this.classes[index] = '';
                index++;
            }
        }
        this.pause = (options.pause !== undefined) ? parseInt(options.pause, 10) : this.pause;
        this.type = (options.type !== undefined) ? options.type : this.type;
        this.history = new HUGnet.Histories(
            null,
            {
                device: this.device,
                id: this.model.get('id'),
                mode: this.mode,
                type: this.type,
                url: this.url
            }
        );
        this._setupProgress();
        this.getLatest();
        this.history.on(
            'sync',
            function ()
            {
                this.$("#data-records").text(this.history.length);
            },
            this
        );
        this.getLatest();
        this.table = new HUGnet.DataTable({
            model: this.history,
            header: this.header,
            fields: this.fields,
            classes: this.classes
        });
        this.setupPlot();
    },
    updateDates: function ()
    {
        var since = this.history.since;
        var until = this.history.until;
        var d = new Date;
        function pad(n){return n<10 ? '0'+n : n};
        if (until != 0) {
            d.setTime(until);
        } else {
            d.setTime(this.last);
        }
        until = pad(d.getMonth()+1)+'/'
            + pad(d.getDate())+'/'
            + d.getFullYear()+' '
            + pad(d.getHours())+':'
            + pad(d.getMinutes())+':'
            + pad(d.getSeconds());
        if (since != 0) {
            d.setTime(since);
            since = pad(d.getMonth()+1)+'/'
                + pad(d.getDate())+'/'
                + d.getFullYear()+' '
                + pad(d.getHours())+':'
                + pad(d.getMinutes())+':'
                + pad(d.getSeconds());
        }
        this.$("#"+this.sinceId).val(since);
        this.$("#"+this.untilId).val(until);
    },
    exportCSV: function ()
    {
        var url = this.url+"?task=history&action=get&format=CSV";
        if (this.until != 0) {
            var until = this.until;
        } else {
            var until = this.last;
        }
        url += "&id="+this.model.get("id").toString(16);
        url += "&data[since]="+parseInt(this.since/1000);
        url += "&data[until]="+parseInt(until/1000);
        url += "&data[order]=desc";
        url += "&data[limit]="+this.csvlimit;
        url += "&data[type]="+this.history.type;
        this.iframe.attr('src',url);
    },
    _setPeriod: function ()
    {
        var period = parseInt(this.$('#setPeriod').val(), 10);
        if (period != 0) {
            if (period > 1440) {
                period = 1440;
            }
            this.period = period;
            this._setupProgress();
            this.getLatest();
        }
    },
    getLatest: function ()
    {
        this.last  = (new Date()).getTime();
        this.history.latest(this.period);
        this.since = this.history.since;
        this.until = this.history.until;
        this.updateDates();
    },
    setupPlot: function ()
    {
        var d = new Date();
        this.plot = new HUGnet.DataFlot({
            parent: this.parent,
            model: this.history,
            header: this.header,
            fields: this.fields,
            classes: this.classes,
            units: this.units,
            timeOffset: d.getTimezoneOffset() * 60000
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
            this.$('#autorefresh').prop("disabled", true);
            this.$('input[type="submit"]').prop('disabled', true);
            this.since = Date.parse(this.$('#'+this.sinceId).val());
            this.until = Date.parse(this.$('#'+this.untilId).val());
            if (this.$('#type').val()) {
                this.history.type = this.$('#type').val();
            }
            this._setupProgress();
            this.history.on('sync', this._finishFetch, this);
            this.history.getPeriod(this.since, this.until);
            this.updateDates();
        }
    },
    _setupProgress: function()
    {
        if (typeof this.progress !== "object") {
            this.progress = new HUGnet.Progress({
                modal: false,
                draggable: true,
                width: 300,
                title: "Building Data Array",
                dialogClass: "window",
                zIndex: 500
            });
            this.history.on('fetchagain', this._updateProgress, this);
            this.history.on('fetchfail', this._teardownProgress, this);
            this.history.on('fetchdone', this._teardownProgress, this);
        }
    },
    _teardownProgress: function()
    {
        this.history.off('fetchagain', this._updateProgress, this);
        this.history.off('fetchfail', this._teardownProgress, this);
        this.history.off('fetchdone', this._teardownProgress, this);
        if (this.progress !== undefined) {
            this.progress.update(1);
            this.progress.remove();
            delete this.progress;
        }
    },
    _updateProgress: function(value)
    {
        if (typeof this.progress === "object") {
            this.progress.update(value);
        }
    },
    getField: function (index, field)
    {
        if (parseInt(field, 10) === field) {
            return "Data" + index;
        }
        return field;
    },
    exit: function()
    {
        this.history.reset();
        this.table.remove();
        this.plot.remove();
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
            this.$('select').prop('disabled', true);
            this.$('input[type="text"]').prop('disabled', true);
            this.history.on("fetchfail", this._poll, this);
            this.history.on("fetchdone", this._poll, this);
            this.getLatest();
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
            this.$('#autorefresh').prop("checked", false);
        }
    },
    _finishFetch: function ()
    {
        this.$('input[type="submit"]').prop('disabled', false);
        this.$('select').prop('disabled', false);
        this.$('input[type="text"]').prop('disabled', false);
        this.$('#autorefresh').prop("disabled", false);
        this.history.off("fetchfail", this._finishFetch, this);
        this.history.off("fetchdone", this._finishFetch, this);
        this.history.off('sync', this._finishFetch, this);
        this.polling = false;
    },
    _poll: function ()
    {
        var self = this;
        setTimeout(
            function () {
                self.getLatest();
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
        data.since = this.since;
        data.type = this.history.type;
        if (this.until != 0) {
            data.until = this.until;
        } else {
            data.until = this.last;
        }
        var d = new Date;
        function pad(n){return n<10 ? '0'+n : n};
        d.setTime(data.until);
        data.untilDate = pad(d.getMonth()+1)+'/'
            + pad(d.getDate())+'/'
            + d.getFullYear()+' '
            + pad(d.getHours())+':'
            + pad(d.getMinutes())+':'
            + pad(d.getSeconds());
        d.setTime(data.since);
        data.sinceDate = pad(d.getMonth()+1)+'/'
            + pad(d.getDate())+'/'
            + d.getFullYear()+' '
            + pad(d.getHours())+':'
            + pad(d.getMinutes())+':'
            + pad(d.getSeconds());
        data.csvurl  = this.url+"?task=history&action=get&format=CSV";
        data.csvurl += "&id="+data.id.toString(16);
        data.csvurl += "&data[since]="+parseInt(data.since/1000);
        data.csvurl += "&data[until]="+parseInt(data.until/1000);
        data.csvurl += "&data[order]="+((data.limit === 0) ? "desc" : "asc");
        if (data.until == 0) {
            data.csvurl += "&data[limit]="+this.csvlimit;
        }
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        if (this.sinceId == "since") {
            this.sinceId = "since"+this.model.get("id");
            this.untilId = "until"+this.model.get("id");
            this.$("#since").attr("id", this.sinceId);
            this.$("#until").attr("id", this.untilId);
        }
        this.$("#"+this.sinceId).datetimepicker();
        this.$("#"+this.untilId).datetimepicker();
        this.iframe = $('<iframe>', { id:'exportCSV' }).hide();
        this.$el.append(this.plot.el);
        this.$el.append(this.table.render().el);
        this.$el.append(this.iframe);
        return this;
    },
    renderEntry: function (view)
    {
        view.render();
    }
});
