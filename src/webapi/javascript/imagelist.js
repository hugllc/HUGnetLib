/**
 * hugnet.image.js
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
 * @subpackage Images
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/**
* This is the model that stores the images.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage Tests
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.ImageList = Backbone.View.extend({
    tabs: undefined,
    id: "images-tabs",
    readonly: false,
    data: {},
    filter: {publish: 1},
    url: '/HUGnetLib/HUGnetLibAPI.php',
    tabTemplate: '<li style="white-space: nowrap;"><a href="#{href}">#{label}</a> <span name="#{href}" class="ui-icon ui-icon-close">Remove Tab</span></li>',
    initialize: function (options)
    {
        if (options) {
            if (options.url) {
                this.url = options.url;
            }
            if (options.id) {
                this.id = options.id;
            }
            if (options.readonly) {
                this.readonly = options.readonly;
            }
        }
        this.images = new HUGnet.ImageListView({
            model: options.images,
            url: this.url,
        });
        this.render();
    },
    render: function ()
    {
        this.$el.html('<div id="'+this.id+'"><ul></ul></div>');
        var self = this;
        this.tabs = $('#'+this.id).tabs({
            active: 0,
            cookie: {
                // store a session cookie
                expires: 10
            },
            activate: function(event, ui) 
            {
                self.$(".tablesorter").trigger("update");
                var tag = ui.newPanel.selector;
                tag = tag.replace("#", "");
                if (self.data[tag]) {
                    self.data[tag].trigger("update");
                }
            }
        });
        var tag = this.id+'-views';
        this.tabs.find( ".ui-tabs-nav" ).append('<li><a href="#'+tag+'">Image List</a></li>');
        this.tabs.append( '<div id="'+tag+'"></div>' );
        $('#'+tag).html(this.images.render().el);
        this.tabs.tabs("refresh");
        this.tabs.tabs("option", "active", 0);

        /* Further tabs will have a close button */
        //this.tabs.tabs("option", "tabTemplate", '<li style="white-space: nowrap;"><a href="#{href}">#{label}</a> <span name="#{href}" class="ui-icon ui-icon-close">Remove Tab</span></li>');
        /* close icon: removing the tab on click */
        var tabs = this.tabs;
        $(document).on( "click", "#"+this.id+" span.ui-icon-close", function(event, ui) {
            var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
            self.data[panelId].exit();
            delete self.data[panelId];
            $( "#" + panelId ).remove();
            tabs.tabs( "refresh" );
        });

        this.images.bind(
            "view",
            function (image)
            {
                this.imageTab(image);
            },
            this
        );
    },
    imageTab: function (image)
    {
        var self = this;
        var tag = this.id + image.get("id");
        if (this.data[tag] !== undefined) {
            return;
        }
        this.data[tag] = new HUGnet.ImageView({
            parent: tag,
            model: image,
            TestID: 1,
            url: this.url
        });
        var title = 'Image: "' + image.get("name") + '"';

        //this.tabs.tabs("add", tag, title);
        this.tabs.find( ".ui-tabs-nav" ).append('<li><a href="#'+tag+'">'+title+'</a> <span name="#{href}" class="ui-icon ui-icon-close">Remove Tab</span></li>');
        this.tabs.append( "<div id='"+tag+"'></div>" );
        $("#"+tag).html(this.data[tag].render().el);
        this.tabs.tabs("refresh");
        this.tabs.tabs("option", "active", -1);
    },
});
