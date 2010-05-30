<?php
/**
 * This is the default endpoint driver and the base for all other
 * endpoint drivers.
 *
 * PHP Version 5
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Misc
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
/** This is for the base class */
require_once dirname(__FILE__)."/../../base/sensors/ResistiveDeviceSensorBase.php";

/**
 * This class has functions that relate to the manipulation of elements
 * of the devInfo array.
 *
 * @category   Containers
 * @package    HUGnetLib
 * @subpackage Database
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class IMCSolarDeviceSensor extends ResistiveDeviceSensorBase
{
    /** @var This is to register the class */
    public static $registerPlugin = array(
        "Name" => "IMCSolarDeviceSensor",
        "Type" => "sensor",
        "Class" => "IMCSolarDeviceSensor",
        "Sensors" => array("02:imcSolar"),
    );
    /** @var object These are the valid values for units */
    protected $idValues = array(2);
    /** @var object These are the valid values for type */
    protected $typeValues = array("imcSolar");

    /**
    * This is the array of sensor information.
    */
    protected $fixed = array(
        "longName" => "IMC Solar Sensor",
        "unitType" => "Temperature",
        "units" => '&#176;C',
        "extraText" => array("Bias Resistor in k Ohms"),
        "extraDefault" => array(100),
    );
    /** @var array The table for IMC Sensors */
    protected $valueTable = array(
        "337045.0" => -40.00, "324834.0" => -39.44, "313110.0" => -38.89,
        "301853.0" => -38.33, "291040.0" => -37.78, "280655.0" => -37.22,
        "270677.0" => -36.67, "261091.0" => -36.11, "251879.0" => -35.56,
        "243025.0" => -35.00, "234511.0" => -34.44, "226329.0" => -33.89,
        "218463.0" => -33.33, "210898.0" => -32.78, "203623.0" => -32.22,
        "196624.0" => -31.67, "189891.0" => -31.11, "183413.0" => -30.56,
        "177178.0" => -30.00, "171178.0" => -29.44, "165403.0" => -28.89,
        "159843.0" => -28.33, "154489.0" => -27.78, "149334.0" => -27.22,
        "144369.0" => -26.67, "139587.0" => -26.11, "134980.0" => -25.56,
        "130542.0" => -25.00, "126265.0" => -24.44, "122143.0" => -23.89,
        "118170.0" => -23.33, "114341.0" => -22.78, "110649.0" => -22.22,
        "107089.0" => -21.67, "103656.0" => -21.11, "100346.0" => -20.56,
        "97152.0" => -20.00, "94071.0" => -19.44, "91099.0" => -18.89,
        "88231.0" => -18.33, "85464.0" => -17.78, "82792.0" => -17.22,
        "80214.0" => -16.67, "77724.0" => -16.11, "75321.0" => -15.56,
        "73000.0" => -15.00, "70759.0" => -14.44, "68594.0" => -13.89,
        "66503.0" => -13.33, "64483.0" => -12.78, "62531.0" => -12.22,
        "60644.0" => -11.67, "58821.0" => -11.11, "57060.0" => -10.56,
        "55357.0" => -10.00, "53711.0" => -9.44, "52119.0" => -8.89,
        "50580.0" => -8.33, "49092.0" => -7.78, "47652.0" => -7.22,
        "46259.0" => -6.67, "44912.0" => -6.11, "43609.0" => -5.56,
        "42348.0" => -5.00, "41128.0" => -4.44, "39947.0" => -3.89,
        "38803.0" => -3.33, "37697.0" => -2.78, "36625.0" => -2.22,
        "35588.0" => -1.67, "34584.0" => -1.11, "33611.0" => -0.56,
        "32669.0" => 0.00, "31756.0" => 0.56, "30872.0" => 1.11,
        "30015.0" => 1.67, "29186.0" => 2.22, "28381.0" => 2.78,
        "27602.0" => 3.33, "26847.0" => 3.89, "26114.0" => 4.44,
        "25405.0" => 5.00, "24716.0" => 5.56, "24049.0" => 6.11,
        "23402.0" => 6.67, "22774.0" => 7.22, "22166.0" => 7.78,
        "21575.0" => 8.33, "21003.0" => 8.89, "20447.0" => 9.44,
        "19908.0" => 10.00, "19385.0" => 10.56, "18877.0" => 11.11,
        "18384.0" => 11.67, "17906.0" => 12.22, "17442.0" => 12.78,
        "16991.0" => 13.33, "16553.0" => 13.89, "16128.0" => 14.44,
        "15715.0" => 15.00, "15315.0" => 15.56, "14925.0" => 16.11,
        "14547.0" => 16.67, "14180.0" => 17.22, "13823.0" => 17.78,
        "13476.0" => 18.33, "13139.0" => 18.89, "12812.0" => 19.44,
        "12494.0" => 20.00, "12184.0" => 20.56, "11884.0" => 21.11,
        "11592.0" => 21.67, "11307.0" => 22.22, "11031.0" => 22.78,
        "10762.0" => 23.33, "10501.0" => 23.89, "10247.0" => 24.44,
        "10000.0" => 25.00, "9759.0" => 25.56, "9526.0" => 26.11,
        "9298.0" => 26.67, "9077.0" => 27.22, "8861.0" => 27.78,
        "8652.0" => 28.33, "8448.0" => 28.89, "8249.4" => 29.44,
        "8056.1" => 30.00, "7868.0" => 30.56, "7684.8" => 31.11,
        "7506.5" => 31.67, "7332.9" => 32.22, "7163.8" => 32.78,
        "6999.1" => 33.33, "6838.8" => 33.89, "6682.6" => 34.44,
        "6530.4" => 35.00, "6382.2" => 35.56, "6237.8" => 36.11,
        "6097.2" => 36.67, "5960.1" => 37.22, "5826.5" => 37.78,
        "5696.4" => 38.33, "5569.5" => 38.89, "5445.8" => 39.44,
        "5325.3" => 40.00, "5207.8" => 40.56, "5093.3" => 41.11,
        "4981.6" => 41.67, "4872.8" => 42.22, "4766.6" => 42.78,
        "4663.1" => 43.33, "4562.1" => 43.89, "4463.6" => 44.44,
        "4367.6" => 45.00, "4273.9" => 45.56, "4182.5" => 46.11,
        "4093.3" => 46.67, "4006.3" => 47.22, "3921.5" => 47.78,
        "3838.6" => 48.33, "3757.8" => 48.89, "3678.9" => 49.44,
        "3601.9" => 50.00, "3526.7" => 50.56, "3453.4" => 51.11,
        "3381.8" => 51.67, "3311.8" => 52.22, "3243.6" => 52.78,
        "3176.9" => 53.33, "3111.8" => 53.89, "3048.3" => 54.44,
        "2986.2" => 55.00, "2925.6" => 55.56, "2866.3" => 56.11,
        "2808.5" => 56.67, "2752.0" => 57.22, "2696.8" => 57.78,
        "2642.9" => 58.33, "2590.2" => 58.89, "2538.7" => 59.44,
        "2488.4" => 60.00, "2439.2" => 60.56, "2391.1" => 61.11,
        "2344.2" => 61.67, "2298.3" => 62.22, "2253.4" => 62.78,
        "2209.5" => 63.33, "2166.6" => 63.89, "2124.7" => 64.44,
        "2083.7" => 65.00, "2043.6" => 65.56, "2004.4" => 66.11,
        "1966.1" => 66.67, "1928.6" => 67.22, "1891.9" => 67.78,
        "1856.1" => 68.33, "1821.0" => 68.89, "1786.7" => 69.44,
        "1753.1" => 70.00, "1720.2" => 70.56, "1688.1" => 71.11,
        "1656.7" => 71.67, "1625.9" => 72.22, "1595.8" => 72.78,
        "1566.3" => 73.33, "1537.5" => 73.89, "1509.3" => 74.44,
        "1481.7" => 75.00, "1454.6" => 75.56, "1428.1" => 76.11,
        "1402.2" => 76.67, "1376.9" => 77.22, "1352.0" => 77.78,
        "1327.7" => 78.33, "1303.9" => 78.89, "1280.6" => 79.44,
        "1257.7" => 80.00, "1235.4" => 80.56, "1213.4" => 81.11,
        "1192.0" => 81.67, "1171.0" => 82.22, "1150.4" => 82.78,
        "1130.2" => 83.33, "1110.5" => 83.89, "1091.1" => 84.44,
        "1072.1" => 85.00, "1053.6" => 85.56, "1035.3" => 86.11,
        "1017.5" => 86.67, "1000.0" => 87.22, "982.9" => 87.78,
        "966.1" => 88.33, "949.6" => 88.89, "933.5" => 89.44,
        "917.7" => 90.00, "902.1" => 90.56, "886.9" => 91.11,
        "872.0" => 91.67, "857.4" => 92.22, "843.1" => 92.78,
        "829.0" => 93.33, "815.3" => 93.89, "801.8" => 94.44,
        "788.5" => 95.00, "775.5" => 95.56, "762.8" => 96.11,
        "750.3" => 96.67, "738.0" => 97.22, "726.0" => 97.78,
        "714.2" => 98.33, "702.6" => 98.89, "691.2" => 99.44,
        "680.1" => 100.00, "669.2" => 100.56, "658.4" => 101.11,
        "647.9" => 101.67, "637.6" => 102.22, "627.4" => 102.78,
        "617.5" => 103.33, "607.7" => 103.89, "598.1" => 104.44,
        "588.7" => 105.00, "579.5" => 105.56, "570.4" => 106.11,
        "561.5" => 106.67, "552.8" => 107.22, "544.2" => 107.78,
        "535.8" => 108.33, "527.5" => 108.89, "519.4" => 109.44,
        "511.4" => 110.00, "503.6" => 110.56, "495.9" => 111.11,
        "488.4" => 111.67, "480.9" => 112.22, "473.7" => 112.78,
        "466.5" => 113.33, "459.5" => 113.89, "452.6" => 114.44,
        "445.8" => 115.00, "439.1" => 115.56, "432.6" => 116.11,
        "426.2" => 116.67, "419.8" => 117.22, "413.6" => 117.78,
        "407.5" => 118.33, "401.5" => 118.89, "395.6" => 119.44,
        "389.9" => 120.00, "384.2" => 120.56, "378.6" => 121.11,
        "373.1" => 121.67, "367.7" => 122.22, "362.4" => 122.78,
        "357.1" => 123.33, "352.0" => 123.89, "347.0" => 124.44,
        "342.0" => 125.00, "337.1" => 125.56, "332.3" => 126.11,
        "327.6" => 126.67, "323.0" => 127.22, "318.4" => 127.78,
        "314.0" => 128.33, "309.6" => 128.89, "305.2" => 129.44,
        "301.0" => 130.00, "296.8" => 130.56, "292.7" => 131.11,
        "288.6" => 131.67, "284.6" => 132.22, "280.7" => 132.78,
        "276.8" => 133.33, "273.0" => 133.89, "269.3" => 134.44,
        "265.6" => 135.00, "262.0" => 135.56, "258.5" => 136.11,
        "255.0" => 136.67, "251.5" => 137.22, "248.1" => 137.78,
        "244.8" => 138.33, "241.5" => 138.89, "238.3" => 139.44,
        "235.1" => 140.00, "232.0" => 140.56, "228.9" => 141.11,
        "225.9" => 141.67, "222.9" => 142.22, "220.0" => 142.78,
        "217.1" => 143.33, "214.2" => 143.89, "211.4" => 144.44,
        "208.7" => 145.00, "206.0" => 145.56, "203.3" => 146.11,
        "200.7" => 146.67, "198.1" => 147.22, "195.5" => 147.78,
        "193.0" => 148.33, "190.5" => 148.89, "188.1" => 149.44,
        "185.7" => 150.00, "183.3" => 150.56, "181.0" => 151.11,
        "178.7" => 151.67, "176.5" => 152.22, "174.3" => 152.78,
        "172.1" => 153.33, "169.9" => 153.89, "167.8" => 154.44,
        "165.7" => 155.00, "163.6" => 155.56, "161.6" => 156.11,
        "159.6" => 156.67, "157.6" => 157.22, "155.7" => 157.78,
        "153.8" => 158.33, "151.9" => 158.89, "150.0" => 159.44,
        "148.2" => 160.00, "146.4" => 160.56, "144.6" => 161.11,
        "142.9" => 161.67, "141.2" => 162.22, "139.5" => 162.78,
        "137.8" => 163.33, "136.1" => 163.89, "134.5" => 164.44,
        "132.9" => 165.00, "131.3" => 165.56, "129.8" => 166.11,
        "128.2" => 166.67, "126.7" => 167.22, "125.2" => 167.78,
        "123.7" => 168.33, "122.3" => 168.89, "120.9" => 169.44,
        "119.5" => 170.00, "118.1" => 170.56, "116.7" => 171.11,
        "115.3" => 171.67, "114.0" => 172.22, "112.7" => 172.78,
        "111.4" => 173.33, "110.1" => 173.89, "108.9" => 174.44,
        "107.6" => 175.00, "106.4" => 175.56, "105.2" => 176.11,
        "104.0" => 176.67, "102.8" => 177.22, "101.6" => 177.78,
        "100.5" => 178.33, "99.4" => 178.89, "98.3" => 179.44,
        "97.2" => 180.00, "96.1" => 180.56, "95.0" => 181.11,
        "93.9" => 181.67, "92.9" => 182.22, "91.9" => 182.78,
        "90.9" => 183.33, "89.9" => 183.89, "88.9" => 184.44,
        "87.9" => 185.00, "86.9" => 185.56, "86.0" => 186.11,
        "85.1" => 186.67, "84.1" => 187.22, "83.2" => 187.78,
        "82.3" => 188.33, "81.4" => 188.89, "80.6" => 189.44,
        "79.7" => 190.00, "78.8" => 190.56, "78.0" => 191.11,
        "77.2" => 191.67, "76.3" => 192.22, "75.5" => 192.78,
        "74.7" => 193.33, "74.0" => 193.89, "73.2" => 194.44,
        "72.4" => 195.00, "71.6" => 195.56, "70.9" => 196.11,
        "70.2" => 196.67, "69.4" => 197.22, "68.7" => 197.78,
        "68.0" => 198.33, "67.3" => 198.89, "66.6" => 199.44,
        "65.9" => 200.00, "65.2" => 200.56, "64.6" => 201.11,
        "63.9" => 201.67, "63.2" => 202.22, "62.6" => 202.78,
        "62.0" => 203.33,
    );
    /**
    * Disconnects from the database
    *
    * @param array  $data    The servers to use
    * @param object &$device The device we are attached to
    */
    public function __construct($data, &$device)
    {
        $this->default["id"] = 2;
        $this->default["type"] = "imcSolar";
        parent::__construct($data, $device);
        // This takes care of The older sensors with the 100k bias resistor
    }
    /**
    * Converts resistance to temperature for IMCSolar thermistor
    * 10K thermistor.
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    *                      and the last one
    *
    * @return float The temperature in degrees C.
    */
    function getReading($A, $deltaT = 0)
    {
        if ($this->dataType == DeviceSensorBase::TYPE_IGNORE) {
            return null;
        }
        $Bias = $this->getExtra(0);
        $ohms = $this->getResistance($A, $Bias);
        $T    = $this->tableInterpolate($ohms);
        if (is_null($T)) {
            return null;
        }
        // tableInterpolate forces the result to be in range, or returns null
        $T = round($T, 4);
        return $T;
    }
    /******************************************************************
     ******************************************************************
     ********  The following are input modification functions  ********
     ******************************************************************
     ******************************************************************/

}
?>
