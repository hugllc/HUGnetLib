<?php
/**
 *   This is the basis for all filter test classes.
 *
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Test
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$
 *
 */

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once dirname(__FILE__).'/../../filter.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 09:09:44.
 */
class filterTestBase extends PHPUnit_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("filterTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    }
    /**
     *
     */
    public static function filterArrayDataSource($class) {
        $o = new $class();
        $return = array();
        foreach($o->filters as $catName => $cat) {
            foreach($cat as $shortName => $sensor) {
                $return[] = array($catName, $shortName, $sensor);
            }
        }
        return $return;
    }

    /**
     * Data provider stub for testFilterVariable
     *
     *  This should be redifined in subclasses.
     */
    public static function dataFilterVariable() {
        return array();
    }
    /**
     * @dataProvider dataFilterVariable
     */
    public function testFilterVariableLongName($catName, $shortName, $filter) {
        // Long Name
        $this->assertType("string", $filter['longName'], $catName.":".$shortName.": Long name is not a string");
        $this->assertThat(strlen($filter['longName']), $this->greaterThan(0), $catName.":".$shortName.": Long name is not a set");    
    }

    /**
     * Generic function to test filter routines.
     *
     * call by using parent::filterTest()
     */
    public static function filterTest($class, $method, $val) {
    
    }
}

?>
