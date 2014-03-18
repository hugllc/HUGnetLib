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
 * @subpackage ImageConfigs
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
* @subpackage ImageConfigs
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var ImageConfigPropertiesView = Backbone.View.extend({
    url: '/HUGnetLib/HUGnetLibAPI.php',
    template: '#ImageConfigPropertiesTemplate',
    tTemplate: '#ImageConfigPropertiesTitleTemplate',
    tagName: 'div',
    _close: false,
    progress: undefined,
    iframe: undefined,
    events: {
        'click .SaveImageConfig': 'saveclose',
        'click .ApplyImageConfig': 'save',
        'change select.type': 'save',
        'click .insertImage': '_insertImage',
        'click .insertPoint': '_insertPoint',
    },
    initialize: function (options)
    {
        if (options) {
            if (options.url) this.url = options.url;
        }
        this.model.refresh();
        this.model.on('change', this.render, this);
        this.model.on('savefail', this.saveFail, this);
        this.image = new HUGnet.ImageSVGView({
            model: this.model,
            style: "border: thin solid black;",
        });
    },
    save: function (e)
    {
        this.setTitle( " [ Saving...] " );
        var i, output = {};
        var data = this.$('#imageForm').serializeArray();
        for (i in data) {
            output[data[i].name] = data[i].value;
        }
        console.log(output);
        var points = [];
        var self = this;
        var index = 0;
        $(".datapoint").each(function(ind, element) {
            if (!$(this).find('[name="delete"]').prop("checked")) {
                if (!points[index]) {
                    points[index] = {};
                }
                var row = this;
                _.each(["pretext", "posttext", "fontsize", "x", "y", "color", "background", "devid", "datachan"],
                    function(sel, i) {
                        points[index][sel] = $(row).find('[name="'+sel+'"]').val();
                    }
                );
                index++;
            }
        });
        
        this.model.set(output);
        this.model.points.reset(points);
        this.model.save();
    },
    saveclose: function (e)
    {
        this._close = true;
        this.save(e);
        this.close();
    },
    saveFail: function ()
    {
        this.setTitle();
        //alert("Save Failed");
    },
    close: function ()
    {
        if (this._close) {
            this.model.off('change', this.render, this);
            this.model.off('savefail', this.saveFail, this);
            this.model.off('saved', this.close, this);
            this.model.off('saved', this.render, this);
            this.image.remove();
            this.remove();
        }
    },
    setTitle: function (extra)
    {
        if (this.$el.is(':data(dialog)')) {
            this.$el.dialog( "option", "title", this.title() + extra );
        }
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
        data.svg = this.image.render().$el.html();
        _.extend(data, HUGnet.viewHelpers);
        this.$el.html(
            _.template(
                $(this.template).html(),
                data
            )
        );
        this.iframe = $('<iframe>', { name: 'insertImageFrame', id: 'insertImageFrame', content: "text/plain;charset=UTF-8" }).hide();
        this.$el.append(this.iframe);
        this.model.off('saved', this.render, this);
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
    _insertPoint: function ()
    {
        this.model.newpoint();
    },
    _insertImage: function ()
    {
        if (this.$("#insertImage input[type=file]").val() == "") {
            return;
        }
        var id = parseInt(this.model.get("id"), 10);
        var url = this.url+"?task=image&action=insert&id="+id;
        var form = $("#insertImage");
        form.attr({
            action: url,
            method: 'post',
            enctype: 'multipart/form-data',
            encoding: 'multipart/form-data',
            target: "insertImageFrame"
        });
        form.submit();
        this._insertProgress("Inserting the image");
        this._insertWait(this);
    },
    _insertWait: function (self)
    {
        var text = self.iframe.contents().text();
        if (text != "") {
            self._teardownProgress();
            self.timer = null;
            self.$("#insertImage input[type=file]").val("");
            var id = parseInt(text, 16);
            self.model.refresh();
        } else {
            self.timer = setTimeout(
                function () {
                    self._insertWait(self);
                },
                500
            );
        }
    },
    _insertProgress: function(title)
    {
        if (typeof this.progress !== "object") {
            this.progress = new HUGnet.Progress({
                modal: false,
                draggable: true,
                width: 300,
                title: title,
                dialogClass: "window no-close",
                zIndex: 500,
            });
            this.progress.update(false);
        }
    },
    _teardownProgress: function()
    {
        if (this.progress !== undefined) {
            this.progress.update(1);
            this.progress.remove();
            delete this.progress;
        }
    },
});

/**
* This is our entry view for the images
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage ImageConfigs
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
var ImageConfigEntryView = Backbone.View.extend({
    url: '/HUGnetLib/HUGnetLibAPI.php',
    tagName: 'tr',
    template: '#ImageConfigEntryTemplate',
    parent: null,
    events: {
        'change .action': 'action',
        //'click .properties': 'properties'
    },
    initialize: function (options)
    {
        if (options) {
            if (options.url) this.url = options.url;
        }
        this.model.bind('change', this.render, this);
        this.model.bind('sync', this.render, this);
        this.model.bind('remove', this.remove, this);
        this.model.bind('configfail', this.refreshFail, this);
        this.parent = options.parent;
    },
    action: function (e)
    {
        var action = this.$('.action').val();
        this.$('.action').val('option:first');
        //this.$('.action')[0].selectedIndex = 0;
        if (action === 'properties') {
            this.properties(e);
        }
    },
    properties: function (e)
    {
        var view = new ImageConfigPropertiesView({ model: this.model, url: this.url });
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
        this.$el.removeClass("working");
        this.$el.html(
            _.template(
                $(this.template).html(),
                this.model.toJSON()
            )
        );
        this.$el.trigger('update');
        return this;
    }
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage ImageConfigs
* @author     Scott Price <prices@hugllc.com>
* @copyright  2012 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.9.7
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.ImageConfigView = Backbone.View.extend({
    template: "#ImageConfigListTemplate",
    url: '/HUGnetLib/HUGnetLibAPI.php',
    events: {
        'click .new': 'create'
    },
    initialize: function (options)
    {
        if (options) {
            if (options.url) this.url = options.url;
        }
        //this.model.each(this.insert, this);
        this.model.bind('add', this.insert, this);
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
        this.$("table").tablesorter({ widgets: ['zebra'] });
        this.$("table").trigger('update');
        return this;
    },
    insert: function (model, collection, options)
    {
        var view = new ImageConfigEntryView({ model: model, parent: this, url: this.url });
        this.$('tbody').append(view.render().el);
        this.$("table").trigger('update');
    },
    create: function ()
    {
        var self = this;
        var ret = $.ajax({
            type: 'GET',
            url: this.url,
            dataType: 'json',
            cache: false,
            data:
            {
                "task": "image",
                "action": "put",
                "data": {
                    "name": 'New Image',
                }
            }
        }).done(
            function (data)
            {
                if (_.isObject(data)) {
                    self.trigger('created');
                    self.model.add(data);
                } else {
                    self.trigger('newfail');
                }
            }
        ).fail(
            function ()
            {
                self.trigger('newfail');
            }
        );
    },
    popup: function (view)
    {
        this.$el.append(view.render().el);
        view.$el.dialog({
            modal: true,
            draggable: true,
            width: 800,
            resizable: true,
            title: view.title(),
            dialogClass: "window",
            zIndex: 500
        });
        view.model.bind(
            'change',
            function ()
            {
                this.$el.dialog( "option", "title", this.title() );
            },
            view
        );
    }
});
