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
HUGnet.ImageView = Backbone.View.extend({
    template: '#ImageViewTemplate',
    tagName: 'div',
    pause: 60,
    id: undefined,
    table: undefined,
    plot: undefined,
    before: 0,
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
        if (options) {
            if (options.url) this.url = options.url;
            if (options.parent) this.parent = options.parent;
        }
        var device;
        var i;
        this.pause = (options.pause !== undefined) ? parseInt(options.pause, 10) : this.pause;

        var avgTypes = this.model.get("averageTypes");
        if (avgTypes["30SEC"]) {
            this.type = "30SEC";
        } else {
            this.type   = "15MIN";
        }
        this.type = (options.type !== undefined) ? options.type : this.type;
        this.image = new HUGnet.ImageSVGView({
            model: this.model,
            style: "border: thin solid black; margin-left: auto; margin-right: auto; display: block; margin-top: 20px;",
            id: "displayimg-"+this.model.get("name"),
        });
        this.getLatest();
        this._template = _.template($(this.template).html());
    },
    update: function ()
    {
        this.image.updateData(this.before, this.type);
    },
    updateDates: function ()
    {
        var d = new Date;
        d.setTime(this.before);
        var before = this._formatDate(d);
        this.$("#"+this.beforeId).val(before);
    },
    getLatest: function ()
    {
        this.before  = (new Date()).getTime();
        this.updateDates();
        this.update();
    },
    submit: function ()
    {
        this.stopPoll();
        if (!this.polling) {
            this.$('#autorefresh').prop("disabled", true);
            this.$('input[type="submit"]').prop('disabled', true);
            this.before = Date.parse(this.$('#'+this.beforeId).val()+' UTC');
            this.type = this.$('#type').val();
            this.image.on("datasyncfail", this._finishFetch, this);
            this.image.on("datasync", this._finishFetch, this);
            this._setURL();
            this.updateDates();
            this.update();
        }
    },
    exit: function()
    {
        this.image.remove();
        this.remove();
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
    startPoll: function()
    {
        if (!this.polling) {
            this.polling = true;
            this.$('input[type="submit"]').prop('disabled', true);
            this.$('select').prop('disabled', true);
            this.$('input[type="text"]').prop('disabled', true);
            this.image.on("datasyncfail", this._poll, this);
            this.image.on("datasync", this._poll, this);
            this.getLatest();
        }
    },
    stopPoll: function()
    {
        if (this.polling) {
            clearTimeout(this.timer);
            this.image.off("datasyncfail", this._poll, this);
            this.image.off("datasync", this._poll, this);
            this.$('#autorefresh').prop("checked", false);
            this._finishFetch();
        }
    },
    _finishFetch: function ()
    {
        this.$('input[type="submit"]').prop('disabled', false);
        this.$('select').prop('disabled', false);
        this.$('input[type="text"]').prop('disabled', false);
        this.$('#autorefresh').prop("disabled", false);
        this.image.off("datasyncfail", this._finishFetch, this);
        this.image.off("datasync", this._finishFetch, this);
        this.polling = false;
    },
    _poll: function ()
    {
        var self = this;
        this.timer = setTimeout(
            function () {
                self.getLatest();
            },
            (this.pause * 1000)
        );
    },
    _setURL: function ()
    {
        var id      = this.model.get("id");
        var imgurl  = window.location.origin+"/";
        imgurl     += this.url+"?task=image&action=get";
        imgurl     += "&id="+id.toString(16);
        imgurl     += "&data[type]="+this.type;
        imgurl     += "&data[date]=now";
        imgurl     += '&format=PNG';
        $("span#url").text(imgurl);
        return imgurl;
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
        var id   = 'svgimg'+this.model.get("id");
        var data = this.model.toJSON();
        data.type = this.type;
        data.before = this.before;
        var d = new Date;
        d.setTime(data.before);
        data.beforeDate = this._formatDate(d);
        data.svg = '<div id="'+id+'">/div>';
        data.imgurl  = this._setURL();

        this.$el.html(this._template(data));
        if (this.beforeId == "before") {
            this.beforeId = "before"+this.model.get("id");
            this.$("#before").attr("id", this.beforeId);
        }
        this.$("#"+this.beforeId).datetimepicker();
        this.$("div#"+id).html("");
        this.$("div#"+id).append(this.image.render().el);
        this.image.update();
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
