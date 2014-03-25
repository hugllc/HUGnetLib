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
HUGnet.ImageView = Backbone.View.extend({
    template: '#ImageViewTemplate',
    tagName: 'div',
    pause: 10,
    rows: 0,
    id: undefined,
    table: undefined,
    plot: undefined,
    before: 0,
    period: 30,
    polling: false,
    type: '30SEC',
    beforeId: "before",
    timer: null,
    events: {
        'click #autorefresh': 'setRefresh',
        'submit': 'submit',
        'change #type': 'submit'
    },
    initialize: function (options)
    {
        this.url = options.url;
        this.data = options.data;
        this.parent = options.parent;
        var device;
        var i;
        this.header = [];
        this.fields = [];
        this.classes = [];
        this.units = [];
        this.header[0] = 'Date';
        this.fields[0] = 'Date';
        this.classes[0] = '';
        this.pause = (options.pause !== undefined) ? parseInt(options.pause, 10) : this.pause;

        var avgTypes = this.model.get("averageTypes");
        if (avgTypes["30SEC"]) {
            this.type = "30SEC";
        } else {
            this.type   = "15MIN";
        }
        this.type = (options.type !== undefined) ? options.type : this.type;
        this.getLatest();
        this.on("update", this.update, this);
        this.image = new HUGnet.ImageSVGView({
            model: this.model,
            style: "border: thin solid black; margin-left: auto; margin-right: auto; display: block; margin-top: 20px;"
        });
    },
    update: function ()
    {
//        this.image.update();
    },
    updateDates: function ()
    {
        var d = new Date;
        d.setTime(this.last);
        var before = this._formatDate(d);
        this.$("#"+this.beforeId).val(before);
    },
    getLatest: function ()
    {
        this.before  = (new Date()).getTime();
        this.updateDates();
    },
    submit: function ()
    {
        this.stopPoll();
        if (!this.polling) {
            this.$('#autorefresh').prop("disabled", true);
            this.$('input[type="submit"]').prop('disabled', true);
            this.before = Date.parse(this.$('#'+this.beforeId).val()+' UTC');
            this.type = this.$('#type').val();
            this.updateDates();
        }
    },
    exit: function()
    {
        this.image.remove();
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
        data.type = this.type;
        data.before = this.before;
        var d = new Date;
        d.setTime(data.before);
        data.beforeDate = this._formatDate(d);
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        if (this.beforeId == "before") {
            this.beforeId = "before"+this.model.get("id");
            this.$("#before").attr("id", this.beforeId);
        }
        this.$("#"+this.beforeId).datetimepicker();
        this.$el.append(this.image.render().el);
        return this;
    },
    _formatDate: function (d)
    {
        function pad(n){return n<10 ? '0'+n : n};
        return pad(d.getUTCMonth()+1)+'/'
            + pad(d.getUTCDate())+'/'
            + d.getUTCFullYear()+' '
            + pad(d.getUTCHours())+':'
            + pad(d.getUTCMinutes())+':'
            + pad(d.getUTCSeconds());
    }
});