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
HUGnet.ExportView = Backbone.View.extend({
    template: '#ExportTemplate',
    tagName: 'div',
    iframe: undefined,
    csvlimit: 40000,
    since: undefined,
    until: undefined,
    sinceDate: undefined,
    untilDate: undefined,
    csvurl: "",
    order: 1,
    events: {
        'click .minute30': 'minute30',
        'click .minute240': 'minute240',
        'click .minute720': 'minute720',
        'click .exportCSV': 'exportCSV'
    },
    initialize: function (options)
    {
        this.url = options.url;
        // Default to 30 minutes
        this.minute30();
    },
    minute30: function ()
    {
        this.setLatest(30);
    },
    minute240: function ()
    {
        this.setLatest(240);
    },
    minute720: function ()
    {
        this.setLatest(720);
    },
    setLatest: function (period)
    {
        var date = new Date;
        this.since = date.getTime() - (period * 60 * 1000);
        this.until = 0;
        this.updateDates();
    },
    exportCSV: function ()
    {
        this.since = Date.parse(this.$('#since').val());
        this.until = Date.parse(this.$('#until').val());
        if (parseInt(this.$('#order').val(), 10) == 0) {
            this.order = 0;
        } else {
            this.order = 1;
        }
        this.csvurl = this.url+"?task=history&format=CSV";
        if (this.until != 0) {
            var until = this.until;
        } else {
            var until = this.last;
        }
        this.csvurl += "&id="+this.model.get("id").toString(16);
        this.csvurl += "&since="+parseInt(this.since/1000);
        this.csvurl += "&until="+parseInt(until/1000);
        this.csvurl += "&order="+this.order;
        this.csvurl += "&limit="+this.csvlimit;
        this.$("#csvurl").html(this.csvurl);
        this.iframe.attr('src', this.csvurl);
    },
    updateDates: function ()
    {
        var d = new Date;
        function pad(n){return n<10 ? '0'+n : n};
        if (this.until != 0) {
            d.setTime(this.until);
        }
        this.untilDate = pad(d.getMonth()+1)+'/'
            + pad(d.getDate())+'/'
            + d.getFullYear()+' '
            + pad(d.getHours())+':'
            + pad(d.getMinutes())+':'
            + pad(d.getSeconds());
        d.setTime(this.since);
        this.sinceDate = pad(d.getMonth()+1)+'/'
            + pad(d.getDate())+'/'
            + d.getFullYear()+' '
            + pad(d.getHours())+':'
            + pad(d.getMinutes())+':'
            + pad(d.getSeconds());
        this.$("#since").val(this.sinceDate);
        this.$("#until").val(this.untilDate);
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
        if (this.until != 0) {
            data.until = this.until;
        } else {
            data.until = this.last;
        }
        data.sinceDate = this.sinceDate;
        data.untilDate = this.untilDate;
        data.csvlimit  = this.csvlimit;
        data.csvurl    = this.csvurl;
        data.order     = this.order;
        var d = new Date;
        d.setTime(data.until);
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        this.$('#since').datetimepicker();
        this.$('#until').datetimepicker();
        this.iframe = $('<iframe>', { id:'exportCSV' }).hide();
        this.$el.append(this.iframe);
        return this;
    },
    renderEntry: function (view)
    {
        view.render();
    }
});
