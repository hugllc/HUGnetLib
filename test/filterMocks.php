<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2010 Hunt Utilities Group, LLC
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
 * @category   Filters
 * @package    HUGnetLibTest
 * @subpackage Filters
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

require_once dirname(__FILE__).'/../filter.php';
require_once dirname(__FILE__).'/../lib/plugins.inc.php';

// Make sure this file isn't counted for code coverage
PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 *  This is a test filter.  It is not used for anything else.
 *
 * @category   Filters
 * @package    HUGnetLibTest
 * @subpackage Filters
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class TestFilter extends FilterBase
{
    var $filters = array(
        "testType" => array(
            "testFilter1" => array(
                "longName" => "Generic Test Sensor 1",
                "function" => "test1",
                "extraText" => "extraTest",
                "extraDefault" => "extraDefaultTest",
           ),
            "testFilter2" => array(
                "longName" => "Generic Test Sensor 2",
                "function" => "test2",
           ),
       ),
    );

    /**
     * Some Function
     *
     * @param array &$history The history to filter
     * @param int   $index    The index in the history to use
     * @param array $filter   Information on the filter we are implementing
     * @param mixed $extra    Extra setup information on the filter
     * @param int   $deltaT   The difference in time between this record and the last
     *
     * @return null
     */
    public function test1($history, $index, $filter, $extra, $deltaT = null)
    {
        $history = array_reverse($history);
        // This must stay the same.
        return $history;
    }
    /**
     * Some Function
     *
     * @param array &$history The history to filter
     * @param int   $index    The index in the history to use
     * @param array $filter   Information on the filter we are implementing
     * @param mixed $extra    Extra setup information on the filter
     * @param int   $deltaT   The difference in time between this record and the last
     *
     * @return null
     */
    public function test2($history, $index, $filter, $extra, $deltaT = null)
    {
    }
}
/**
 * This class is to test how things handle not having a filters variable;
 *
 * @category   Filters
 * @package    HUGnetLibTest
 * @subpackage Filters
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class TestFilterNoFilters extends FilterBase
{
    /**
     * constructor
     */
    function __construct()
    {
        // Make absolutely sure that there are no filters
        unset($this->filters);
    }
}

?>
