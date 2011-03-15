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
class MeanDifferenceOutputFilterPlugin extends OutputFilterBase
{
    /** @var This is how many samples to iterate over */
    const SAMPLES = 5;
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "5 Sample Mean Difference",
        "Type" => "outputFilter",
        "Class" => "MeanDifferenceOutputFilterPlugin",
        "Flags" => array("meandifference"),
    );
    /** @var This is where we store our samples */
    protected $samples = array();
    /** @var This is where we are currently in the array */
    protected $index = 0;
    
    /**
    * Does the actual conversion
    *
    * @param mixed $field The field to execute this on
    *
    * @return bool True on success, false on failure
    */
    public function execute($field)
    {
        $this->index = 0;
        $this->samples = array();
        $end = count($this->data);
        while ($this->index < $end) {
            $this->_getValues($field);
            $meanDiff = $this->_meanDiff();
            if ($meanDiff === false) {
                break;
            }
            $mean = array_sum($this->samples) / count($this->samples);
            $diff = abs($this->data[$this->index][$field] - $mean);
            if ($diff > ($meanDiff * 2)) {
                $this->data[$this->index][$field] = null;
            }
            $this->index++;
        }
        return true;
    }

    /**
    * Does the actual conversion
    *
    * @return float The average difference from the mean to the value
    */
    private function _meanDiff()
    {
        $samples = count($this->samples);
        if (empty($samples)) {
            return false;
        }
        $mean = array_sum($this->samples) / $samples;
        $diffs = array();
        foreach ($this->samples as $key => $val) {
            $diffs[$key] = abs($val - $mean);
        }
        $diff = array_sum($diffs) / $samples;
        if (empty($diff)) {
            return $mean * 0.1;
        }
        return $diff;
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
        $diff = ((self::SAMPLES - 1) / 2);
        $current = $this->index - ((self::SAMPLES - 1) / 2);
        $end = count($this->data);
        $atEnd = ($current + self::SAMPLES) - $end;
        if ($atEnd >= 0) {
            $current -= $atEnd;
        }
        $count = 0;
        $this->samples = array();
        while (($count < self::SAMPLES) && ($current < $end)) {
            if (is_numeric($this->data[$current][$field])) {
                $this->samples[$current] = $this->data[$current][$field];
                $count++;
            }
            $current++;
        }
    }

    
}
?>
