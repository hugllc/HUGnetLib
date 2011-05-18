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
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../../tables/LockTable.php';
require_once dirname(__FILE__)."/HUGnetDBTableTestBase.php";

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Test
 * @package    HUGnetLibTest
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class LockTableTest extends HUGnetDBTableTestBase
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
            "servers" => array(
                array(
                    "driver" => "sqlite",
                    "file" => ":memory:",
                    "group" => "default",
                ),
                array(
                    "driver" => "sqlite",
                    "file" => ":memory:",
                    "group" => "volatile",
                ),
            ),
        );
        $this->config = &ConfigContainer::singleton();
        $this->config->forceConfig($config);
        $this->pdo = &$this->config->servers->getPDO("volatile");
        $this->o = new LockTableTestStub(array("group" => "volatile"));
        $this->o->create();
        parent::Setup();
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
        unset($this->config);
    }
    /**
    * This gets us our database preload
    *
    * @access protected
    *
    * @return null
    */
    protected function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__).'/../files/LockTableTest.xml'
        );
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataColumns()
    {
        $o = new LockTable(array("group" => "default"));
        return HUGnetDBTableTestBase::splitObject($o, "sqlColumns");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIndexes()
    {
        $o = new LockTable(array("group" => "default"));
        return HUGnetDBTableTestBase::splitObject($o, "sqlIndexes");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataVars()
    {
        return array(
            array(new LockTable(array("group" => "default"))),
        );
    }

    /**
    * data provider for testSet
    *
    * @return array
    */
    public static function dataSet()
    {
        return array(
            array("expiration", "2010-04-25 13:42:23", 1272202943),
            array("expiration", "2010-04-25", 1272153600),
            array("expiration", "Sun, 25 April 2010, 1:42:23pm", 1272202943),
            array("expiration", 1234567890, 1234567890),
            array("expiration", "This is not a date", 0),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $var    The variable to set
    * @param mixed  $value  The value to set
    * @param mixed  $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataSet
    */
    public function testSet($var, $value, $expect)
    {
        $this->o->$var = $value;
        $data = $this->readAttribute($this->o, "data");
        $this->assertSame($expect, $data[$var]);
    }
    /**
    * data provider for testGetLock
    *
    * @return array
    */
    public static function dataCheck()
    {
        return array(
            array(
                1, "lock", "123456",
                array(
                    'group' => 'volatile',
                    'id' => null,
                    'type' => null,
                    'lockData' => '',
                    'expiration' => null,
                ),
                false,
            ),
            array(
                21, "lock", "ABCDEFG",
                array(
                    'group' => 'volatile',
                    'id' => 25,
                    'type' => 'lock',
                    'lockData' => 'ABCDEFG',
                    'expiration' => 150,
                ),
                false,
            ),
            array(
                25, "lock", "ABCDEFG",
                array(
                    'group' => 'volatile',
                    'id' => 25,
                    'type' => 'lock',
                    'lockData' => 'ABCDEFG',
                    'expiration' => 150,
                ),
                true,
            ),
            array(
                25, "lock", "ABCDEFGH",
                array(
                    'group' => 'volatile',
                    'id' => null,
                    'type' => null,
                    'lockData' => '',
                    'expiration' => null,
                ),
                false,
            ),
            array(
                25, null, 10,
                array(
                    'group' => 'volatile',
                    'id' => null,
                    'type' => null,
                    'lockData' => '',
                    'expiration' => null,
                ),
                false,
            ),
            array(
                25, 'lock', null,
                array(
                    'group' => 'volatile',
                    'id' => null,
                    'type' => null,
                    'lockData' => '',
                    'expiration' => null,
                ),
                false,
            ),
            array(
                null, 'lock', 10,
                array(
                    'group' => 'volatile',
                    'id' => null,
                    'type' => null,
                    'lockData' => '',
                    'expiration' => null,
                ),
                false,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param int    $id     The id of the locking element
    * @param string $type   The type of lock
    * @param string $data   The data string
    * @param mixed  $expect The expected class data
    * @param mixed  $ret    The expected return
    *
    * @return null
    *
    * @dataProvider dataCheck
    */
    public function testCheck($id, $type, $data, $expect, $ret)
    {
        $lock = $this->o->check($id, $type, $data);
        $this->assertSame($ret, $lock, "Return Wrong");
        $this->assertSame($expect, $this->o->toArray());
    }
    /**
    * data provider for testPlace
    *
    * @return array
    */
    public static function dataPlace()
    {
        return array(
            array(
                1, "lock", "123456", 50,
                array(
                    'group' => 'volatile',
                    'id' => 1,
                    'type' => 'lock',
                    'lockData' => '123456',
                    'expiration' => 78,
                ),
                true,
            ),
            array(
                21, "lock", "ABCDEFG", 50,
                array(
                    'group' => 'volatile',
                    'id' => 25,
                    'type' => 'lock',
                    'lockData' => 'ABCDEFG',
                    'expiration' => 150,
                ),
                false,
            ),
            array(
                25, "lock", "ABCDEFG", 150,
                array(
                    'group' => 'volatile',
                    'id' => 25,
                    'type' => 'lock',
                    'lockData' => 'ABCDEFG',
                    'expiration' => 178,
                ),
                true,
            ),
            array(
                25, "lock", "ABCDEFG", 10,
                array(
                    'group' => 'volatile',
                    'id' => 25,
                    'type' => 'lock',
                    'lockData' => 'ABCDEFG',
                    'expiration' => 38,
                ),
                true,
            ),
            array(
                25, "lock", "ABCDEFGH", 10,
                array(
                    'group' => 'volatile',
                    'id' => 25,
                    'type' => 'lock',
                    'lockData' => 'ABCDEFGH',
                    'expiration' => 38,
                ),
                true,
            ),
            array(
                25, null, 10, 10,
                array(
                    'group' => 'volatile',
                    'id' => null,
                    'type' => null,
                    'lockData' => '',
                    'expiration' => null,
                ),
                false,
            ),
            array(
                25, 'lock', null, 10,
                array(
                    'group' => 'volatile',
                    'id' => null,
                    'type' => null,
                    'lockData' => '',
                    'expiration' => null,
                ),
                false,
            ),
            array(
                25, 'lock', 10, null,
                array(
                    'group' => 'volatile',
                    'id' => null,
                    'type' => null,
                    'lockData' => '',
                    'expiration' => null,
                ),
                false,
            ),
            array(
                0, 'lock', 10, 10,
                array(
                    'group' => 'volatile',
                    'id' => null,
                    'type' => null,
                    'lockData' => '',
                    'expiration' => null,
                ),
                false,
            ),
            array(
                10, '', 10, 10,
                array(
                    'group' => 'volatile',
                    'id' => null,
                    'type' => null,
                    'lockData' => '',
                    'expiration' => null,
                ),
                false,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param int    $id       The id of the locking element
    * @param string $type     The type of lock
    * @param string $data     The data string
    * @param int    $timeLeft The amount of time left on the lock
    * @param mixed  $expect   The expected class data
    * @param mixed  $ret      The expected return
    *
    * @return null
    *
    * @dataProvider dataPlace
    */
    public function testPlace($id, $type, $data, $timeLeft, $expect, $ret)
    {
        $lock = $this->o->place($id, $type, $data, $timeLeft);
        $this->assertSame($ret, $lock, "Return Wrong");
        $this->assertSame($expect, $this->o->toArray());
    }
    /**
    * data provider for testGetAllLocks
    *
    * @return array
    */
    public static function dataGetAllLocks()
    {
        return array(
            array(
                25, "lock",
                array(
                    array(
                        'group' => 'volatile',
                        'id' => 25,
                        'type' => 'lock',
                        'lockData' => 'ABCDEFG',
                        'expiration' => 150,
                    ),
                ),
                true,
            ),
            array(
                null, "lock",
                array(
                    array(
                        'group' => 'volatile',
                        'id' => 25,
                        'type' => 'lock',
                        'lockData' => 'ABCDEFG',
                        'expiration' => 150,
                    ),
                    array(
                        'group' => 'volatile',
                        'id' => 27,
                        'type' => 'lock',
                        'lockData' => 'GFBAQ',
                        'expiration' => 165,
                    ),
                    array(
                        'group' => 'volatile',
                        'id' => 26,
                        'type' => 'lock',
                        'lockData' => 'GFEDCBAQ',
                        'expiration' => 180,
                    ),
                ),
                true,
            ),
            array(
                25, "other",
                array(
                ),
                false,
            ),
            array(
                1, "lock",
                array(
                ),
                false,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param int    $id     The id of the locking element
    * @param string $type   The type of lock
    * @param mixed  $expect The expected class data
    * @param mixed  $ret    The expected return
    *
    * @return null
    *
    * @dataProvider dataGetAllLocks
    */
    public function testGetAllLocks($id, $type, $expect, $ret)
    {
        $lock = $this->o->getAllLocks($type, $id);
        $this->assertSame($ret, $lock, "Return Wrong");
        $data = array();
        while ($lock) {
            $data[] = $this->o->toArray();
            $lock = $this->o->nextInto();
        };
        $this->assertSame($expect, $data);
    }
    /**
    * data provider for testGetAllLocks
    *
    * @return array
    */
    public static function dataPurgeAll()
    {
        return array(
            array(
                "volatile",
                true,
            ),
        );
    }
    /**
    * test the set routine when an extra class exists
    *
    * @param string $group The group to use
    * @param mixed  $ret   The expected return
    *
    * @return null
    *
    * @dataProvider dataPurgeAll
    */
    public function testPurgeAll($group, $ret)
    {
        $lock = $this->o->purgeAll();
        $this->assertSame($ret, $lock, "Return Wrong");
        $stmt = $this->pdo->query("SELECT * FROM `locks`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame(array(), $rows, "Database wrong");
    }
}
/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Tables
 * @package    HUGnetLib
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class LockTableTestStub extends LockTable
{
    /**
    * Returns the current time in seconds.  This is for testing purposes
    *
    * @return int
    */
    protected function now()
    {
        return 28;
    }
}
?>
