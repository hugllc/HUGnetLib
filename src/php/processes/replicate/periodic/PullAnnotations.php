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
 * @subpackage Annotations
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
 * @subpackage Annotations
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.3
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class PullAnnotations extends \HUGnet\processes\replicate\Periodic
{
    /** This is the maximum number of history records to get */
    const MAX_ANNOTATIONS = 50;
    /** This is the period */
    protected $period = 60;
    /** This is the object we use */
    private $_annotation;
    /** This is the url to get stuff from */
    private $_url;
    /** This is the last item we got */
    private $_last = 1;
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
        $this->_annotation = $this->system()->Annotation();
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
            $this->_pullAnnotations();
        }
    }
    /**
     * This pulles out all of the sensors for a device
     *
     * @return array of devices
     */
    private function _pullAnnotations()
    {
        do {
            $ret = \HUGnet\Util::postData(
                $this->_url,
                array(
                    "uuid"   => urlencode($this->system()->get("uuid")),
                    "action" => "repl",
                    "task"   => "annotation",
                    "data"   => array(
                        "start" => $this->_last,
                        "limit" => self::MAX_ANNOTATIONS,
                    )
                ),
                120
            );
            if (count($ret) > 0) {
                $this->system()->out(
                    "Pulled ".count($ret)." annotations since ".$this->_last
                );
                foreach ($ret as $anno) {
                    // Insert any unknown devices
                    if (!$this->_annotation->load($anno["id"])) {
                        $this->_annotation->table()->clearData();
                        $this->_annotation->table()->fromArray($anno);
                        $this->_annotation->table()->insertRow(true);
                    }
                    $this->_last = $anno["date"];
                }
                $this->_last++;
            }
        } while ((count($ret) > 0) && $this->ui()->loop());
    }
}


?>
