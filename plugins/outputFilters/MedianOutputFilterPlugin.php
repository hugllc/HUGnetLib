<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2011 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/OutputFilterBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class MedianOutputFilterPlugin extends OutputFilterBase
{
    /** @var This is how many samples to iterate over.  This MUST be odd */
    const SAMPLES = 5;
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "5 Sample Median Filter",
        "Type" => "outputFilter",
        "Class" => "MedianOutputFilterPlugin",
        "Flags" => array("median5"),
    );
    /** @var This is where we store our samples */
    protected $samples = array();

    /**
    * Does the actual conversion
    *
    * @param mixed $field The field to execute this on
    *
    * @return bool True on success, false on failure
    */
    public function execute($field)
    {
        $this->_getValues($field);
        foreach (array_keys($this->samples) as $k) {
            $this->data[$k][$field] = $this->_getMedian($k);
        }
        return true;
    }

    /**
    * Does the actual conversion
    *
    * @param int $index The point to get the data from
    *
    * @return null
    */
    private function _getMedian($index)
    {
        $half = (int)(self::SAMPLES / 2);
        if (($index < $half)
            || ($index >= (count($this->samples) - $half))
        ) {
            $ret = $this->samples[$index];
        } else {
            $samples = array_slice($this->samples, $index - $half, self::SAMPLES);
            sort($samples);
            $ret = $samples[$half];
        }
        return $ret;
    }

    /**
    * Does the actual conversion
    *
    * @param mixed $field The field to execute this on
    *
    * @return null
    */
    private function _getValues($field)
    {
        $this->samples = array();
        foreach (array_keys((array)$this->data) as $k) {
            $this->samples[$k] = $this->data[$k][$field];
        }
    }


}
?>
