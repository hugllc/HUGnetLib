<?php
/**
 * Median Filter
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package HUGnetLib
 * @subpackage Filters
 * @copyright 2007 Hunt Utilities Group, LLC
 * @author Scott Price <prices@hugllc.com>
 * @version SVN: $Id: unitConversion.inc.php 369 2007-10-12 15:05:32Z prices $    
 *
 */

if (!class_exists('medianFilter') && class_exists("filter_base")) {
    
    class MedianFilter extends filter_base
    {
        var $filters = array(
            "numeric" => array(
                "medianFilter" => array(
                    "longName" => "Median Filter",
                    "extraDefault" => array(3,1),
                    "extraText" => array("Rows", "Active Row"),
                    "function" => "median",
                ),
            ),
        );
        
        /**
         * This function implements a n pole median filter
         *
         * @param array &$history The history to filter
         * @param int   $index    The index in the history to use
         * @param array $filter   Information on the filter we are implementing
         * @param mixed $extra    Extra setup information on the filter
         * @param int   $deltaT   The difference in time between this record and the last one
         */
        public function median(&$history, $index, $filter, $extra, $deltaT=null) 
        {
            $poles  = is_int($extra[0]) ? $extra[0] : $filter["extraDefault"][0];
            $active = is_int($extra[1]) ? $extra[1] : $filter["extraDefault"][1];
            $keys   = array_keys($history);
            $lastKey = (count($keys) - $active);
            for ($key = $active; $key < $lastKey; $key++) {
                $mArray = array();
                $start = $key - $active;
                $end   = $start + $poles;
                for($i = $start; $i < $end; $i++) {
                    $mArray[] = $history[$keys[$i]]["Data".$index];
                }
                $history[$keys[$key]]["Data".$index] = $this->_getMedian($mArray, $active);
                $history[$keys[$key]]["data"][$index] = $history[$keys[$key]]["Data".$index]; 
            }
        }
        
        /**
         * This function returns the median value in the array $val
         *
         * @param array $val    An array of values to filter
         * @param int   $active The active element.
         *
         * @return mixed Returns the median value in the array $val         
         */
        private function _getMedian($val, $active = null) 
        {
            if (!is_int($active)) $active = ((int)(count($val) / 2)) + 1; 
            sort($val);
            return $val[$active];        
        }
    }
}
if (method_exists($this, "addGeneric")) {
    $this->addGeneric(array("Name" => "medianFilter", "Type" => "filter", "Class" => "MedianFilter"));
}
?>
