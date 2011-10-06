<?php
/**
 * Tests the filter class
 *
 * PHP Version 5
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */

/** Get our classes */
require_once CODE_BASE.'plugins/outputFilters/NullOutputFilterPlugin.php';
/** This is a required class */
require_once CODE_BASE.'containers/OutputContainer.php';
/** This is a required class */
require_once CODE_BASE.'containers/ConfigContainer.php';
/** This is a required class */
require_once 'OutputFilterPluginTestBase.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuitePlugins
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class NullOutputFilterPluginTest extends OutputFilterPluginTestBase
{

    /**
    * Sets up the fixture, for example, open a network connection.
    * This method is called before a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function setUp()
    {
        $config = array(
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->socket = &$this->config->sockets->getSocket("default");
        $this->d = new OutputContainer();
        $this->o = new NullOutputFilterPlugin(array(), $this->d);
    }

    /**
    * Tears down the fixture, for example, close a network connection.
    * This method is called after a test is executed.
    *
    * @return null
    *
    * @access protected
    */
    protected function tearDown()
    {
        unset($this->o);
    }

    /**
    * Data provider for testRegisterPlugin
    *
    * @return array
    */
    public static function dataRegisterPlugin()
    {
        return array(
            array("NullOutputFilterPlugin"),
        );
    }
    /**
     * Data provider for testExecute
     *
     * @return array
     */
    public static function dataExecute()
    {
        return array(
            array(
                array(
                ),
                array(
                    array("test" => "hello"),
                    array("test" => "there"),
                    array("test" => "world"),
                ),
                "test",
                array(
                    array("test" => "hello"),
                    array("test" => "there"),
                    array("test" => "world"),
                ),
                true,
            ),
        );
    }
    /**
    * test CtoF()
    *
    * @param array  $setup  The stuff to preload into the Filter
    * @param mixed  $data   The data to use
    * @param string $field  The field to use
    * @param mixed  $expect The value to expect
    * @param bool   $return The expected return value
    *
    * @return null
    *
    * @dataProvider dataExecute
    */
    public function testExecute($setup, $data, $field, $expect, $return)
    {
        $this->o = new NullOutputFilterPlugin($setup, $data);
        $ret = $this->o->execute($field);
        $this->assertSame($return, $ret, "Return Wrong");
        $this->assertSame($expect, $data, "The data is wrong");
    }


}

?>
