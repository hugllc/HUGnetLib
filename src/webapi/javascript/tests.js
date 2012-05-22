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
 * @subpackage Devices
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
* @subpackage Tests
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.TestSuite = Backbone.View.extend({
    tabs: undefined,
    data: {},
    initialize: function (options)
    {
        this.tests = new HUGnet.TestsView({
            model: options.tests,
        });

        this.render();
    },
    render: function ()
    {
        this.$el.html('<div id="tests-tabs"><ul></ul></div>');
        var self = this;
        this.tabs = $('#tests-tabs').tabs({
            tabTemplate: '<li><a href="#{href}">#{label}</a></li>',
            cookie: {
                // store a session cookie
                expires: 10
            }
        });
        this.tabs.tabs("add", '#tests-tabs-tests', 'Test Definitions');
        $('#tests-tabs-tests').html(this.tests.render().el);

        /* Further tabs will have a close button */
        this.tabs.tabs("option", "tabTemplate", '<li style="white-space: nowrap;"><a href="#{href}">#{label}</a> <span name="#{href}" class="ui-icon ui-icon-close">Remove Tab</span></li>');
        /* close icon: removing the tab on click */
        $( "#tests-tabs span.ui-icon-close" ).live( "click", function(event, ui) {
            var index = $( "li", self.tabs ).index( $( this ).parent() );
            var id = $( this ).attr("name");
            self.data[id].exit();
            delete self.data[id];
            self.tabs.tabs( "remove", index );
        });

        /* This selects a newly added tab */
        this.tabs.tabs({
            add: function(event, ui) {
                self.tabs.tabs('select', '#' + ui.panel.id);
            }
        });

        this.tests.bind(
            "run",
            function (test)
            {
                this.testTab(test, 'poll');
            },
            this
        );
        this.tests.bind(
            "view",
            function (test)
            {
                this.testTab(test, 'view');
            },
            this
        )
    },
    testTab: function (test, mode)
    {
        var self = this;
        var tag = "#tabs-test" + test.get("id");
        if (this.data[tag] !== undefined) {
            alert('Tab for "' + test.get("name") + '" is already open');
            return;
        }
        var title = "";
        if (mode === 'poll') {
            this.data[tag] = new HUGnet.DataPollView({
                parent: tag,
                mode: mode,
                model: test,
                TestID: 1,
            });
            title = 'Run Test "' + test.get("name") + '"';
        } else {
            this.data[tag] = new HUGnet.DataView({
                parent: tag,
                mode: mode,
                model: test,
                TestID: 1,
            });
            title = 'View Test "' + test.get("name") + '"'
        }

        this.tabs.tabs("add", tag, title);
        $(tag).html(this.data[tag].render().el);
    }
});
