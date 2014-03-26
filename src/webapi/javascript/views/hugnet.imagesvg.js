/**
 * hugnet.imagesvg.js
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
 * @subpackage ImageSVGs
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
* @subpackage ImageSVGs
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.ImageSVGView = Backbone.View.extend({
    template: "#ImageSVGTemplate",
    url: '/HUGnetLib/HUGnetLibAPI.php',
    style: "",
    events: {
    },
    initialize: function (options)
    {
        if (options) {
            if (options.url) this.url = options.url;
            if (options.style) this.style = options.style;
        }
        //this.model.each(this.insert, this);
        this.model.on('change', this.render, this);
        this.model.on('datasync', this.updatePoints, this);
    },
    /**
    * This renders the SVG
    *
    * @return null
    */
    render: function ()
    {
        var data = this.model.toJSON();
        data.style = this.style;
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        this.$("table").tablesorter({ widgets: ['zebra'] });
        this.$("table").trigger('update');
        this.update();
        return this;
    },
    /**
     * This updates the data in the image
     * 
     * @return null
     */
    update: function (date, type)
    {
        this.model.getReading(date, type);
    },
    /**
     * This updates the data in the image
     * 
     * @return null
     */
    updatePoints: function ()
    {
        var points = this.model.get("points");
        var data   = this.model.get("data");
        _.each(
            points,
            function (point, key)
            {
                var id = "point"+key;
                // This escapes these characters correctly for the selector
                //id = id.replace(/([ #;?&,.+*~\':"!^$[\]()=>|\/@])/g,'\\$1');
                var el = $('text#'+id+' tspan');
                el.text(data.points[key]);
            }
        );
    }
});
