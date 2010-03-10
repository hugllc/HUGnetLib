<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2009 Hunt Utilities Group, LLC
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
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */

require_once dirname(__FILE__).'/../../hugnet.inc.php';

require_once 'AnalysisTest.php';
require_once 'AverageTest.php';
require_once 'DeviceTest.php';
require_once 'FirmwareTest.php';
require_once 'GatewayTest.php';
require_once 'HistoryTest.php';
require_once 'PlogTest.php';
require_once 'ProcessTest.php';
require_once 'ProcStatsTest.php';
require_once 'RawHistoryTest.php';
require_once 'VirtualHistoryTest.php';

/**
 *  This class runs all of the tests.  This must be done with no errors
 * before the software is ever released.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class HUGnetLibDatabaseTests
{
    /**
     * test suite
     *
     * @return null
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('HUGnetLibDatabase');
        $suite->addTestSuite('AnalysisTest');
        $suite->addTestSuite('DeviceTest');
        $suite->addTestSuite('FirmwareTest');
        $suite->addTestSuite('GatewayTest');
        $suite->addTestSuite('HistoryTest');
        $suite->addTestSuite('AverageTest');
        $suite->addTestSuite('PlogTest');
        $suite->addTestSuite('ProcessTest');
        $suite->addTestSuite('ProcStatsTest');
        $suite->addTestSuite('RawHistoryTest');
        $suite->addTestSuite('VirtualHistoryTest');

        return $suite;
    }
}

?>
