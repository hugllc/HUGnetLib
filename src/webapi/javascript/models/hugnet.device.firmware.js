/**
 * hugnet.device.firmware.js
 *
 * @category   JavaScript
 * @package    HUGnetLib
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2015 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/**
* This is the model that stores the device firmware.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage Devices
* @author     Scott Price <prices@hugllc.com>
* @copyright  2015 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.8
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
* @since      0.14.5
*/
HUGnet.Firmware = Backbone.Model.extend({
    idAttribute: 'id',
    defaults:
    {
        id: null,
        Version: "",
        Code: "",
        CodeHash: "",
        Data: "",
        DataHash: "",
        FWPartNum: "",
        HWPartNum: "",
        Date: "",
        FileType: "",
        RelStatus: "",
        Tag: "",
        Target: "",
        Active: "",
        md5: ""
    },
    /**
    * This function initializes the object
    */
    initialize: function(attrib)
    {
    },
    /**
    * This function initializes the object
    */
    fix: function(attributes)
    {
    },
});

/**
* This is the model that stores the devices.
*
* @category   JavaScript
* @package    HUGnetLib
* @subpackage Devices
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    Release: 0.14.8
* @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
*/
HUGnet.Firmwares = Backbone.Collection.extend({
    urlPart: '/firmware',
    baseurl: '',
    model: HUGnet.Firmware,
    device: null,
    initialize: function (options)
    {
        if (options) {
            if (options.baseurl) this.baseurl = options.baseurl;
            if (options.device) this.device = options.device;
        }
    },
    url: function ()
    {
        return this.baseurl + this.urlPart;
    },
    comparator: function (model)
    {
        return parseInt(model.get("id"), 10);
    },
    _fixHWPartNum: function (HWPartNum)
    {
        var substr = "";
        if (_.isString(HWPartNum)) {
            substr = HWPartNum.substr(0, 7);
            if ((substr == "0039-21") && (HWPartNum.length() >= 10)) {
                substr = HWPartNum.substr(0, 10);
            }
        }
        return substr;

    },
    isLatest: function ()
    {
        if (_.isObject(this.device)) {
            
        }
        return false;
    },
});
