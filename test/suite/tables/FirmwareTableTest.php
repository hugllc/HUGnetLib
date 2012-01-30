<?php
/**
 * Tests the filter class
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
 * @package    HUGnetLibTest
 * @subpackage SuiteTables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is a required class */
require_once CODE_BASE.'tables/FirmwareTable.php';
/** This is a required class */
require_once TEST_BASE."tables/HUGnetDBTableTestBase.php";
/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Libraries
 * @package    HUGnetLibTest
 * @subpackage SuiteTables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2012 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.9.7
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class FirmwareTableTest extends HUGnetDBTableTestBase
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
        $this->config = array(
        );
        $this->myConfig = &ConfigContainer::singleton();
        $this->myConfig->forceConfig($this->config);
        $this->pdo = &$this->myConfig->servers->getPDO();
        $this->o = new FirmwareTable();
        $this->o->create();
        parent::Setup();
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
            TEST_CONFIG_BASE.'files/FirmwareTableTest.xml'
        );
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
        $this->o = null;
        $this->config = null;
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataColumns()
    {
        $obj = new FirmwareTable();
        return HUGnetDBTableTestBase::splitObject($obj, "sqlColumns");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataIndexes()
    {
        $obj = new FirmwareTable();
        return HUGnetDBTableTestBase::splitObject($obj, "sqlIndexes");
    }
    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataVars()
    {
        return array(
            array(new FirmwareTable()),
        );
    }

    /**
    * data provider for testGetLatest
    *
    * @return array
    */
    public static function dataGetLatest()
    {
        return array(
            array(
                // No HWPartNum Specified
                array(
                    "FWPartNum" => "0039-20-01-C",
                    "RelStatus" => 8,
                    "Target" => "mega16",
                ),
                array(
                    "group" => "default",
                    "filename" => "",
                    "id" => "3",
                    "Version" => "2.2.3",
                    "Code" => "abc",
                    "CodeHash" => (string)md5("abc"),
                    "Data" => "def",
                    "DataHash" => (string)md5("def"),
                    "FWPartNum" => "0039-20-01-C",
                    "HWPartNum" => "0039-21",
                    "Date" => 1208318061,
                    "FileType" => "SREC",
                    "RelStatus" => 8,
                    "Tag" => "v1.2.3",
                    "Target" => "mega16",
                    "Active" => "1",
                    "md5" => null,
                ),
                true,
            ),
            array(
                array(
                    "FWPartNum" => "0039-20-01-C",
                    "HWPartNum" => "0039-21",
                    "RelStatus" => 8,
                    "Target" => "mega16",
                ),
                array(
                    "group" => "default",
                    "filename" => "",
                    "id" => "3",
                    "Version" => "2.2.3",
                    "Code" => "abc",
                    "CodeHash" => (string)md5("abc"),
                    "Data" => "def",
                    "DataHash" => (string)md5("def"),
                    "FWPartNum" => "0039-20-01-C",
                    "HWPartNum" => "0039-21",
                    "Date" => 1208318061,
                    "FileType" => "SREC",
                    "RelStatus" => 8,
                    "Tag" => "v1.2.3",
                    "Target" => "mega16",
                    "Active" => "1",
                    "Target" => "mega16",
                    "md5" => null,
                ),
                true,
            ),
            array(
                array(
                    "FWPartNum" => "0039-20-01-C",
                    "HWPartNum" => "0039-21",
                    "RelStatus" => 8,
                    "Target" => "mega16",
                    "Version" => "2.2.3",
                ),
                array(
                    "group" => "default",
                    "filename" => "",
                    "id" => "3",
                    "Version" => "2.2.3",
                    "Code" => "abc",
                    "CodeHash" => (string)md5("abc"),
                    "Data" => "def",
                    "DataHash" => (string)md5("def"),
                    "FWPartNum" => "0039-20-01-C",
                    "HWPartNum" => "0039-21",
                    "Date" => 1208318061,
                    "FileType" => "SREC",
                    "RelStatus" => 8,
                    "Tag" => "v1.2.3",
                    "Target" => "mega16",
                    "Active" => "1",
                    "Target" => "mega16",
                    "md5" => null,
                ),
                true,
            ),
            array(
                array(
                    "FWPartNum" => "0039-20-01-C",
                    "HWPartNum" => "0039-21",
                    "RelStatus" => 4,
                    "Target" => "mega16",
                ),
                array(
                    "group" => "default",
                    "filename" => "",
                    "id" => "1",
                    "Version" => "1.2.3",
                    "Code" => "abc",
                    "CodeHash" => "900150983cd24fb0d6963f7d28e17f72",
                    "Data" => "def",
                    "DataHash" => "4ed9407630eb1000c0f6b63842defa7d",
                    "FWPartNum" => "0039-20-01-C",
                    "HWPartNum" => "0039-21",
                    "Date" => 1271390061,
                    "FileType" => "SREC",
                    "RelStatus" => 4,
                    "Tag" => "v1.2.3",
                    "Target" => "mega16",
                    "Active" => "1",
                    "md5" => "104e9ba35ebb82e6c70a9909e375b8be",
                ),
                true,
            ),
            array(
                array(
                    "FWPartNum" => "0039-01-01-C",
                    "HWPartNum" => "0039-86",
                    "Status" => 8,
                    "Target" => "mega16",
                ),
                array(
                ),
                false,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array $preload The value to preload
    * @param array $data    The data array to exepct
    * @param bool  $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataGetLatest
    */
    public function testGetLatest($preload, $data, $expect)
    {
        $this->o->fromArray($preload);
        $ret = $this->o->getLatest();
        $this->assertSame($expect, $ret, "Return value was not correct");
        if ($ret) {
            $this->assertAttributeSame($data, "data", $this->o, "Data not correct");
        }
    }
    /**
    * data provider for testToFile
    *
    * @return array
    */
    public static function dataToFile()
    {
        return array(
            array(
                // Everything works
                array(
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "Version" => "1.2.3",
                    "Code" => "asdf",
                    "Data" => "fsda",
                    "RelStatus" => 8,
                    "Target" => "mega16",
                ),
                sys_get_temp_dir(),
                "00392001C-1.2.3.gz",
                true,
            ),
            array(
                // No HWPartNum Specified
                array(
                    "HWPartNum" => "0039-21-01-A",
                    "FWPartNum" => "0039-20-01-C",
                    "Version" => "1.2.3",
                    "Code" => "asdf",
                    "Data" => "fsda",
                    "RelStatus" => 8,
                    "Target" => "mega16",
                ),
                "/this/is/a/dir/that/should/never/exist",
                "00392001C-1.2.3.gz",
                false,
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param array  $preload  The value to preload
    * @param string $path     The file path to use
    * @param string $filename The filename to check for
    * @param bool   $expect   The expected return
    *
    * @return null
    *
    * @dataProvider dataToFile
    */
    public function testToFile($preload, $path, $filename, $expect)
    {
        @unlink($path."/".$filename);
        $this->o->fromArray($preload);
        $ret = @$this->o->toFile($path);
        $this->assertSame($expect, $ret);
        if ($expect) {
            $this->assertTrue(file_exists($path."/".$filename));
            $stuff = implode("", gzfile($path."/".$filename));
            @unlink($path."/".$filename);
            $this->assertSame(
                $this->o->toString(), $stuff, "File contents wrong"
            );
        } else {
            $this->assertFalse(file_exists($path."/".$filename));
        }
    }
    /**
    * data provider for testFromFile
    *
    * @return array
    */
    public static function dataFromFile()
    {
        return array(
            array(
                // Everything works
                TEST_CONFIG_BASE."files",
                "MD5 (00392001C-00.01.04.gz) = 84b01697b6fbc32c457d3b9815585fe0",
                true,
                array(
                    "group" => "default",
                    "filename" => "00392001C-00.01.04.gz",
                    "id" => "37",
                    "Version" => "0.1.4",
                    "CodeHash" => "14c1839ee1c69da903a49165693d8ff2",
                    "DataHash" => "ff0091534269deecbe6dfcecc652664f",
                    "FWPartNum" => "0039-20-01-C",
                    "HWPartNum" => "0039-21",
                    "Date" => 1172579985,
                    "FileType" => "SREC",
                    "RelStatus" => 8,
                    "Tag" => "f00392001-00-01-04",
                    "Target" => "atmega16",
                    "Active" => "1",
                    "md5" => "84b01697b6fbc32c457d3b9815585fe0",
                ),
            ),
            array(
                // Everything works
                TEST_CONFIG_BASE."files",
                "00392001C-00.01.04.gz",
                true,
                array(
                    "group" => "default",
                    "filename" => "00392001C-00.01.04.gz",
                    "id" => "37",
                    "Version" => "0.1.4",
                    "CodeHash" => "14c1839ee1c69da903a49165693d8ff2",
                    "DataHash" => "ff0091534269deecbe6dfcecc652664f",
                    "FWPartNum" => "0039-20-01-C",
                    "HWPartNum" => "0039-21",
                    "Date" => 1172579985,
                    "FileType" => "SREC",
                    "RelStatus" => 8,
                    "Tag" => "f00392001-00-01-04",
                    "Target" => "atmega16",
                    "Active" => "1",
                    "md5" => null,
                ),
            ),
            array(
                // Bad MD5
                TEST_CONFIG_BASE."files",
                "MD5 (00392001C-00.01.04.gz) = bd2dd61d3ef24bfab9d40c8791f3b18b",
                false,
                array(
                ),
            ),
            array(
                // No HWPartNum Specified
                "/this/is/a/dir/that/should/never/exist",
                "00392001C-1.2.3.gz",
                false,
                array(),
            ),
        );
    }

    /**
    * test the set routine when an extra class exists
    *
    * @param string $path     The file path to use
    * @param string $filename The filename to check for
    * @param bool   $expect   The expected return
    * @param array  $data     The data array to expect
    *
    * @return null
    *
    * @dataProvider dataFromFile
    */
    public function testFromFile($path, $filename, $expect, $data)
    {
        $this->o->clearData();
        $ret = @$this->o->fromFile($filename, $path);
        $this->assertSame($expect, $ret);
        if ($expect) {
            $theData = $this->readAttribute($this->o, "data");
            unset($theData["Code"]);
            unset($theData["Data"]);
            $this->assertSame($data, $theData);
        } else {
            $this->assertFalse(file_exists($path."/".$filename));
        }
    }
    /**
    * data provider for testSet
    *
    * @return array
    */
    public static function dataSet()
    {
        return array(
            array("Date", "2010-04-25 13:42:23", 1272202943),
            array("Date", "2010-04-25", 1272153600),
            array("Date", "Sun, 25 April 2010, 1:42:23pm", 1272202943),
            array("Date", 1234567890, 1234567890),
            array("Date", "This is not a date", 0),
            array("RelStatus", "DEV", FirmwareTable::DEV),
            array("RelStatus", "BAD", FirmwareTable::BAD),
            array("RelStatus", "BETA", FirmwareTable::BETA),
            array("RelStatus", "RELEASE", FirmwareTable::RELEASE),
            array("RelStatus", "72", 72),
            array("HWPartNum", "00392104C", "0039-21"),
            array("HWPartNum", "0039-21-04-C", "0039-21"),
            array("HWPartNum", "34523442350039-21-04-C", "0039-21"),
            array("FWPartNum", "00392104C", "0039-21-04-C"),
            array("FWPartNum", "0039-21-04-C", "0039-21-04-C"),
            array("FWPartNum", "34523442350039-21-04-C", "0039-21-04-C"),
            array(
                "filename",
                "MD5 (00392001C-00.00.07.gz) = 104e9ba35ebb82e6c70a9909e375b8be",
                "00392001C-00.00.07.gz"
            ),
            array(
                "filename",
                "00392001C-00.00.07.gz",
                "00392001C-00.00.07.gz"
            ),
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
    * data provider for testCompareVesrion
    *
    * @return array
    */
    public static function dataCompareVersion()
    {
        return array(
            array(array(), "1.2.3", "1.2.3", 0),
            array(array(), "1.2.4", "1.2.3", 1),
            array(array(), "1.3.3", "1.2.3", 1),
            array(array(), "2.2.3", "1.2.3", 1),
            array(array(), "1.2.3", "1.2.4", -1),
            array(array(), "1.2.3", "1.3.3", -1),
            array(array(), "1.2.3", "2.2.3", -1),
            array(array("Version" => "1.3.3"), "1.2.3", null, -1),
            array(array("Version" => "1.2.3"), "1.2.4", "", 1),
            array(array("Version" => "1.2.3"), "01.02.03", "", 0),
            array(array(), "01.02.03", "", 1),
        );
    }
    /**
    * test
    *
    * @param array  $preload The value to preload
    * @param string $v1      The first version
    * @param string $v2      The second version
    * @param int    $expect  What to expect (1, 0, -1)
    *
    * @return null
    *
    * @dataProvider dataCompareVersion
    */
    function testCompareVersion($preload, $v1, $v2, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $ret = $this->o->CompareVersion($v1, $v2);
        $this->assertEquals($expect, $ret);
    }
    /**
    * data provider for testCompareVesrion
    *
    * @return array
    */
    public static function dataCheckFile()
    {
        return array(
            array(array(), "00392001C-01.02.03.gz", true),
            array(
                array(),
                "MD5 (00392001C-00.00.07.gz) = 104e9ba35ebb82e6c70a9909e375b8be",
                false,
            ),
            array(
                array(),
                "MD5 (00392001C-01.02.03.gz) = 104e9ba35ebb82e6c70a9909e375b8be",
                true,
            ),
            // Wrong md5
            array(
                array(),
                "MD5 (00392001C-01.02.03.gz) = 104e9ba35ebb82e6c70a9909e375b8bf",
                false,
            ),
        );
    }
    /**
    * test
    *
    * @param array  $preload The value to preload
    * @param string $file    The filename to give it
    * @param bool   $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataCheckFile
    */
    function testCheckFile($preload, $file, $expect)
    {
        $this->o->clearData();
        $this->o->fromAny($preload);
        $ret = $this->o->checkFile($file);
        $this->assertEquals($expect, $ret);
    }
    /**
    * Data provider for testInsertRow
    *
    * @return array
    */
    public static function dataExists()
    {
        return array(
            array(
                array(
                    array(
                        "FWPartNum" => "0039-21-01-C",
                        "Version" => "1.2.3",
                    ),
                ),
                array(
                    "FWPartNum" => "0039-21-01-C",
                    "Version" => "1.2.3",
                ),
                true
            ),
            array(
                array(
                    array(
                        "FWPartNum" => "0039-21-01-C",
                        "Version" => "1.2.3",
                    ),
                ),
                array(
                    "FWPartNum" => "0039-21-01-C",
                    "Version" => "1.2.4",
                ),
                false
            ),
        );
    }
    /**
    * Tests the insert of a DeviceID
    *
    * @param array $preload The data to load into the database
    * @param mixed $data    The data to use
    * @param array $expect  The expected return
    *
    * @dataProvider dataExists
    *
    * @return null
    */
    public function testExists($preload, $data, $expect)
    {
        foreach ((array)$preload as $load) {
            $this->o->fromAny($load);
            $this->o->insertRow();
        }
        $this->o->clearData();
        $this->o->fromAny($data);
        $ret = $this->o->exists();
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider
    *
    * @return array
    */
    public static function dataGetData()
    {
        return array(
            array(
                array(
                    "Data" => "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300A0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                ),
                "FF",
                "FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F6F6F6F6F707070010000000000000002"
                    ."000000000000001027001027000000102700102700000010270010"
                    ."270000001027001027000000102700102700000010270010270000"
                    ."002027001027000000202700102700000020270010270000002027"
                    ."001027000000202700102700000020270010270000002027001027"
                    ."00000020270010270000002027001027000000"
            ),
        );
    }

    /**
    * test
    *
    * @param array  $preload The preload for the object
    * @param string $empty   The empty value for the memory
    * @param mixed  $expect  The return value to expect.
    *
    * @return null
    *
    * @dataProvider dataGetData().
    */
    public function testGetData($preload, $empty, $expect)
    {
        $this->o->fromAny($preload);
        $ret = $this->o->getData($empty);
        $this->assertSame($expect, $ret);
    }
    /**
    * data provider
    *
    * @return array
    */
    public static function dataGetCode()
    {
        return array(
            array(
                array(
                    "Code" => "S1230000FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F46
S12300206F6F6F6F707070010000000000000002000000000000001027001027000000102F
S12300402700102700000010270010270000001027001027000000102700102700000010E4
S1230060270010270000002027001027000000202700102700000020270010270000002084
S1230080270010270000002027001027000000202700102700000020270010270000002064
S12300C0270010270000002027001027000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF71
S9030000FC",
                ),
                "FF",
                "FFFFFFFFFFFFFFFFFFFF0039201343000008FFFFFF"
                    ."500102020202101002026F6F6F6F6F707070010000000000000002"
                    ."000000000000001027001027000000102700102700000010270010"
                    ."270000001027001027000000102700102700000010270010270000"
                    ."002027001027000000202700102700000020270010270000002027"
                    ."001027000000202700102700000020270010270000002027001027"
                    ."00000020FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF"
                    ."FFFFFFFFFFFFFFFFFF270010270000002027001027000000"
            ),
        );
    }

    /**
    * test
    *
    * @param array  $preload The preload for the object
    * @param string $empty   The empty value for the memory
    * @param mixed  $expect  The return value to expect.
    *
    * @return null
    *
    * @dataProvider dataGetCode().
    */
    public function testGetCode($preload, $empty, $expect)
    {
        $this->o->fromAny($preload);
        $ret = $this->o->getCode($empty);
        $this->assertSame($expect, $ret);
    }

}

?>
