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
 * @subpackage ImageConfigs
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
* @subpackage ImageConfigs
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
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
        'click tr.datapoint': '_rowclick',
    },
    initialize: function (options)
    {
        _.bindAll(this, "saveFail", "saveclose");
        if (options) {
            if (options.url) this.url = options.url;
        }
        this.model.on('change', this.render, this);
        this.image = new HUGnet.ImageSVGView({
            model: this.model,
            style: "border: thin solid black;",
            draggable: true,
            id: "cfgimg-"+this.model.get("name"),
            sync: false,

        });
        this.image.on("dragend", this._dragmove, this);
        this._template = _.template($(this.template).html());
        this._tTemplate = _.template($(this.tTemplate).html());
    }, 
    _dragmove: function (point, delta, e)
    {
        if ((delta.x != 0) && (delta.y != 0)) {
            var pt = this.model.points.get(point.id);
            var x  = parseInt(pt.get("x"), 10) + parseInt(delta.x, 10);
            var y  = parseInt(pt.get("y"), 10) + parseInt(delta.y, 10);
            pt.set("x", x);
            pt.set("y", y);
            this.model.flushpoints();
        }
        this._hilight(point.id);
    },
    _rowclick: function (e)
    {
        var index = $(e.target).closest("tr").attr("rowindex");
        this._hilight(index);
    },
    _hilight: function (index)
    {
        var row = $('[rowindex="'+index+'"]');
        row.siblings().removeClass("datapointhilight");
        row.addClass("datapointhilight");
        this.image.hilight(index);
    },
    save: function (e)
    {
        this.setTitle( " [ Saving...] " );
        var i, output = {};
        var data = this.$('#imageForm').serializeArray();
        for (i in data) {
            output[data[i].name] = data[i].value;
        }
        var points = [];
        var self = this;
        var index = 0;
        $(".datapoint").each(function(ind, element) {
            // This checks that we only include this one once.
            var check = $(this).attr("rowindex");
            if (!$(this).find('[name="delete"]').prop("checked") && (check >= ind)) {
                if (!points[index]) {
                    points[index] = {};
                }
                points[index].id = index;
                var row = this;
                _.each(["pretext", "posttext", "fontsize", "x", "y", "color", "color1", "background", "devid", "datachan", "backgroundmax", "valmin", "valmax"],
                    function(sel, i) {
                        points[index][sel] = $(row).find('[name="'+sel+'"]').val();
                    }
                );
                if ($(this).find('[name="units"]').prop("checked")) {
                    points[index].units = 1;
                } else {
                    points[index].units = 0;
                }
                index++;
            }
        });
        output.points = points;
        this.model.save(output, { error: this.saveFail, wait: true });
        this.setTitle();
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
            // Remove all of the datapoints.
            $(".datapoint").remove();
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
        // Remove all data points
        $(".datapoint").remove();
        var self = this;
        var id   = 'svgcfgimg'+this.model.get("id");
        var data = this.model.toJSON();
        data.svg = '<div id="'+id+'">/div>';
        _.extend(data, HUGnet.viewHelpers);
        this.$el.html(this._template(data));
        this.iframe = $('<iframe>', { name: 'insertImageFrame', id: 'insertImageFrame', content: "text/plain;charset=UTF-8" }).hide();
        this.$el.append(this.iframe);
        this.$("div#"+id).html("");
        this.$("div#"+id).append(this.image.render().el);
        this.model.off('saved', this.render, this);
        // This fixes the backgrounds that don't work otherwise...
        setTimeout(
            function () {
                self.image.update();
            },
            250
        );
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
        return this._tTemplate(this.model.toJSON());
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
        var url = this.model.inserturl();
        var form = $("#insertImage");
        form.attr({
            action: url,
            method: 'POST',
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
            self.model.fetch();
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
                dialogClass: "window",
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
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
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
        this.model.bind('remove', this.remove, this);
        this.parent = options.parent;
        this._template = _.template($(this.template).html());
    },
    action: function (e)
    {
        var action = this.$('.action').val();
        this.$('.action').val('option:first');
        //this.$('.action')[0].selectedIndex = 0;
        if (action === 'properties') {
            this.properties(e);
        } else if (action === 'delete') {
            this.delImage(e);
        }
    },
    properties: function (e)
    {
        this.model.fetch();
        var view = new ImageConfigPropertiesView({ model: this.model, url: this.url });
        this.parent.popup(view);
    },
    delImage: function (e)
    {
        var name = this.model.get("name");
        var id   = this.model.get("id");
        var self = this;
        $('<div></div>').appendTo('body')
        .html('Delete Image #'+id+' "'+name+'"?')
        .dialog({
            resizable: false,
            height:140,
            modal: true,
            buttons: {
                "Delete": function() {
                    self.model.removeImg();
                    $(this).dialog("close");
                    $(this).remove();
                },
                "Cancel": function() {
                    $(this).dialog("close");
                    $(this).remove();
                }
            }
        });    
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
        var data = this.model.toJSON();
        this.$el.html(this._template(data));
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
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.3
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.ImageConfigView = Backbone.View.extend({
    template: "#ImageConfigListTemplate",
    url: '/HUGnetLib/HUGnetLibAPI.php',
    events: {
        'click .new': 'create'
    },
    views: {},
    initialize: function (options)
    {
        if (options) {
            if (options.url) this.url = options.url;
        }
        this.model.each(this.insert, this);
        this.model.bind('add', this.insert, this);
        this.model.bind('remove', this.delImage, this);
        this._template = _.template($(this.template).html());
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
        
        this.$el.html(this._template(data));
        //this.model.each(this.renderEntry);
        this.$("table").tablesorter({ widgets: ['zebra'] });
        this.$("table").trigger('update');
        return this;
    },
    insert: function (model, collection, options)
    {
        var id = model.get("id");
        this.views[id] = new ImageConfigEntryView({ model: model, parent: this, url: this.url });
        this.$('table:first').children('tbody').append(this.views[id].render().el);
        this.$("table").trigger('update');
    },
    delImage: function (model, collection, options)
    {
        var id = model.get("id");
        this.views[id].remove();
    },
    create: function ()
    {
        this.model.create({"name": "New Image"});
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
    },
});
