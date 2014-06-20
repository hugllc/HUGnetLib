<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2013 Hunt Utilities Group, LLC
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
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This is the HUGnet namespace */
namespace HUGnet\devices\inputTable\drivers\aduc;
/** This keeps this file from being included unless HUGnetSystem.php is included */
defined('_HUGNET') or die('HUGnetSystem not found');
/** This is my base class */
require_once dirname(__FILE__)."/../../DriverADuC.php";

/**
 * Sensor driver for direct voltage reading on the ADuC706x
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2013 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.10.2
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCUSSensorRTD extends \HUGnet\devices\inputTable\DriverADuC
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "USSensor Platinum Temperature Sensor RTD",
        "shortName" => "ADUCUSSensorRTD",
        "unitType" => "Temperature",
        "storageUnit" => '&#176;C',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array("Bias Resistor (Ohms)"),
        "extraDesc" => array(
            "The other resistor in the voltage divider with the RTD",
        ),
        "extraNames" => array(
            "r" => 0,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(10),
        "extraDefault" => array(10000),
        "maxDecimals" => 4,
        "inputSize" => 4,
        "requires" => array("AI"),
        "provides" => array("DC"),
    );
    /** @var array The lookup table */
    private $_valueTable = array(
        "803.14" => -50, "807.11" => -49, "811.07" => -48, "815.03" => -47,
        "819.00" => -46, "822.96" => -45, "826.92" => -44, "830.88" => -43,
        "834.83" => -42, "838.79" => -41, "842.74" => -40, "846.70" => -39,
        "850.65" => -38, "854.60" => -37, "858.55" => -36, "862.50" => -35,
        "866.45" => -34, "870.40" => -33, "874.34" => -32, "878.29" => -31,
        "882.23" => -30, "886.17" => -29, "890.11" => -28, "894.05" => -27,
        "897.99" => -26, "901.93" => -25, "905.87" => -24, "909.80" => -23,
        "913.74" => -22, "917.67" => -21, "921.60" => -20, "925.53" => -19,
        "929.46" => -18, "933.39" => -17, "937.32" => -16, "941.25" => -15,
        "945.17" => -14, "949.09" => -13, "953.02" => -12, "956.94" => -11,
        "960.86" => -10, "964.78" => -9, "968.70" => -8, "972.61" => -7,
        "976.53" => -6, "980.44" => -5, "984.36" => -4, "988.27" => -3,
        "992.18" => -2, "996.09" => -1, "1000.00" => 0, "1003.91" => 1,
        "1007.81" => 2, "1011.72" => 3, "1015.62" => 4, "1019.53" => 5,
        "1023.43" => 6, "1027.33" => 7, "1031.23" => 8, "1035.13" => 9,
        "1039.03" => 10, "1042.92" => 11, "1046.82" => 12, "1050.71" => 13,
        "1054.60" => 14, "1058.49" => 15, "1062.38" => 16, "1066.27" => 17,
        "1070.16" => 18, "1074.05" => 19, "1077.94" => 20, "1081.82" => 21,
        "1085.70" => 22, "1089.59" => 23, "1093.47" => 24, "1097.35" => 25,
        "1101.23" => 26, "1105.10" => 27, "1108.98" => 28, "1112.86" => 29,
        "1116.73" => 30, "1120.60" => 31, "1124.47" => 32, "1128.35" => 33,
        "1132.21" => 34, "1136.08" => 35, "1139.95" => 36, "1143.82" => 37,
        "1147.68" => 38, "1151.55" => 39, "1155.41" => 40, "1159.27" => 41,
        "1163.13" => 42, "1166.99" => 43, "1170.85" => 44, "1174.70" => 45,
        "1178.56" => 46, "1182.41" => 47, "1186.27" => 48, "1190.12" => 49,
        "1193.97" => 50, "1197.82" => 51, "1201.67" => 52, "1205.52" => 53,
        "1209.36" => 54, "1213.21" => 55, "1217.05" => 56, "1220.90" => 57,
        "1224.74" => 58, "1228.58" => 59, "1232.42" => 60, "1236.26" => 61,
        "1240.09" => 62, "1243.93" => 63, "1247.77" => 64, "1251.60" => 65,
        "1255.43" => 66, "1259.26" => 67, "1263.09" => 68, "1266.92" => 69,
        "1270.75" => 70, "1274.58" => 71, "1278.40" => 72, "1282.23" => 73,
        "1286.05" => 74, "1289.87" => 75, "1293.70" => 76, "1297.52" => 77,
        "1301.33" => 78, "1305.15" => 79, "1308.97" => 80, "1312.78" => 81,
        "1316.60" => 82, "1320.41" => 83, "1324.22" => 84, "1328.03" => 85,
        "1331.84" => 86, "1335.65" => 87, "1339.46" => 88, "1343.26" => 89,
        "1347.07" => 90, "1350.87" => 91, "1354.68" => 92, "1358.48" => 93,
        "1362.28" => 94, "1366.08" => 95, "1369.87" => 96, "1373.67" => 97,
        "1377.47" => 98, "1381.26" => 99, "1385.06" => 100, "1388.85" => 101,
        "1392.64" => 102, "1396.43" => 103, "1400.22" => 104, "1404.00" => 105,
        "1407.79" => 106, "1411.58" => 107, "1415.36" => 108, "1419.14" => 109,
        "1422.93" => 110, "1426.71" => 111, "1430.49" => 112, "1434.26" => 113,
        "1438.04" => 114, "1441.82" => 115, "1445.59" => 116, "1449.37" => 117,
        "1453.14" => 118, "1456.91" => 119, "1460.68" => 120, "1464.45" => 121,
        "1468.22" => 122, "1471.98" => 123, "1475.75" => 124, "1479.51" => 125,
        "1483.28" => 126, "1487.04" => 127, "1490.80" => 128, "1494.56" => 129,
        "1498.32" => 130, "1502.08" => 131, "1505.83" => 132, "1509.59" => 133,
        "1513.34" => 134, "1517.10" => 135, "1520.85" => 136, "1524.60" => 137,
        "1528.35" => 138, "1532.10" => 139, "1535.84" => 140, "1539.59" => 141,
        "1543.33" => 142, "1547.08" => 143, "1550.82" => 144, "1554.56" => 145,
        "1558.30" => 146, "1562.04" => 147, "1565.78" => 148, "1569.52" => 149,
        "1573.25" => 150, "1576.99" => 151, "1580.72" => 152, "1584.45" => 153,
        "1588.18" => 154, "1591.91" => 155, "1595.64" => 156, "1599.37" => 157,
        "1603.09" => 158, "1606.82" => 159, "1610.54" => 160, "1614.27" => 161,
        "1617.99" => 162, "1621.71" => 163, "1625.43" => 164, "1629.15" => 165,
        "1632.86" => 166, "1636.58" => 167, "1640.30" => 168, "1644.01" => 169,
        "1647.72" => 170, "1651.43" => 171, "1655.14" => 172, "1658.85" => 173,
        "1662.56" => 174, "1666.27" => 175, "1669.97" => 176, "1673.68" => 177,
        "1677.38" => 178, "1681.08" => 179, "1684.78" => 180, "1688.48" => 181,
        "1692.18" => 182, "1695.88" => 183, "1699.58" => 184, "1703.27" => 185,
        "1706.96" => 186, "1710.66" => 187, "1714.35" => 188, "1718.04" => 189,
        "1721.73" => 190, "1725.42" => 191, "1729.10" => 192, "1732.79" => 193,
        "1736.48" => 194, "1740.16" => 195, "1743.84" => 196, "1747.52" => 197,
        "1751.20" => 198, "1754.88" => 199, "1758.56" => 200, "1762.24" => 201,
        "1765.91" => 202, "1769.59" => 203, "1773.26" => 204, "1776.93" => 205,
        "1780.60" => 206, "1784.27" => 207, "1787.94" => 208, "1791.61" => 209,
        "1795.28" => 210, "1798.94" => 211, "1802.60" => 212, "1806.27" => 213,
        "1809.93" => 214, "1813.59" => 215, "1817.25" => 216, "1820.91" => 217,
        "1824.56" => 218, "1828.22" => 219, "1831.88" => 220, "1835.53" => 221,
        "1839.18" => 222, "1842.83" => 223, "1846.48" => 224, "1850.13" => 225,
        "1853.78" => 226, "1857.43" => 227, "1861.07" => 228, "1864.72" => 229,
        "1868.36" => 230, "1872.00" => 231, "1875.64" => 232, "1879.28" => 233,
        "1882.92" => 234, "1886.56" => 235, "1890.19" => 236, "1893.83" => 237,
        "1897.46" => 238, "1901.10" => 239, "1904.73" => 240, "1908.36" => 241,
        "1911.99" => 242, "1915.62" => 243, "1919.24" => 244, "1922.87" => 245,
        "1926.49" => 246, "1930.12" => 247, "1933.74" => 248, "1937.36" => 249,
        "1940.98" => 250, "1944.60" => 251, "1948.22" => 252, "1951.83" => 253,
        "1955.45" => 254, "1959.06" => 255, "1962.68" => 256, "1966.29" => 257,
        "1969.90" => 258, "1973.51" => 259, "1977.12" => 260, "1980.73" => 261,
        "1984.33" => 262, "1987.94" => 263, "1991.54" => 264, "1995.14" => 265,
        "1998.75" => 266, "2002.35" => 267, "2005.95" => 268, "2009.54" => 269,
        "2013.14" => 270, "2016.74" => 271, "2020.33" => 272, "2023.93" => 273,
        "2027.52" => 274, "2031.11" => 275, "2034.70" => 276, "2038.29" => 277,
        "2041.88" => 278, "2045.46" => 279, "2049.05" => 280, "2052.63" => 281,
        "2056.22" => 282, "2059.80" => 283, "2063.38" => 284, "2066.96" => 285,
        "2070.54" => 286, "2074.11" => 287, "2077.69" => 288, "2081.27" => 289,
        "2084.84" => 290, "2088.41" => 291, "2091.98" => 292, "2095.55" => 293,
        "2099.12" => 294, "2102.69" => 295, "2106.26" => 296, "2109.82" => 297,
        "2113.39" => 298, "2116.95" => 299, "2120.52" => 300, "2124.08" => 301,
        "2127.64" => 302, "2131.20" => 303, "2134.75" => 304, "2138.31" => 305,
        "2141.87" => 306, "2145.42" => 307, "2148.97" => 308, "2152.52" => 309,
        "2156.08" => 310, "2159.62" => 311, "2163.17" => 312, "2166.72" => 313,
        "2170.27" => 314, "2173.81" => 315, "2177.36" => 316, "2180.90" => 317,
        "2184.44" => 318, "2187.98" => 319, "2191.52" => 320, "2195.06" => 321,
        "2198.60" => 322, "2202.13" => 323, "2205.67" => 324, "2209.20" => 325,
        "2212.73" => 326, "2216.26" => 327, "2219.79" => 328, "2223.32" => 329,
        "2226.85" => 330, "2230.38" => 331, "2233.90" => 332, "2237.43" => 333,
        "2240.95" => 334, "2244.47" => 335, "2247.99" => 336, "2251.51" => 337,
        "2255.03" => 338, "2258.55" => 339, "2262.06" => 340, "2265.58" => 341,
        "2269.09" => 342, "2272.60" => 343, "2276.12" => 344, "2279.63" => 345,
        "2283.14" => 346, "2286.64" => 347, "2290.15" => 348, "2293.66" => 349,
        "2297.16" => 350, "2300.66" => 351, "2304.17" => 352, "2307.67" => 353,
        "2311.17" => 354, "2314.67" => 355, "2318.16" => 356, "2321.66" => 357,
        "2325.16" => 358, "2328.65" => 359, "2332.14" => 360, "2335.64" => 361,
        "2339.13" => 362, "2342.62" => 363, "2346.10" => 364, "2349.59" => 365,
        "2353.08" => 366, "2356.56" => 367, "2360.05" => 368, "2363.53" => 369,
        "2367.01" => 370, "2370.49" => 371, "2373.97" => 372, "2377.45" => 373,
        "2380.93" => 374, "2384.40" => 375, "2387.88" => 376, "2391.35" => 377,
        "2394.82" => 378, "2398.29" => 379, "2401.76" => 380, "2405.23" => 381,
        "2408.70" => 382, "2412.17" => 383, "2415.63" => 384, "2419.10" => 385,
        "2422.56" => 386, "2426.02" => 387, "2429.48" => 388, "2432.94" => 389,
        "2436.40" => 390, "2439.86" => 391, "2443.31" => 392, "2446.77" => 393,
        "2450.22" => 394, "2453.67" => 395, "2457.13" => 396, "2460.58" => 397,
        "2464.03" => 398, "2467.47" => 399, "2470.92" => 400, "2474.37" => 401,
        "2477.81" => 402, "2481.25" => 403, "2484.70" => 404, "2488.14" => 405,
        "2491.58" => 406, "2495.02" => 407, "2498.45" => 408, "2501.89" => 409,
        "2505.33" => 410, "2508.76" => 411, "2512.19" => 412, "2515.62" => 413,
        "2519.06" => 414, "2522.48" => 415, "2525.91" => 416, "2529.34" => 417,
        "2532.77" => 418, "2536.19" => 419, "2539.62" => 420, "2543.04" => 421,
        "2546.46" => 422, "2549.88" => 423, "2553.30" => 424, "2556.72" => 425,
        "2560.13" => 426, "2563.55" => 427, "2566.96" => 428, "2570.38" => 429,
        "2573.79" => 430, "2577.20" => 431, "2580.61" => 432, "2584.02" => 433,
        "2587.43" => 434, "2590.83" => 435, "2594.24" => 436, "2597.64" => 437,
        "2601.05" => 438, "2604.45" => 439, "2607.85" => 440, "2611.25" => 441,
        "2614.65" => 442, "2618.04" => 443, "2621.44" => 444, "2624.83" => 445,
        "2628.23" => 446, "2631.62" => 447, "2635.01" => 448, "2638.40" => 449,
        "2641.79" => 450, "2645.18" => 451, "2648.57" => 452, "2651.95" => 453,
        "2655.34" => 454, "2658.72" => 455, "2662.10" => 456, "2665.48" => 457,
        "2668.86" => 458, "2672.24" => 459, "2675.62" => 460, "2679.00" => 461,
        "2682.37" => 462, "2685.74" => 463, "2689.12" => 464, "2692.49" => 465,
        "2695.86" => 466, "2699.23" => 467, "2702.60" => 468, "2705.97" => 469,
        "2709.33" => 470, "2712.70" => 471, "2716.06" => 472, "2719.42" => 473,
        "2722.78" => 474, "2726.14" => 475, "2729.50" => 476, "2732.86" => 477,
        "2736.22" => 478, "2739.57" => 479, "2742.93" => 480, "2746.28" => 481,
        "2749.63" => 482, "2752.98" => 483, "2756.33" => 484, "2759.68" => 485,
        "2763.03" => 486, "2766.38" => 487, "2769.72" => 488, "2773.07" => 489,
        "2776.41" => 490, "2779.75" => 491, "2783.09" => 492, "2786.43" => 493,
        "2789.77" => 494, "2793.11" => 495, "2796.44" => 496, "2799.78" => 497,
        "2803.11" => 498, "2806.44" => 499, "2809.78" => 500,
    );
    /**
    * Changes a raw reading into a output value
    *
    * @param int   $A      Output of the A to D converter
    * @param float $deltaT The time delta in seconds between this record
    * @param array &$data  The data from the other sensors that were crunched
    * @param mixed $prev   The previous value for this sensor
    *
    * @return mixed The value in whatever the units are in the sensor
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getReading(
        $A, $deltaT = 0, &$data = array(), $prev = null
    ) {
        bcscale(20);
        $Am    = pow(2, 23);
        $Rbias = $this->getExtra(0);

        $A = abs($A);
        if ($A == $Am) {
            return null;
        }
        $A *= $this->reference();
        $R = (float)(($A * $Rbias) / ($Am - $A));
        $T = $this->_tableInterpolate($R, $this->_valueTable);
        return round($T, $this->get('maxDecimals', 1));
    }
    /**
    * This function should be called with the values set for the specific
    * thermistor that is used.
    *
    * @param float $input  The input value
    * @param array &$table The table to look through.
    *
    * @return float The Temperature in degrees C
    */
    private function _tableInterpolate($input, &$table)
    {
        $max = max(array_keys($table));
        $min = min(array_keys($table));
        if (($input < $min) || ($input > $max)) {
            return null;
        }
        foreach (array_keys($table) as $key) {
            $last = $key;
            if ((float)$key > $input) {
                break;
            }
            $prev = $key;
        }
        $out   = $table[$prev];
        $fract = ($prev - $input) / ($prev - $last);
        $diff  = $fract * ($table[$last] - $table[$prev]);
        return (float)($out + $diff);
    }
    /**
    * Returns the reversed reading
    *
    * @param array $value   The data to use
    * @param int   $channel The channel to get
    * @param float $deltaT  The time delta in seconds between this record
    * @param array &$prev   The previous reading
    * @param array &$data   The data from the other sensors that were crunched
    *
    * @return string The reading as it would have come out of the endpoint
    *
    * @SuppressWarnings(PHPMD.ShortVariable)
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    */
    protected function getRaw(
        $value, $channel = 0, $deltaT = 0, &$prev = null, &$data = array()
    ) {
        bcscale(20);
        $Am    = pow(2, 23);
        $Rbias = $this->getExtra(0);

        if (is_null($value)) {
            return null;
        }
        $table = array_flip($this->_valueTable);
        $R = $this->_tableInterpolate($value, $table);
        $A = ($R * $Am) / ($Rbias + $R);
        return (int)round(($A * -1));

    }
}


?>
