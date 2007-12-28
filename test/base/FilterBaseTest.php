<?php
/**
 * This runs all of the tests associated with HUGnetLib.
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
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Filters
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
// Call FilterBaseTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'FilterBaseTest::main');
}
/** Test framework */
require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../base/FilterBase.php';

/**
 * Test class for FilterBase.
 * Generated by PHPUnit on 2007-12-13 at 09:08:00.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Filters
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class FilterBaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    FilterBase
     * @access protected
     */
    protected $o;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     *
     * @access public
     * @static
     */
    public static function main()
    {
        include_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('FilterBaseTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->o = new FilterBase;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     *
     * @access protected
     */
    protected function tearDown()
    {
        unset($this->o);
    }

    /**
     * Test the filters array to make sure it is blank and an array
     *
     * @return void
     */
    public function testFilterssArray() 
    {
        $this->assertSame(array(), $this->o->filters);
    }


}

// Call FilterBaseTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'FilterBaseTest::main') {
    FilterBaseTest::main();
}
?>
