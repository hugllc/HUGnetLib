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
 * @subpackage DeviceSensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
HUGnet.viewHelpers = {
    formatDate: function (date, alt)
    {
        alt = (alt !== undefined) ? alt : "Never";
        if ((date == undefined) || (date == 0)) {
            return alt;
        }
        var d = new Date(date * 1000);
        return d.toString();
    },
    selectInt: function (start, end, inc, selected)
    {
        var html = "";
        for (; start <= end; start += inc) {
            html += '<option value="'+start+'" ';
            if (start == selected) {
                html += 'selected="selected"';
            }
            html += '>'+start+'</option>';
        }
        return html;
    }
};
