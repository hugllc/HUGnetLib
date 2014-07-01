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
 * @subpackage DeviceSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
HUGnet.viewHelpers = {
    formatDate: function (date, alt)
    {
        alt = (alt !== undefined) ? alt : "Never";
        if ((date === undefined) || (date === 0)) {
            return alt;
        }
        var d = new Date(date * 1000);
        return d.toString();
    },
    selectInt: function (start, end, inc, selected)
    {
        var html = "";
        if (inc === 0) {
            return html;
        }
        for (; ((start <= end) && (inc > 0)) || ((start >= end) && (inc < 0)); start += inc) {
            html += '<option value="'+start+'"';
            if (start === (selected - 0)) {
                html += ' selected="selected"';
            }
            html += '>'+start+'</option>';
        }
        return html;
    },
    sqlDate: function (unixdate)
    {
        var date = new Date(unixdate);
        var m = date.getMonth() + 1;
        var d = date.getDate();
        var Y = date.getFullYear();
        var H = date.getHours();
        var i = date.getMinutes();
        var s = date.getSeconds();

        if (H < 10) {
            H = "0" + H;
        }
        if (i < 10) {
            i = "0" + i;
        }
        if (s < 10) {
            s = "0" + s;
        }
        if (m < 10) {
            m = "0" + m;
        }
        if (d < 10) {
            d = "0" + d;
        }

        return Y + "-" + m + "-" + d + " " + H + ":" + i + ":" + s;
    },
    sqlUTCDate: function (unixdate)
    {
        var date = new Date(unixdate);
        var m = date.getUTCMonth() + 1;
        var d = date.getUTCDate();
        var Y = date.getUTCFullYear();
        var H = date.getUTCHours();
        var i = date.getUTCMinutes();
        var s = date.getUTCSeconds();

        if (H < 10) {
            H = "0" + H;
        }
        if (i < 10) {
            i = "0" + i;
        }
        if (s < 10) {
            s = "0" + s;
        }
        if (m < 10) {
            m = "0" + m;
        }
        if (d < 10) {
            d = "0" + d;
        }

        return Y + "-" + m + "-" + d + " " + H + ":" + i + ":" + s;
    },
    showInfo: function (info, key)
    {
        var html = '';
        if (info) {
            if (typeof info == "object") {
                if (info[key]) {
                    html = ' title="'+info[key]+'" ';
                }
            } else if (typeof info == "string") {
                html = ' title="'+info+'" ';
            }
        }
        return html;
    }
};
