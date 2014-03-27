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
    /** This is where we cache the point data */
    private $_dataCache = array();
    
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
        } else if ($action === "put") {
            $ret = $this->_put($args);
        } else if ($action === "delete") {
            $ret = $this->_delete($args);
        } else if ($action === "getreading") {
            $ret = $this->_getReading($args);
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
    private function _delete($args)
    {
        if ($this->table()->deleteRow()) {
            $ret = "success";
        } else {
            $ret = "error";
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
            $this->setParam("LastModified", $this->system()->now());
            imagedestroy($img);
            print json_encode((string)((int)$this->table()->updateRow()));
        } else {
            print json_encode("0");
        }
        return null;
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _put($args)
    {
        $data = (array)$args->get("data");
        $id = $args->get("id");
        
        if (is_null($id)) {
            $this->create($data);
            $ret = true;
        } else {
            $params = (array)$data["params"];
            unset($data["params"]);
            $this->table()->clearData();
            $this->table()->fromArray($data);

            foreach ($params as $key => $value) {
                $this->setParam($key, $value);
            }
            $this->setParam("LastModified", $this->system()->now());
            $ret = $this->store(true);

        }
        if ($ret) {
            return $this->toArray(true);
        }
        return -1;
    }
    /**
    * returns a history object for this device
    *
    * @param object $args The argument object
    *
    * @return string
    */
    private function _getReading($args)
    {
        $data = (array)$args->get("data");
        return $this->getReading($data["date"], $data["type"]);
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
    /**
    * Returns data for this image
    *
    * @param mixed  $date The date to get the reading for
    * @param string $type The type of average to get
    *
    * @return array Record of the data for this image
    */
    public function getReading($date = null, $type = null)
    {
        /* Clear the cache */
        $this->_dataCache = array();
        if (empty($date)) {
            $date = time();
        } else {
            $date = \HUGnet\db\Table::unixDate($date);
        }
        if (empty($type)) {
            $type = $this->get("baseavg");
        }
        $ret = array(
            "id"     => $this->id(),
            "type"   => $type,
            "points" => array(),
        );
        $points = (array)json_decode($this->get("points"), true);
        foreach ($points as $key => $point) {
            $ret["points"][$key] = $this->_getPoint(
                $point["devid"], $point["datachan"], $date, $type, $point["units"]
            );
        }
        return $ret;
    }
    /**
    * Returns data for this image
    *
    * @param mixed  $date The date to get the reading for
    * @param string $type The type of average to fix the date for
    *
    * @return int Date in unix format for this average type
    */
    private function _dateDiff($date, $type)
    {
        switch ($type) {
        case "30SEC":
            $ret = $date - 55;
            break;
        case "1MIN":
            $ret = $date - 90;
            break;
        case "5MIN":
            $ret = $date - 550;
            break;
        case "15MIN":
            $ret = $date - 1700;
            break;
        case "HOURLY":
            $ret = $date - 3800;
            break;
        case "DAILY":
            $ret = $date - 87500;
            break;
        case "WEEKLY":
            $ret = $date - (86400 * 7);
            break;
        case "MONTHLY":
            $ret = $date - (86400 * (int)date('t', $date));
            break;
        case "YEARLY":
            $ret = $date - (86400 * 365.242);
            break;
        }
        return (int)$ret;
    }
    /**
    * Returns data for this image
    *
    * @param mixed  $date The date to get the reading for
    * @param string $type The type of average to fix the date for
    *
    * @return string The date in a pretty string
    */
    private function _prettyDate($date, $type)
    {
        if (empty($date)) {
            return "Never";
        }
        switch ($type) {
        case "30SEC":
            $format = "Y-m-d H:i:s";
            break;
        case "1MIN":
        case "5MIN":
        case "15MIN":
            $format = "Y-m-d H:i:00";
            break;
        case "HOURLY":
            $format = "Y-m-d H:00:00";
            break;
        case "DAILY":
            $format = "Y-m-d";
            break;
        case "WEEKLY":
            $format = "Y-m-d";
            break;
        case "MONTHLY":
            $format = "Y-m";
            break;
        case "YEARLY":
            $format = "Y";
            break;
        default:
            $format = "Y-m-d H:i:s";
        }
        return gmdate($format, $date);
    }
    /**
    * Returns data for this image
    *
    * @param string $device   The device to get the reading for
    * @param int    $datachan The data channel on that device
    * @param mixed  &$date    The date to get the reading for
    * @param string $type     The type of average to get
    *
    * @return array Record of the data for this image
    */
    private function _getPoint($device, $datachan, $date, $type, $units = false)
    {
        if (!is_array($this->_dataCache[$date])) {
            $this->_dataCache[$date] = array();
        }
        if (is_string($device)) {
            $device = hexdec($device);
        }
        if (!is_array($this->_dataCache[$date][$device])) {
            $dev = $this->system()->device($device);
            $hist = $dev->historyFactory(
                array(), false
            );
            $res = $hist->getPeriod(
                (int)$this->_dateDiff($date, $type),
                (int)$date,
                $device,
                $type
            );
            $chans = $dev->dataChannels();
            $this->_dataCache[$date][$device] = $hist->toArray(true);
            $chans->convert(
                $this->_dataCache[$date][$device]
            );
            $this->_dataCache[$date][$device]["chans"] = $chans->toArray();
        }
        if (trim(strtolower($datachan)) == "date") {
            $ret = $this->_prettyDate(
                $this->_dataCache[$date][$device]["Date"], $type
            );
        } else if (is_numeric($datachan)) {
            $ret = $this->_dataCache[$date][$device]["Data".$datachan];
        } else {
            $ret = null;
        }
        if (is_null($ret)) {
            $ret = "?";
        }
        if ((bool)$units) {
            $units = $this->_dataCache[$date][$device]["chans"][$datachan]["units"];
            if (!is_null($units)) {
                $ret .= " ".html_entity_decode($units, ENT_COMPAT, 'UTF-8');
            }
        }
        return (string)$ret;
    }
}


?>
