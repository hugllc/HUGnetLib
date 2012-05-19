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
 * @subpackage Tests
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
var TestPropertiesView = Backbone.View.extend({
    template: '#TestPropertiesTemplate',
    tTemplate: '#TestPropertiesTitleTemplate',
    tagName: 'div',
    events: {
        'click .save': 'save',
        'change .fieldcount': 'save',
    },
    initialize: function (options)
    {
        this.model.bind('change', this.render, this);
        this.model.bind('savefail', this.saveFail, this);
    },
    save: function (e)
    {
        this.setTitle( " [ Saving...] " );
        var i, output = {};
        var data = this.$('form').serializeArray();
        for (i in data) {
            output[data[i].name] = data[i].value;
        }
        output['fields'] = {};
        for (i = 0; i < output['fieldcount']; i++) {
            output['fields'][i] = {};
            output['fields'][i]['name'] = output['fields'+i+'name'];
            delete output['fields'+i+'name'];
            output['fields'][i]['device'] = output['fields'+i+'device'];
            delete output['fields'+i+'device'];
            output['fields'][i]['field'] = output['fields'+i+'field'];
            delete output['fields'+i+'field'];
        }
        console.log(output);
        this.model.set(output);
        console.log("Model");
        console.log(this.model.toJSON());
        this.model.save();
    },
    saveFail: function (msg)
    {
        this.setTitle();
        alert("Save Failed: " + msg);
    },
    setTitle: function (extra)
    {
        this.$el.dialog( "option", "title", this.title() + extra );
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
        var i;
        if (typeof data.fields !== 'object') {
            data.fields = {};
        }
        for (i = 0; i < data.fieldcount; i++) {
            if (data.fields[i] === undefined) {
                data.fields[i] = { device: 0, field: 0, name: "unnamed" };
            }
        }
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        this.setTitle();
        return this;
    },
    /**
    * Gets infomration about a device.  This is retrieved directly from the device
    *
    * This function is for use of the device list
    *
    * @param id The id of the device to get
    *
    * @return null
    */
    title: function ()
    {
        return _.template(
            $(this.tTemplate).html(),
            this.model.toJSON()
        );
    },
});

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
var TestEntryView = Backbone.View.extend({
    model: Test,
    tagName: 'tr',
    template: '#TestEntryTemplate',
    parent: null,
    events: {
        'click .properties': 'properties',
        'click .run': 'run',
        'click .view': 'view',
    },
    initialize: function (options)
    {
        this.model.bind('change', this.render, this);
        this.model.bind('remove', this.remove, this);
        this.parent = options.parent;
    },
    properties: function (e)
    {
        var view = new TestPropertiesView({ model: this.model });
        this.parent.popup(view);
    },
    run: function (e)
    {
        this.parent.trigger("run", this.model);
    },
    view: function (e)
    {
        this.parent.trigger("view", this.model);
    },
    /**
    * Gets infomration about a device.  This is retrieved directly from the device
    *
    * This function is for use of the device list
    *
    * @param id The id of the device to get
    *
    * @return null
    */
    render: function ()
    {
        var data = this.model.toJSON();
        var created = new Date(data["created"] * 1000);
        data["created"] = this.formatDate(created);
        var modified = new Date(data["modified"] * 1000);
        data["modified"] = this.formatDate(modified);
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        this.$el.trigger('update');
        return this;
    },
    formatDate: function (date)
    {
        return date.toLocaleDateString()+" "+date.toLocaleTimeString();
    }
});

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
window.TestsView = Backbone.View.extend({
    model: Tests,
    template: "#TestListTemplate",
    rows: 0,
    events: {
        'click .new': 'new',
    },
    initialize: function (options)
    {
        this.model = new Tests();
        this.model.bind('add', this.insert, this);
        this.model.bind('savefail', this.saveFail, this);
        this.model.fetch();
    },
    new: function ()
    {
        this.model.new();
    },
    saveFail: function (msg)
    {
        alert("Save Failed: " + msg);
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
        this.$el.html(
            _.template(
                $(this.template).html(),
                this.model.toJSON()
            )
        );
        //this.model.each(this.renderEntry);
        this.$('.tablesorter').tablesorter({ widgets: ['zebra'] });
        this.$el.trigger('update');
        return this;
    },
    insert: function (model, collection, options)
    {
        var view = new TestEntryView({ model: model, parent: this });
        this.$('tbody').append(view.render().el);
        this.$el.trigger('update');
        this.$('.tablesorter').trigger('update');
    },
    popup: function (view)
    {
        this.$el.append(view.render().el);
        view.$el.dialog({
            modal: true,
            draggable: false,
            width: 500,
            resizable: false,
            title: view.title(),
            dialogClass: "window",
        });
        view.model.bind(
            'change',
            function ()
            {
                this.$el.dialog( "option", "title", this.title() );
            },
            view
        );
    },
});
