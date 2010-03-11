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
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */


require_once dirname(__FILE__).'/../devInfo.php';

/**
 * Test class for filter.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:56.
 *
 * @category   Devices
 * @package    HUGnetLibTest
 * @subpackage Devices
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class DevInfoTest extends PHPUnit_Framework_TestCase
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
    }

    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDeviceID()
    {
        return array(
            array(
                  array(
                        "DeviceID" => "12345",
                        "From" => "54321"
                       ),
                  array(
                        "DeviceID" => "012345",
                        "From" => "54321"
                       )
                 ),
            array(
                  array(
                        "From" => "12345"
                       ),
                  array(
                        "From" => "12345",
                        "DeviceID" => "012345"
                       )
                 ),
            array(
                  array(
                        "PacketFrom" => "12345"
                       ),
                  array(
                        "PacketFrom" => "12345",
                        "DeviceID" => "012345"
                       )
                 ),
        );
    }

    /**
    * test
    *
    * @param array  $Info   devInfo array of the device we are using
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDeviceID
    */
    public function testDeviceID($Info, $expect)
    {
        $ret = devInfo::DeviceID($Info);
        $this->assertSame($expect, $Info, "Info modified incorrectly");
        $this->assertSame($expect["DeviceID"], $ret, "Return not correct");
    }


    /**
    * data provider for testDeviceID
    *
    * @return array
    */
    public static function dataDeviceID2SN()
    {
        return array(
            array("000010", 16),
            array("V00010", -16),
        );
    }

    /**
    * test
    *
    * @param string $id     The device ID to test
    * @param int    $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDeviceID2SN
    */
    public function testDeviceID2SN($id, $expect)
    {
        $ret = devInfo::DeviceID2SN($id);
        $this->assertSame($expect, $ret);
    }
    /**
    * test
    *
    * @param string $expect The expected return
    * @param int    $sn     The serial number to convert
    *
    * @return null
    *
    * @dataProvider dataDeviceID2SN
    */
    public function testSN2DeviceID($expect, $sn)
    {
        $ret = devInfo::sn2DeviceID($sn);
        $this->assertSame($expect, $ret);
    }



    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataRawData()
    {
        return array(
            array(
                  array(
                        "RawData" => "12345",
                        "Data" => "54321"
                       ),
                  array(
                        "RawData" => "12345",
                        "Data" => "54321"
                       )
                 ),
            array(
                  array(
                        "Data" => "12345"
                       ),
                  array(
                        "Data" => "12345",
                        "RawData" => "12345"
                       )
                 ),
            array(
                  array(
                        "rawdata" => "12345"
                       ),
                  array(
                        "rawdata" => "12345",
                        "RawData" => "12345"
                       )
                 ),
            array(
                  array(
                        "RawSetup" => "12345"
                       ),
                  array(
                        "RawSetup" => "12345",
                        "RawData" => "12345"
                       )
                 ),
        );
    }

    /**
    * test
    *
    * @param array  $Info   devInfo array of the device we are using
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataRawData
    */
    public function testRawData($Info, $expect)
    {
        $ret = devInfo::RawData($Info);
        $this->assertSame($expect, $Info, "Info modified incorrectly");
        $this->assertSame($expect["RawData"], $ret, "Return not correct");
    }

    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataSetDate()
    {
        return array(
            array(
                  array(
                        "Date" => "2007-11-27 12:23:47"
                       ),
                  "LastConfig",
                  array(
                        "Date" => "2007-11-27 12:23:47",
                        "LastConfig" => "2007-11-27 12:23:47"
                       )
                 ),
            array(
                  array(
                        "Date" => "2007-11-27 12:23:47",
                        "LastConfig" => "2007-11-20 12:23:47"
                       ),
                  "LastConfig",
                  array(
                        "Date" => "2007-11-27 12:23:47",
                        "LastConfig" => "2007-11-27 12:23:47"
                       )
                 ),
        );
    }

    /**
    * test
    *
    * @param array  $Info   devInfo array of the device we are using
    * @param string $Field  The date field to check
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataSetDate
    */
    public function testSetDate($Info, $Field, $expect)
    {
        $ret = devInfo::SetDate($Info, $Field);
        $this->assertSame($expect, $Info, "Info modified incorrectly");
        $this->assertSame($expect[$Field], $ret, "Return not correct");
        $this->assertRegExp(
            "/[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]/",
            $Info[$Field]
        );
    }

    /**
    * Tests the date as it is returned if no date is in Info
    *
    * @return null
    */
    public function testSetDateNotGiven()
    {
        $Info  = array();
        $Field = "LastConfig";
        $ret   = devInfo::SetDate($Info, $Field);
        $this->assertRegExp(
            "/[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]/",
            $Info[$Field]
        );
    }


    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataSetStringSize()
    {
        return array(
            array("", 6, "0", "000000"),
            array("12345678", 5, "0", "45678"),
            array("12", 4, null, "0012"),
        );
    }

    /**
    * test
    *
    * @param string $value  The string to play with
    * @param int    $size   The size to cut the string to
    * @param string $pad    The value to pad the string with
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataSetStringSize
    */
    public function testSetStringSize($value, $size, $pad, $expect)
    {
        if (is_null($pad)) {
            $ret = devInfo::setStringSize($value, $size);
        } else {
            $ret = devInfo::setStringSize($value, $size, $pad);
        }
        $this->assertSame($expect, $value, '$value not changed correctly');
        $this->assertSame($expect, $ret, "Return was not correct");
    }

    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataHexifyVersion()
    {
        return array(
            array("1.2.3", "010203"),
            array("11.12.13", "111213"),
        );
    }

    /**
    * test
    *
    * @param string $version The version to play with
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataHexifyVersion
    */
    public function testHexifyVersion($version, $expect)
    {
        $value = devInfo::hexifyVersion($version);
        $this->assertSame($expect, $value);
    }

    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataHexifyPartNum()
    {
        return array(
            array("0039-20-06-C", "0039200643"),
            array("0039-26-01-P", "0039260150"),
        );
    }

    /**
    * test
    *
    * @param string $partNum The partNum to play with
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataHexifyPartNum
    */
    public function testHexifyPartNum($partNum, $expect)
    {
        $value = devInfo::hexifyPartNum($partNum);
        $this->assertSame($expect, $value);
    }


    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataDehexifyVersion()
    {
        return array(
            array("010203", "1.2.3"),
            array("111213", "11.12.13"),
        );
    }

    /**
    * test
    *
    * @param string $version The string to play with
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataDehexifyVersion
    */
    public function testDehexifyVersion($version, $expect)
    {
        $value = devInfo::dehexifyVersion($version);
        $this->assertSame($expect, $value);
    }

    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataDehexifyPartNum()
    {
        return array(
            array("0039200643", "0039-20-06-C"),
        );
    }

    /**
    * test
    *
    * @param string $partNum The string to play with
    * @param string $expect  The expected return
    *
    * @return null
    *
    * @dataProvider dataDehexifyPartNum
    */
    public function testDehexifyPartNum($partNum, $expect)
    {
        $value = devInfo::dehexifyPartNum($partNum);
        $this->assertSame($expect, $value);
    }


    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataDehexify()
    {
        return array(
            array("4142434445", "ABCDE"),
            array("6162636465", "abcde"),
        );
    }


    /**
    * test
    *
    * @param string $str    The string to play with
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataDehexify
    */
    public function testDehexify($str, $expect)
    {
        $bin = devInfo::dehexify($str);
        $this->assertSame($expect, $bin);
    }

    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataHexify()
    {
        return array(
            array(1, null, "01"),
            array(-1, 4, "FFFF"),
            array(1024, 2, "00"),
            array(1024, 4, "0400"),
        );
    }
    /**
    * test
    *
    * @param int    $value  The number to play with
    * @param int    $width  The width of the output string
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataHexify
    */
    public function testHexify($value, $width, $expect)
    {
        if (is_null($width)) {
            $ret = devInfo::hexify($value);
        } else {
            $ret = devInfo::hexify($value, $width);
        }
        $this->assertEquals($expect, $ret);
    }


    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataHexifyStr()
    {
        return array(
            array("\0\r\n", "000D0A"),
            array("123", "313233"),
            array("ABC", "414243"),
        );
    }
    /**
    * test
    *
    * @param string $str    The string to play with
    * @param string $expect The expected return
    *
    * @return null
    *
    * @dataProvider dataHexifyStr
    */
    public function testHexifyStr($str, $expect)
    {
        $ret = devInfo::hexifyStr($str);
        $this->assertEquals($expect, $ret);
    }


    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataGetYdhms()
    {
        return array(
            array(100000, 0, "1d 3h 46m 40s"),
            array("1234", 0, "20m 34s"),
            array(array(), 0, "0s"),
            array(0, 0, "0s"),
            array(1234567890, 0, "39Y 44d 5h 31m 30s"),
        );
    }
    /**
    * test
    *
    * @param mixed  $val    The value to feed the function
    * @param int    $digits The number of digits
    * @param string $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetYdhms
    */
    public function testGetYdhms($val, $digits, $expect)
    {
        $ret = devInfo::getYdhms($val, $digits);
        $this->assertEquals($expect, $ret);
    }

    /**
    * data provider for testGetBytes
    *
    * @return array
    */
    public static function dataGetBytes()
    {
        return array(
            array(10000, 2, "9.77 k bytes"),
            array("1234", 2, "1.21 k bytes"),
            array(array(), 2, "0.00 bytes"),
            array(0, 2, "0.00 bytes"),
        );
    }
    /**
    * test
    *
    * @param mixed  $val    The value to feed the function
    * @param int    $digits The number of digits
    * @param string $expect The expected return value
    *
    * @return null
    *
    * @dataProvider dataGetBytes
    */
    public function testGetBytes($val, $digits, $expect)
    {
        $ret = devInfo::getBytes($val, $digits);
        $this->assertEquals($expect, $ret);
    }



}


?>
