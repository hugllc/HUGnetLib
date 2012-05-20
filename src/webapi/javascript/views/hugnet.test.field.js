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
 * @subpackage TestFields
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
* @subpackage TestFields
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var TestFieldPropertiesView = Backbone.View.extend({
    template: '#TestFieldPropertiesTemplate',
    tTemplate: '#TestFieldPropertiesTitleTemplate',
    tagName: 'div',
    events: {
        'click .save': 'save',
    },
    initialize: function (options)
    {
        this.model.bind('change', this.render, this);
    },
    saveclose: function (e)
    {
        this._close = true;
        this.save(e);
    },
    save: function (e)
    {
        this.setTitle( " [ Saving...] " );
        var i, output = {};
        var data = this.$('form').serializeArray();
        for (i in data) {
            output[data[i].name] = data[i].value;
        }
        this.model.set(output);
        this.remove();
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
* @subpackage TestFields
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var TestFieldEntryView = Backbone.View.extend({
    model: TestField,
    tagName: 'tr',
    template: '#TestFieldEntryTemplate',
    parent: null,
    events: {
        'click .properties': 'properties',
    },
    initialize: function (options)
    {
        this.model.bind('change', this.render, this);
        this.model.bind('remove', this.remove, this);
        this.parent = options.parent;
    },
    properties: function (e)
    {
        var view = new TestFieldPropertiesView({ model: this.model });
        this.parent.popup(view);
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
        this.$el.html(
            _.template(
                $(this.template).html(),
                this.model.toJSON()
            )
        );
        return this;
    },
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage TestFields
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
window.TestFieldsView = Backbone.View.extend({
    model: TestFields,
    template: "#TestFieldListTemplate",
    rows: 0,
    events: {
    },
    initialize: function (options)
    {
        //this.model.bind('add', this.insert, this);
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
        /* insert all of the models */
        this.model.each(this.insert, this);
        this.$("tr").removeClass("odd").removeClass("even");
        this.$("tr:odd").addClass("odd");
        this.$("tr:even").addClass("even");
        return this;
    },
    insert: function (model, key)
    {
        var view = new TestFieldEntryView({ model: model, parent: this });
        this.$('tbody').append(view.render().el);
    },
    popup: function (view)
    {
        this.$el.append(view.render().el);
        view.$el.dialog({
            modal: true,
            draggable: false,
            width: 300,
            resizable: false,
            title: view.title(),
            dialogClass: "window",
            zIndex: 1000,
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
