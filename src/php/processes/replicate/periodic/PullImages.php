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
 * @subpackage Images
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\processes\replicate\periodic;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * Networking for devices.
 *
 * This class will do all of the networking for devices.  It will poll, get configs,
 * update software, and anything else related to talking to devices.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Images
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class PullImages extends \HUGnet\processes\replicate\Periodic
{
    /** This is the maximum number of history records to get */
    const MAX_DEVICES = 20;
    /** This is the period */
    protected $period = 3600;
    /** This is the object we use */
    private $_image;
    /** This is the url to get stuff from */
    private $_url;
    /**
    * This function sets up the driver object, and the database object.  The
    * database object is taken from the driver object.
    *
    * @param object &$gui The user interface to use
    *
    * @return null
    */
    protected function __construct(&$gui)
    {
        parent::__construct($gui);
        $this->_image = $this->system()->image();
    }
    /**
    * This function creates the system.
    *
    * @param object &$gui the user interface object
    *
    * @return null
    */
    public static function &factory(&$gui)
    {
        return parent::intFactory($gui);
    }
    /**
    * This function creates the system.
    *
    * @return null
    */
    public function &execute()
    {
        $this->_url = $this->ui()->get("url");
        if ($this->ready() && !empty($this->_url)) {
            $this->_pullImages();
            $now = $this->system()->now();
            $ids = $this->_image->ids();
            foreach (array_keys($ids) as $key) {
                $this->system()->out("Working on Image $key", 2);
                $this->system()->main();
                if (!$this->ui()->loop()) {
                    break;
                }
                $this->_image->load($key);
                $this->_pullImage($this->_image, $now);
            }
            $this->last = $now;
        }
    }
    /**
     * This pulles out all of the sensors for a device
     *
     * @param int &$img The device to use
     * @param int $now  The time to use
     *
     * @return none
     */
    private function _pullImage(&$img, $now)
    {
        $this->system()->out(
            "Pulling Image ".$img->id()." from master server..."
        );
        $ret = $this->_pull();
        if ($ret) {
            $this->system()->out(
                "Successfully pulled Image ".$img->id()."."
            );
            $img->load($img->id());
            $img->setParam("LastMasterPull", $now);
            $img->store();
        } else {
            $this->system()->out("Failure.");
            /* Don't store it if we fail */
        }
    }
    /**
     * This pulles out all of the sensors for a device
     *
     * @return array of devices
     */
    private function _pullImages()
    {
        $start = 0;
        do {
            $ret = \HUGnet\Util::postData(
                $this->_url,
                array(
                    "uuid"   => urlencode($this->system()->get("uuid")),
                    "action" => "list",
                    "task"   => "image",
                    "data"   => array(
                        "limit" => self::MAX_DEVICES,
                        "start" => $start,
                    ),
                ),
                120
            );
            if (!is_array($ret) || !$this->ui()->loop()) {
                break;
            }
            $this->system()->out(
                "Checking images $start to ".($start + count($ret))
            );
            foreach ($ret as $img) {
                // Insert any unknown devices
                if (!$this->_image->load($img["id"])) {
                    $this->_image->table()->clearData();
                    $this->_image->table()->fromArray($img);
                    $this->_image->table()->insertRow(true);
                }
            }
            $start += self::MAX_DEVICES;
        } while (count($ret) == self::MAX_DEVICES);
    }
    /**
    * Pulls the record from the given URL
    *
    * @return string The left over string
    */
    private function _pull()
    {
        $args = array(
            "uuid"    => urlencode($this->system()->get("uuid")),
            "action"  => "get",
            "task"    => "image",
            "id"      => $this->_image->get("id"),
        );
        $ret = \HUGnet\Util::postData($this->_url, $args);
        $this->_image->table()->clearData();
        $this->_image->table()->fromAny($ret);
        return $this->_image->store();
        
    }
}


?>
