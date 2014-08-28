<?php
/**
 * Classes for dealing with annotations
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
 * @subpackage Annotations
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\processes\updater\periodic;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');

/**
 * Networking for annotations.
 *
 * This class will do all of the networking for annotations.  It will poll, get configs,
 * update software, and anything else related to talking to annotations.
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Annotations
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class PushAnnotations extends \HUGnet\processes\updater\Periodic
{
    /** This is the maximum number of history records to get */
    const MAX_ANNOTATIONS = 100;
    /** This is the period */
    protected $period = 60;
    /** This is the object we use */
    private $_annotation;
    /** This is our start time */
    private $_start = 0;
    /** This is the url to send data to */
    private $_url = "";
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
        $this->_annotation = $this->system()->annotation();
        $this->_annotation->table()->sqlLimit = self::MAX_ANNOTATIONS;
        $master = $this->system()->get("master");
        $this->_url = $master["url"];
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
        if ($this->ready() && $this->hasMaster()) {
            $this->_pushAnnotations();
        }
    }
    /**
     * This pushes out all of the annotations
     *
     * @return array of devices
     */
    private function _pushAnnotations()
    {
        $res = $this->_annotation->table()->selectInto(
            array(
                "date" => array(
                    '$gt' => $this->_start,
                )
            )
        );
        $count = 0;
        $fail  = 0;
        while ($res) {
            $data = $this->_annotation->table()->toArray();
            // IDs will be different on different servers
            $this->system()->out("Pushing out annotation ".$data["id"], 2);
            unset($data["id"]);
            $ret = \HUGnet\Util::postData(
                $this->_url,
                array(
                    "uuid"   => urlencode($this->system()->get("uuid")),
                    "action" => "put",
                    "task"   => "annotation",
                    "data"   => $data
                ),
                120
            );
            if ($ret) {
                $this->_start = $data["date"];
                $count++;
            } else {
                $fail++;
            }
            $res = $this->_annotation->table()->nextInto();
        }
        $this->system()->out("Pushed out $count annotation(s)");
        if ($fail > 0) {
            $this->system()->out("Failed to push out $fail annotation(s)");
        }
    }

}


?>
