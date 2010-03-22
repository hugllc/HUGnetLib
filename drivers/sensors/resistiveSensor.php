<?php
/**
 * Sensor driver for resistive sensors
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
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2010 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
if (!class_exists('resistiveSensor')) {
    include_once HUGNET_INCLUDE_PATH."/drivers/units/temperatureUnits.php";

    /**
    * class for dealing with resistive sensors.
    *
    *  This class deals with all resistive sensors.  This includes thermistors,
    *  resistive door sensors, and other resistors.
    *
    * @category   Drivers
    * @package    HUGnetLib
    * @subpackage Sensors
    * @author     Scott Price <prices@hugllc.com>
    * @copyright  2007-2010 Hunt Utilities Group, LLC
    * @copyright  2009 Scott Price
    * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
    * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
    */
    class ResistiveSensor extends SensorBase
    {
        /** @var float Moisture red zone % */
        protected $Mr = 18;
        /** @var float Moisture yellow zone % */
        protected $My = 12;

        /** @var This is to register the class */
        public static $registerPlugin = array(
            "Name" => "resistiveSensor",
            "Type" => "sensor",
        );
        /**
        * This is the array of sensor information.
        *
        * The BC Components thermistor
        * is in here twice for historical compatability.  There are some endpoints
        * that still have sensor type 0 with 100k bias resistors.  This first entry
        * is to take care of those, even though this new system is flexible enough
        * to deal with the change in bias resistors on the same sensor type.  The
        * two entries should be kept identical except for the first extraDefault,
        * which should be 100 under the 0x00 type and 10 under the 0x02 type.
        */
        public $sensors = array(
            0x00 => array(
                'BCTherm2322640' => array(
                    "longName" => "BC Components Thermistor #2322640 ",
                    "unitType" => "Temperature",
                    "validUnits" => array('&#176;F', '&#176;C'),
                    "function" => "BCTherm238164066103",
                    "storageUnit" => '&#176;C',
                    "unitModes" => array(
                        '&#176;C' => 'raw,diff',
                        '&#176;F' => 'raw,diff',
                   ),
                    "extraText" => array(
                        "Bias Resistor in k Ohms",
                        "Thermistor Value @25C"
                    ),
                    "extraDefault" => array(100, 10),
               ),
           ),
            0x01 => array(
                'BaleMoistureV1' => array(
                    "longName" => "Bale Moisture V1",
                    "unitType" => "Bale Moisture",
                    "validUnits" => array('%'),
                    "function" => "getMoistureV1",
                    "storageUnit" => '%',
                    "unitModes" => array(
                        '%' => 'raw,diff',
                   ),
                    "extraText" => array(
                        "Bias Resistor in k Ohms",
                        "Red Zone resistance in Ohms",
                        "Yellow Zone resistance in Ohms"
                    ),
                    "extraDefault" => array(100, 10000, 100000),
               ),
           ),
            0x02 => array(
                'BCTherm2322640' => array(
                    "longName" => "BC Components Thermistor #2322640 ",
                    "unitType" => "Temperature",
                    "validUnits" => array('&#176;F', '&#176;C'),
                    "function" => "BCTherm238164066103",
                    "storageUnit" => '&#176;C',
                    "unitModes" => array(
                        '&#176;C' => 'raw,diff',
                        '&#176;F' => 'raw,diff',
                   ),
                    "extraText" => array(
                        "Bias Resistor in k Ohms",
                        "Thermistor Value @25C"
                    ),
                    "extraDefault" => array(10, 10),
               ),
               'imcSolar' => array(
                    "longName" => "IMC Solar Sensor",
                    "unitType" => "Temperature",
                    "validUnits" => array('&#176;F', '&#176;C'),
                    "function" => "imcSolar",
                    "storageUnit" => '&#176;C',
                    "unitModes" => array(
                        '&#176;C' => 'raw,diff',
                        '&#176;F' => 'raw,diff',
                    ),
                    "extraText" => array("Bias Resistor in k Ohms"),
                    "extraDefault" => array(100),
                ),
               'B57560G0103F000' => array(
                    "longName" => "EPCOS B57560G0103F000",
                    "unitType" => "Temperature",
                    "validUnits" => array('&#176;F', '&#176;C'),
                    "function" => "B57560G0103F000",
                    "storageUnit" => '&#176;C',
                    "unitModes" => array(
                        '&#176;C' => 'raw,diff',
                        '&#176;F' => 'raw,diff',
                    ),
                    "extraText" => array("Bias Resistor in k Ohms"),
                    "extraDefault" => array(10),
                ),
                'resisDoor' => array(
                    "longName" => "Resistive Door Sensor",
                    "unitType" => "Door",
                    "validUnits" => array('%'),
                    "function" => "resisDoor",
                    "storageUnit" => '%',
                    "unitModes" => array(
                        '%' => 'raw',
                   ),
                    "extraText" => array(
                        "Bias Resistor in kOhms",
                        "Fixed Resistor in kOhms",
                        "Switched Resistor in kOhms"
                    ),
                    "extraDefault" => array(10,10,10),
               ),
                'potDirection' => array(
                    "longName" => "POT Direction Sensor",
                    "unitType" => "Direction",
                    "validUnits" => array('&#176;'),
                    "function" => "potDir",
                    "storageUnit" => '&#176;',
                    "unitModes" => array(
                        '&#176;' => 'raw',
                   ),
                    "extraText" => array(
                        "POT Resistance in kOhms",
                        "Direction 1 in degrees",
                        "Resistance 1 in kOhms",
                        "Direction 2 in degrees",
                        "Resistance 2 in kOhms",
                    ),
                    "extraDefault" => array(25,0, 0, 180, 25),
               ),
           ),
            0x03 => array(
                'BaleMoistureV2' => array(
                    "longName" => "Bale Moisture V2",
                    "unitType" => "Bale Moisture",
                    "validUnits" => array('%'),
                    "function" => "getMoistureV2",
                    "storageUnit" => '%',
                    "unitModes" => array(
                        '%' => 'raw,diff',
                   ),
                    "extraText" => array(
                        "Bias Resistor in k Ohms",
                        "Red Zone resistance in k Ohms",
                        "Yellow Zone resistance in k Ohms"
                    ),
                    "extraDefault" => array(1000, 10, 1000),
               ),
           ),
        );
        /** @var array The table for IMC Sensors */
        private $_imcSolarTable = array(
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
        /** This is the lookup table for the B57560G0103F000 thermistor */
        private $_B57560G0103F000table = array(
            "519910" => -55, "379890" => -50, "280700" => -45,
            "209600" => -40, "158090" => -35, "120370" => -30,
            "92484" => -25, "71668" => -20, "55993" => -15,
            "44087" => -10, "34971" => -5, "27936" => 0,
            "22468" => 5, "18187" => 10, "14813" => 15,
            "12136" => 20, "10000" => 25, "8284" => 30,
            "6899" => 35, "5774" => 40, "4856" => 45,
            "4103" => 50, "3482" => 55, "2967" => 60,
            "2539" => 65, "2182" => 70, "1882" => 75,
            "1629" => 80, "1415" => 85, "1234" => 90,
            "1079" => 95, "946.6" => 100, "833.1" => 105,
            "735.5" => 110, "651.1" => 115, "578.1" => 120,
            "514.6" => 125, "459.4" => 130, "411.1" => 135,
            "368.8" => 140, "331.6" => 145, "298.9" => 150,
            "270.0" => 155, "244.4" => 160, "221.7" => 165,
            "201.6" => 170, "183.6" => 175, "167.6" => 180,
            "153.3" => 185, "140.4" => 190, "128.9" => 195,
            "118.5" => 200, "109.1" => 205, "100.7" => 210,
            "93.01" => 215, "86.08" => 220, "79.78" => 225,
            "74.05" => 230, "68.83" => 235, "64.08" => 240,
            "59.73" => 245, "55.75" => 250, "52.11" => 255,
            "48.76" => 260, "45.69" => 265, "42.87" => 270,
            "40.26" => 275, "37.86" => 280, "35.64" => 285,
            "33.59" => 290, "31.70" => 295, "29.94" => 300,
        );

        /**
        * Converts a raw AtoD reading into resistance
        *
        * This function takes in the AtoD value and returns the calculated
        * resistance of the sensor.  It does this using a fairly complex
        * formula.  This formula and how it was derived is detailed in
        *
        * @param int   $A    Integer The AtoD reading
        * @param int   $TC   Integer The time constant used to get the reading
        * @param float $Bias Float The bias resistance in kOhms
        * @param int   $Tf   See {@link sensor_base::$Tf}
        * @param int   $D    See {@link sensor_base::$D}
        * @param int   $s    See {@link sensor_base::$s}
        * @param int   $Am   See {@link sensor_base::$Am}
        *
        * @return The resistance corresponding to the values given in k Ohms
        */
        function getResistance(
            $A,
            $TC,
            $Bias,
            $Tf = null,
            $D = null,
            $s = null,
            $Am = null
        ) {
            if (is_null($Tf)) {
                $Tf = $this->Tf;
            }
            if (is_null($D)) {
                $D = $this->D;
            }
            if (is_null($s)) {
                $s = $this->s;
            }
            if (is_null($Am)) {
                $Am = $this->Am;
            }
            if ($D == 0) {
                return 0.0;
            }
            $Den = ((($Am*$s*$TC*$Tf)/$D) - $A);
            if (($Den == 0) || !is_numeric($Den)) {
                $Den = 1.0;
            }
            $R = (float)($A*$Bias)/$Den;
            return round($R, 4);
        }

        /**
        * Converts a raw AtoD reading into resistance
        *
        * This function takes in the AtoD value and returns the calculated
        * resistance that the sweep is at.  It does this using a fairly complex
        * formula.  This formula and how it was derived is detailed in
        *
        * @param int   $A  Integer The AtoD reading
        * @param int   $TC Integer The time constant used to get the reading
        * @param float $R  Float The overall resistance in kOhms
        * @param int   $Tf See {@link sensor_base::$Tf}
        * @param int   $D  See {@link sensor_base::$D}
        * @param int   $s  See {@link sensor_base::$s}
        * @param int   $Am See {@link sensor_base::$Am}
        *
        * @return The resistance corresponding to the values given in k Ohms
        */
        function getSweep(
            $A,
            $TC,
            $R,
            $Tf = null,
            $D = null,
            $s = null,
            $Am = null
        ) {
            if (is_null($Tf)) {
                $Tf = $this->Tf;
            }
            if (is_null($D)) {
                $D = $this->D;
            }
            if (is_null($s)) {
                $s = $this->s;
            }
            if (is_null($Am)) {
                $Am = $this->Am;
            }

            if ($D == 0) {
                return 0.0;
            }
            $Den = (($Am*$s*$TC*$Tf)/$D);
            if (($Den == 0) || !is_numeric($Den)) {
                $Den = 1.0;
            }
            $Rs = (float)(($A*$R)/$Den);
            if ($Rs > $R) {
                return round($R, 4);
            }
            if ($Rs < 0) {
                return 0.0;
            }
            return round($Rs, 4);
        }

        /**
        * Converts resistance to temperature for BC Components #2322 640 66103
        * 10K thermistor.
        *
        * <b>BC Components #2322 640 series</b>
        *
        * This function implements the formula in $this->BCThermInterpolate
        * for a is from BCcomponents PDF file for thermistor
        * #2322 640 series datasheet on page 6.
        *
        * <b>Thermistors available:</b>
        *
        * -# 10K Ohm BC Components #2322 640 66103. This is defined as thermistor
        * 0 in the type code.
        *     - R0 10
        *     - A 3.354016e-3
        *     - B 2.569355e-4
        *     - C 2.626311e-6
        *     - D 0.675278e-7
        *
        * @param int   $A      Output of the A to D converter
        * @param array $sensor The sensor information array
        * @param int   $TC     The time constant
        * @param mixed $extra  Extra sensor information
        * @param float $deltaT The time delta in seconds between this record
        *                      and the last one
        * @param array $cal    The calibration array
        *
        * @return float The temperature in degrees C.
        */
        function bcTherm238164066103(
            $A,
            $sensor,
            $TC,
            $extra,
            $deltaT = null,
            $cal=array()
        ) {
            if (!is_array($extra)) {
                $extra = array();
            }
            $Bias      = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $baseTherm = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $ohms      = $this->getResistance($A, $TC, $Bias);
            $T         = $this->_BcTherm2322640Interpolate(
                $ohms,
                $baseTherm,
                3.354016e-3,
                2.569355e-4,
                2.626311e-6,
                0.675278e-7
            );

            if (is_null($T)) {
                return null;
            }
            if ($T > 150) {
                return null;
            }
            if ($T < -40) {
                return null;
            }
            $T = round($T, 4);
            return $T;
        }

        /**
        * This takes two direction/resistance pairs and then assignes a direction
        * to any given resistance.
        *
        * It handles both wrap around cases correctly.
        *
        * @param int   $A      Output of the A to D converter
        * @param array $sensor The sensor information array
        * @param int   $TC     The time constant
        * @param mixed $extra  Extra sensor information
        * @param float $deltaT The time delta in seconds between this record
        *                      and the last one
        * @param array $cal    The calibration array
        *
        * @return float The direction.
        */
        function potDir($A, $sensor, $TC, $extra, $deltaT = null, $cal=array())
        {
            if (!is_array($extra)) {
                $extra = array();
            }
            $RTotal = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $dir1   = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $R1     = (empty($extra[2])) ? $sensor['extraDefault'][2] : $extra[2];
            $dir2   = (empty($extra[3])) ? $sensor['extraDefault'][3] : $extra[3];
            $R2     = (empty($extra[4])) ? $sensor['extraDefault'][4] : $extra[4];
            $R      = $this->getSweep($A, $TC, $RTotal);

            if (is_null($R) || ($dir1 == $dir2) || ($R1 == $R2) || ($RTotal == 0)) {
                return null;
            }

            $m = ($dir1 - $dir2) / ($R1 - $R2);
            $b = $dir2 - ($m * $R2);
            $dir = ($m * $R) + $b;

            while ($dir > 360) {
                $dir -= 360;
            }
            while ($dir < 0) {
                $dir += 360;
            }
            return round($dir, 4);
        }

        /**
        * This formula is from BCcomponents PDF file for the
        * # 2322 640 thermistor series on page 6.  See the data sheet for
        * more information.
        *
        * This function should be called with the values set for the specific
        * thermistor that is used.  See eDEFAULT::Therm0Interpolate as an example.
        *
        * @param float $R  The current resistance of the thermistor in kOhms
        * @param float $R0 The resistance of the thermistor at 25C in kOhms
        * @param float $A  Thermistor Constant A (From datasheet)
        * @param float $B  Thermistor Constant B (From datasheet)
        * @param float $C  Thermistor Constant C (From datasheet)
        * @param float $D  Thermistor Constant D (From datasheet)
        *
        * @return float The Temperature in degrees C
        */
        private function _bcTherm2322640Interpolate($R, $R0, $A, $B, $C, $D)
        {
            // This gets out bad values
            if ($R <= 0) {
                return null;
            }
            if ($R0 == 0) {
                return null;
            }
            $T  = $A;
            $T += $B * log($R/$R0);
            $T += $C * pow(log($R/$R0), 2);
            $T += $D * pow(log($R/$R0), 3);
            $T  = pow($T, -1);

            $T -= 273.15;
            return($T);
        }
        /**
        * Converts resistance to temperature for BC Components #2322 640 66103
        * 10K thermistor.
        *
        * @param int   $A      Output of the A to D converter
        * @param array $sensor The sensor information array
        * @param int   $TC     The time constant
        * @param mixed $extra  Extra sensor information
        * @param float $deltaT The time delta in seconds between this record
        *                      and the last one
        * @param array $cal    The calibration array
        *
        * @return float The temperature in degrees F.
        */
        function imcSolar($A, $sensor, $TC, $extra, $deltaT = null, $cal=array())
        {
            if (!is_array($extra)) {
                $extra = array();
            }
            $Bias = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $ohms = $this->getResistance($A, $TC, $Bias);
            $T    = $this->_tableInterpolate($ohms, "_imcSolarTable", 337045, 62);
            if (is_null($T)) {
                return null;
            }
            // _tableInterpolate forces the result to be in range, or returns null
            $T = round($T, 4);
            return $T;
        }
        /**
        * Converts resistance to temperature for EPCOS B57560G0103F000
        * 10K thermistor.
        *
        * @param int   $A      Output of the A to D converter
        * @param array $sensor The sensor information array
        * @param int   $TC     The time constant
        * @param mixed $extra  Extra sensor information
        * @param float $deltaT The time delta in seconds between this record
        *                      and the last one
        * @param array $cal    The calibration array
        *
        * @return float The temperature in degrees C.
        */
        function b57560G0103F000(
            $A,
            $sensor,
            $TC,
            $extra,
            $deltaT = null,
            $cal=array()
        ) {
            if (!is_array($extra)) {
                $extra = array();
            }
            $Bias = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $ohms = $this->getResistance($A, $TC, $Bias);
            $T    = $this->_tableInterpolate(
                $ohms,
                "_B57560G0103F000table",
                519910,
                29.94
            );
            if (is_null($T)) {
                return null;
            }
            // _tableInterpolate forces the result to be in range, or returns null
            $T = round($T, 4);
            return $T;
        }
        /**
        * This function should be called with the values set for the specific
        * thermistor that is used.  See eDEFAULT::Therm0Interpolate as an example.
        *
        * @param float  $R   The current resistance of the thermistor in k ohms
        * @param string $var The class variable that holds the table
        * @param float  $max The maximum resistance in the table
        * @param float  $min The minimum resistance in the table
        *
        * @return float The Temperature in degrees C
        */
        private function _tableInterpolate($R, $var, $max, $min)
        {
            $R *= 1000;
            if (($R < $min) || ($R > $max)) {
                return null;
            }
            $table = &$this->$var;
            foreach ($table as $ohm => $temp) {
                $ohm  = $ohm;
                $last = $ohm;
                if ((float)$ohm < $R) {
                    break;
                }
                $next = $ohm;
            }
            $T     = $table[$last];
            $fract = ($next - $R) / ($next - $last);
            $T    += $fract;
            return $T;
        }

        /**
        * This function calculates the open percentage based on the resistance seen.
        *
        * This sensor expects the following extras:
        *  0. The bias resistor
        *  1. The fixed resistor
        *  2. The switched resistor
        *
        * @param int   $A      The incoming value
        * @param array $sensor The sensor setup array
        * @param int   $TC     The time constant
        * @param mixed $extra  Extra parameters for the sensor
        * @param float $deltaT The time delta in seconds between this record
        *                      and the last one
        * @param array $cal    The calibration array
        *
        * @return float The percentage of time the door is open
        */
        function resisDoor($A, $sensor, $TC, $extra, $deltaT = null, $cal=array())
        {
            $Bias  = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $Fixed = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            if ($Fixed <= 0) {
                return null;
            }
            $Switched = (empty($extra[2])) ? $sensor['extraDefault'][2] : $extra[2];
            if ($Switched <= 0) {
                return null;
            }
            $R  = $this->getResistance($A, $TC, $Bias);
            $R -= $Fixed;
            // Got something wrong here.  We shouldn't have a negative resistance.
            if ($R < 0) {
                return null;
            }
            $perc = ($R / $Switched) * 100;
            // We need to limit this to between 0 and  100.
            // It can't be open more than all the time.
            // It can't be open less than none of the time.
            if (($perc < 0) || ($perc > 100)) {
                return null;
            }
            return round($perc, 2);
        }

        /**
        * This function calculates the open percentage based on the resistance seen.
        *
        * This sensor expects the following extras:
        *  0. The bias resistor
        *  1. The red zone resistance
        *  2. The yellow zone resistance
        *
        * @param float $A      The incoming value
        * @param array $sensor The sensor setup array
        * @param int   $TC     The time constant
        * @param mixed $extra  Extra parameters for the sensor
        * @param float $deltaT The time delta in seconds between this record
        *                      and the last one
        * @param array $cal    The calibration array
        *
        * @return float The percentage of time the door is open
        */
        function getMoistureV2(
            $A,
            $sensor,
            $TC,
            $extra,
            $deltaT = null,
            $cal=array()
        ) {
            $Bias = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $Rr   = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $Ry   = (empty($extra[2])) ? $sensor['extraDefault'][2] : $extra[2];
            if ($Ry <= $Rr) {
                return null;
            }
            $R = $this->getResistance($A, $TC, $Bias, 1, 1, 64);
            $M = $R;
            return $M;
        }

        /**
        * This function calculates the open percentage based on the resistance seen.
        *
        * This is for V1 of the moisture sensor.  No more of these will be made.
        *
        * This sensor expects the following extras:
        *  0. The bias resistor
        *  1. The red zone resistance
        *  2. The yellow zone resistance
        *
        * It is not well documented.  It seems to contain the formula:
        *  - B = (My - Mr) / (log(Ry) - log(Rr))
        *  - A = Mr - (B * log(Rr))
        *  - M = A + (B * log(R));
        * where:
        * - M = Moisture (%)
        * - Mr = Minimum % for red zone (bad)
        * - My = Minimum % for yellow zone (marginal)
        * - Rr = Maximum Ohms for red zone (bad)
        * - Ry = Maximum Ohms for yellow zone (marginal)
        * - A = ???
        * - B = ???
        *
        * I think this formula is based on logrythmic curves with the points
        * (Ry, My) and (Rr, Mr).  Resistance and Moiture have an inverse
        * relationship.
        *
        * @param float $A      The incoming value
        * @param array $sensor The sensor setup array
        * @param int   $TC     The time constant
        * @param mixed $extra  Extra parameters for the sensor
        * @param float $deltaT The time delta in seconds between this record
        *                      and the last one
        * @param array $cal    The calibration array
        *
        * @return float The percentage of time the door is open
        */
        function getMoistureV1(
            $A,
            $sensor,
            $TC,
            $extra,
            $deltaT = null,
            $cal=array()
        ) {
            $Bias = (empty($extra[0])) ? $sensor['extraDefault'][0] : $extra[0];
            $Rr   = (empty($extra[1])) ? $sensor['extraDefault'][1] : $extra[1];
            $Ry   = (empty($extra[2])) ? $sensor['extraDefault'][2] : $extra[2];
            if ($Ry <= $Rr) {
                return null;
            }
            $R = $this->getResistance($A, 1, $Bias);

            if ($R == 0) {
                return(35.0);
            }
            //$R is coming in k Ohms.  We need Ohms.
            $R   = $R * 1000;
            $num = $this->My - $this->Mr;
            $den = log($Ry) - log($Rr);
            // $den is saved from ever being 0 because we abort the routine if
            // $Ry == $Rr.  That is the only way $den could equal 0.
            $B = $num / $den;
            $A = $this->Mr - ($B * log($Rr));
            $M = $A + ($B * log($R));

            if ($M > 35) {
                return null;
            }
            return round($M, 2);
        }

    }
}

?>
