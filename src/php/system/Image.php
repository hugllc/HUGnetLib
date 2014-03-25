<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
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
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is our base class */
require_once dirname(__FILE__)."/../base/SystemTableBase.php";
/** This is our system interface */
require_once dirname(__FILE__)."/../interfaces/SystemInterface.php";
/** This is our system interface */
require_once dirname(__FILE__)."/../interfaces/WebAPI.php";

/**
 * Base system class.
 *
 * This class is the new API into HUGnetLib.  It controls the config and gives out
 * objects for everything else.  This is the only file that should be
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage System
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class Image extends \HUGnet\base\SystemTableBase
    implements \HUGnet\interfaces\WebAPI, \HUGnet\interfaces\SystemInterface
{
    /**
    * This function creates the system.
    *
    * @param mixed  &$system (object)The system object to use
    * @param mixed  $data    (int)The id of the item, (array) data info array
    * @param string $table   The table to use
    *
    * @return null
    */
    public static function &factory(&$system, $data=null, $table="Images")
    {
        $object = parent::factory($system, $data, $table);
        return $object;
    }
    /**
    * returns a history object for this device
    *
    * @param object $args  The argument object
    * @param array  $extra Extra data from the
    *
    * @return string
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    public function webAPI($args, $extra)
    {
        $action = trim(strtolower($args->get("action")));
        $ret = null;
        if ($action === "list") {
            $ret = $this->_list($args);
        } else if ($action === "insert") {
            $ret = $this->_insert($args);
        }
        return $ret;
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _list($args)
    {
        $data = $args->get("data");
        if (isset($data["limit"]) && is_numeric(trim($data["limit"]))) {
            $this->table()->sqlLimit = (int)trim($data["limit"]);
            unset($data["limit"]);
        }
        if (isset($data["start"]) && is_numeric(trim($data["start"]))) {
            $this->table()->sqlStart = (int)trim($data["start"]);
            unset($data["start"]);
        }
        $ret = $this->getList($data, false);
        return $ret;
    }
    /**
    * Insert a background image
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _insert($args)
    {
        header('Content-type: text/plain; charset=UTF-8');
        if (file_exists($_FILES["import"]["tmp_name"])) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimetype = $finfo->file($_FILES['import']['tmp_name']);
            if (false !== $ext = array_search(
                $mimetype,
                array(
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                ),
                true
            )) {
                $data = file_get_contents($_FILES["import"]["tmp_name"]);
            }
        } else {
            $data = $args->get("data");
        }
        if (is_string($data) && (strlen($data) > 0)) {
            $this->load($args->get("id"));
            $this->table()->set("image", base64_encode($data));
            $this->table()->set("imagetype", $mimetype);
            $img = imagecreatefromstring($data);
            $this->table()->set("height", imagesy($img));
            $this->table()->set("width", imagesx($img));
            imagedestroy($img);
            print json_encode((string)((int)$this->table()->updateRow()));
        } else {
            print json_encode("0");
        }
        return null;
    }
    /**
    * Returns the table as an array
    *
    * @param bool $default Whether or not to include the default values
    *
    * @return array
    */
    public function toArray($default = false)
    {
        $return = $this->table()->toArray($default);
        if ($default) {
            if ($return["baseavg"] == "15MIN") {
                $avg = $this->system()->table("E00391200Average");
            } else {
                $avg = $this->system()->table("E00393700Average");
            }
            $return["averageTypes"] = (array)$avg->averageTypes();
        }
        return $return;
    }
    /**
    * Gets one of the parameters
    *
    * @param string $field The field to get
    *
    * @return The value of the field
    */
    public function &getParam($field)
    {
        $params = $this->table()->get("params");
        $array = json_decode($params, true);
        return $array[$field];
    }
    /**
    * Sets one of the parameters
    *
    * @param string $field The field to set
    * @param mixed  $value The value to set the field to
    *
    * @return null
    */
    public function &setParam($field, $value)
    {
        /* This makes sure the field is always in json format */
        $this->getParam($field);
        /* get the fields */
        $params = $this->table()->get("params");
        $params = json_decode($params, true);
        if (is_null($value)) {
            unset($params[$field]);
        } else {
            $params[$field] = $value;
        }
        return $this->table()->set("params", json_encode($params));
    }
}


?>
