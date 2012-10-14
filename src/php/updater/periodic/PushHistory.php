<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2012 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\updater\periodic;
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
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 */
class PushHistory extends \HUGnet\updater\Periodic
{
    /** This is the period */
    protected $period = 60;
    /** This is the object we use */
    private $_device;
    /** These are the tables we are going to go through */
    private $_tableClasses = array(
        "RawHistoryTable", "E00393700HistoryTable", "ETESTHistoryTable",
    );
    /** These are the tables we are going to go through */
    private $_lastPush = array();
    /** These are the tables we are going to go through */
    private $_tables = array();
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
        $this->_device = $this->system()->device();
        foreach ($this->_tableClasses as $class) {
            $this->_tables[$class] = new $class();
        }
        $this->_lastPush = $this->device()->getParam("LastHistoryPush");
        if (is_string($this->_lastPush)) {
            $this->_lastPush = json_decode($this->_lastPush);
        }
        if (!is_array($this->_lastPush)) {
            $this->_lastPush = array();
        }
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
            foreach ($this->_tableClasses as $class) {
                $this->system()->main();
                if (!$this->ui()->loop()) {
                    break;
                }
                $now  = &$this->_lastPush[$class];
                $hist = &$this->_tables[$cass];
                $ret = $hist->getPeriod($now);
                if ($ret) {
                    $pass  = 0;
                    $fail  = 0;
                    $total = 0;
                    do {
                        $this->system()->main();
                        if (!$this->ui()->loop()) {
                            break;
                        }
                        $ret = $this->_post(
                            null,
                            $hist->sqlTable,
                            $hist->toArray(true)
                        );
                        if ($ret === "success") {
                            $pass++;
                            if ($now < $hist->get("Date")) {
                                $now = $hist->get("Date");
                            }
                        } else {
                            $fail++;
                        }
                        $total++;
                    } while ($hist->nextInto());
                    if ($pass > 0) {
                        $this->ui()->out(
                            "Successfully pushed $pass/$total history records."
                        );
                    }
                    if ($fail > 0) {
                        $this->ui()->out(
                            "Failed to push $fail/$total history records."
                        );
                    }
                }
            }
            $device = $this->device();
            $device->setParam("LastHistoryPush", $this->_lastPush);
            $device->store();
        }
    }
    /**
    * Gets the config and saves it
    *
    * @param string $url The url to post to
    *
    * @return string The left over string
    */
    private function _post($url, $class, $record)
    {
        if (!is_string($url) || (strlen($url) == 0)) {
            $master = $this->system()->get("master");
            $url = $master["url"];
        }

        return \HUGnet\Util::postData(
            $url,
            array(
                "uuid"   => urlencode($this->system()->get("uuid")),
                "action" => "post",
                "task"   => "historyPost",
                "table"  => $class,
                "record" => $record,
            )
        );
    }
}


?>
