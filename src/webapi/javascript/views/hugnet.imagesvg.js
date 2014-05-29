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
    tagName: 'div',
    style: "",
    svg: null,
    date: null,
    type: null,
    name: "",
    id: "",
    nameSpace: "http://www.w3.org/2000/svg",
    points: {},
    groups: {},
    boxes: {},
    text: {},
    background: null,
    draggable: false,
    events: {
    },
    initialize: function (options)
    {
        if (options) {
            if (options.url) this.url = options.url;
            if (options.style) this.style = options.style;
            if (options.draggable) this.draggable = options.draggable;
            if (options.id) this.draggable = options.id;
        }
        //this.model.each(this.insert, this);
        this.model.on('change', this.render, this);
        this.model.on('datasync', this.updatePoints, this);
        //this.$el = $(document.createElement("svg"));
        var name = this.model.get("name");
        this.name = name.replace(/([ #;?&,.+*~\':"!^$[\]()=>|\/@])/g,'_');
        if (this.id == "") {
            this.id = "svgimage-"+this.name;
        }
        this.id = this.id.replace(/([ #;?&,.+*~\':"!^$[\]()=>|\/@])/g,'_');
        this.$el.attr("id", this.id);
        this.svg = SVG(this.$el[0]);
        this.svg.attr("style", this.style);
    },
    /**
    * This renders the SVG
    *
    * @return null
    */
    render: function ()
    {
        var self = this;
        this.points = {};
        this.groups = {};
        this.boxes  = {};
        this.text   = {};
        var height = this.model.get("height");
        var width  = this.model.get("width");
        this.svg.clear();
        this.svg.size(width, height);
        var imagetype = this.model.get("imagetype");
        if (imagetype != "") {
            this.background = this.svg.image(
                'data:'+this.model.get("imagetype")+';base64,'+this.model.get("image"), 
                width, 
                height
            );
        }
        var points = this.model.get("points");
        var svg  = this.svg;
        _.each(points, function(point, index) {
            var group = svg.group();
            group.attr("id", "Point"+index);
            var text = group.text(function(add) {
                if (point.pretext != "") {
                    add.tspan(point.pretext);
                }
                var string = point.devid+"."+point.datachan;
                if (string != ".") {
                    self.points[index] = add.tspan(string);
                }
                if (point.posttext != "") {
                    add.tspan(point.posttext);
                }
            });
            text.fill({color: point.color});
            text.font({size: point.fontsize});
            var box  = group.rect(10, 10);
            self.textBackground(text, box, point.background);
            text.front();
            group.move(parseInt(point.x, 10), parseInt(point.y, 10));
            self.groups[index] = group;
            self.text[index]   = text;
            self.boxes[index]  = box;
            if (self.draggable) {
                group.draggable();
                group.dragend = function (delta, event) {
                    self.trigger("dragend", point, delta, event);
                };
                group.dragstart = function (delta, event) {
                    self.trigger("dragend", point, delta, event);
                };
            }
        });
        if (this.background) {
            // Send the background to the back.
            this.background.back();
        }
        return this;
    },
    textBackground: function (text, box, color)
    {
        var bbox = text.bbox();
        box.width(bbox.width);
        box.height(bbox.height);
        box.fill({ color: color });
        box.move(bbox.x, bbox.y);
    },
    /**
     * This updates the data in the image
     * 
     * @return null
     */
    updateData: function (date, type)
    {
        if (date) {
            this.date = date;
        }
        if (type) {
            this.type = type;
        }
        this.model.getReading(this.date, this.type);
    },
    /**
     * This updates the data in the image
     * 
     * @return null
     */
    update: function (data)
    {
        var points = this.model.get("points");
        var self   = this;
        _.each(
            points,
            function (point, key)
            {
                //var id = "point"+key;
                // This escapes these characters correctly for the selector
                //id = id.replace(/([ #;?&,.+*~\':"!^$[\]()=>|\/@])/g,'\\$1');
                //var el = $('text#'+id+' tspan');
                if (self.points[key] && data) {
                    self.points[key].clear();
                    self.points[key].plain(data.points[key]);
                }
                if (self.text[key] && self.boxes[key]) {
                    self.textBackground(
                        self.text[key], self.boxes[key], point.background
                    );
                }
            }
        );
        this.trigger("datasync");
    },
    /**
     * This updates the data in the image
     * 
     * @return null
     */
    hilight: function (index)
    {
        var offset = 5;
        this.$("svg rect.datapointhilight").remove();
        var box  = this.svg.rect(
            this.boxes[index].width() + offset, 
            this.boxes[index].height() + offset
        );
        box.attr("class", "datapointhilight");
        
        this.groups[index].add(box);
        this.boxes[index].before(box);
        box.center(this.boxes[index].cx(), this.boxes[index].cy());
    },
    /**
     * This updates the data in the image
     * 
     * @return null
     */
    updatePoints: function ()
    {
        var data   = this.model.get("data");
        this.update(data);
        this.trigger("datasync");
    }
});
