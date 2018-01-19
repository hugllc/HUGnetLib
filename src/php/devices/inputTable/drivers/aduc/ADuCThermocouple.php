<?php
/**
 * Classes for dealing with devices
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
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
 * Sensor driver for a thermocouple on the ADuC706x
 *
 * @category   Libraries
 * @package    HUGnetLib
 * @subpackage Sensors
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.14.8
 * @link       http://dev.hugllc.com/index.php/Project:HUGnetLib
 * @since      0.9.7
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class ADuCThermocouple extends \HUGnet\devices\inputTable\DriverADuC
    implements \HUGnet\devices\inputTable\DriverInterface
{
    /** This is the number of decimal places we use for our *MATH*, not the output */
    const DECIMAL_PLACES = 20;
    /**
    * This is where the data for the driver is stored.  This array must be
    * put into all derivative classes, even if it is empty.
    */
    protected $params = array(
        "longName" => "ADuC Thermocouple",
        "shortName" => "ADuCThermo",
        "unitType" => "Temperature",
        "storageUnit" => '&#176;C',
        "storageType" => \HUGnet\devices\datachan\Driver::TYPE_RAW,
        "extraText" => array(
            "R1 to Source (kOhms)",
            "R2 to Ground (kOhms)",
            "AtoD Ref Voltage (mV)",
            "Thermocouple Type",
            "Junction Temp Channel",
        ),
        "extraDesc" => array(
            "The input resistance to the AtoD",
            "The resistor connecting the AtoD to ground",
            "The voltage used for the AtoD reference.",
            "The type of thermocouple used",
            "The data channel to read the local junction temperature of the
             thermocouple from",
        ),
        "extraNames" => array(
            "r1"       => 0,
            "r2"       => 1,
            "atodref"  => 2,
            "type"     => 3,
            "tempchan" => 4,
        ),
        // Integer is the size of the field needed to edit
        // Array   is the values that the extra can take
        // Null    nothing
        "extraValues" => array(
            5, 5, 5, 
            array(
                "b" => "B", "e" => "E", "j" => "J", "k" => "K",
                "n" => "N", "r" => "R", "s" => "S", "t" => "T"
            ), 
            array()
        ),
        "extraDefault" => array(1, 10, 1200, "k", 0),
        "maxDecimals" => 4,
        "inputSize" => 4,
        "requires" => array("AI", "ATODREF"),
        "provides" => array("DC"),
    );
    /** These are the coeffients of the thermocouple equasion */
    private $_coeffients = array(
        "b" => array(
            "0.219" => null, // For below 200(0.219 mV) deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "2.431" => array(// For below 700(2.431 mV) degC
                9.8423321E+01, 6.9971500E+02, -8.4765304E+02,
                1.0052644E+03, -8.3345952E+02, 4.5508542E+02,
                -1.5523037E+02, 2.9886750E+01, -2.4742860E+00
            ),
            "13.820" => array(// For below 1820(13.820 mV) deg C
                2.1315071E+02, 2.8510504E+02, -5.2742887E+01,
                9.9160804E+00, -1.2965303E+00, 1.1195870E-01,
                -6.0625199E-03, 1.8661696E-04, -2.4878585E-06 
            ),
        ),
        "e" => array(
            "-9.835" => null, // For below -270(-9.835 mV) deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "0" => array(// For below 0(0 mV) degC
                0, 1.6977288E+01, -4.3514970E-01,
                -1.5859697E-01, -9.2502871E-02, -2.6084314E-02,
                -4.1360199E-03, -3.4034030E-04, -1.1564890E-05
            ),
            "76.373" => array(// For below and 1000(76.373 mV) deg C
                0, 1.7057035E+01, -2.3301759E-01,
                6.5435585E-03, -7.3562749E-05, -1.7896001E-06,
                8.4036165E-08, -1.3735879E-09, 1.0629823E-11,
                -3.2447087E-14 
            ),
        ),
        "j" => array(
            "-8.095" => null, // For below -210(-8.095 mV) deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "0" => array(// For below 00(0 mV) degC
                0, 1.9528268E+01, -1.2286185E+00,
                -1.0752178E+00, -5.9086933E-01, -1.7256713E-01,
                -2.8131513E-02, -2.3963370E-03, -8.3823321E-05
            ),
            "42.919" => array(// For below 760(42.919 mV) and 1200(69.553 mV) deg C
                0, 1.978425E+01, -2.001204E-01,
                1.036969E-02, -2.549687E-04, 3.585153E-06,
                -5.344285E-08, 5.099890E-10
            ),
            "69.553" => array(// For below 1200(69.553 mV) deg C
                -3.11358187E+03, 3.00543684E+02, -9.94773230E+00,
                1.70276630E-01, -1.43033468E-03, 4.73886084E-06
            ),
        ),
        "k" => array(
            "-5.891" => null, // For below -200(-5.891 mV) deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "0" => array(// For below 0(0 mV) degC
                0, 2.5173462E1, -1.1662878, -1.0833638, -8.9773540E-1,
                -3.7342377E-1, -8.6632643E-2, -1.0450598E-2, -5.1920577E-4
            ),
            "20.644" => array(// For below 500(20.644 mV) deg C
                0, 2.508355E1, 7.860106E-2, -2.503131E-1, 8.315270E-2,
                -1.228034E-2, 9.804036E-4, -4.413030E-5, 1.057734E-6,
                -1.052755E-8
            ),
            "54.886" => array(// For below 1370(54.886 mV) deg C
                -1.318058E2, 4.830222E1, -1.646031, 5.464731E-2, -9.650715E-4,
                8.802193E-6, -3.110810E-8
            ),
        ),
        "n" => array(
            "-3.990" => null, // For below -200(-5.891 mV) deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "0" => array(// For below 0(0 mV) degC
                0, 3.8436847E+01, 1.1010485E+00,
                5.2229312E+00, 7.2060525E+00, 5.8488586E+00,
                2.7754916E+00, 7.7075166E-01, 1.1582665E-01,
                7.3138868E-03
            ),
            "20.613" => array(// For below 500(20.644 mV) deg C
                0, 3.86896E+01, -1.08267E+00, 4.70205E-02,
                -2.12169E-06, -1.17272E-04, 5.39280E-06,
                -7.98156E-08
            ),
            "47.513" => array(// For below 1370(54.886 mV) deg C
                1.972485E+01, 3.300943E+01, -3.915159E-01,
                9.855391E-03, -1.274371E-04, 7.767022E-07
            ),
        ),
        "r" => array(
            "-0.226" => null, // For below -50(-0.226 mV) deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "1.923" => array(// For below 250(1.923 mV) degC
                0, 1.8891380E+02, -9.3835290E+01,
                1.3068619E+02, -2.2703580E+02, 3.5145659E+02,
                -3.8953900E+02, 2.8239471E+02, -1.2607281E+02,
                3.1353611E+01, -3.3187769E+00
            ),
            "13.228" => array(// For below 1200(13.228 mV) deg C
                1.334584505E+01, 1.472644573E+02, -1.844024844E+01,
                4.031129726E+00, -6.249428360E-01, 6.468412046E-02,
                -4.458750426E-03, 1.994710149E-04, -5.313401790E-06,
                6.481976217E-08
            ),
            "19.739" => array(// For below 1664.5(19.739 mV) deg C
                -8.199599416E+01, 1.553962042E+02, -8.342197663E+00,
                4.279433549E-01, -1.191577910E-02, 1.492290091E-04
            ),
            "21.103" => array(// For below 1768.1(21.103 mV) deg C
                3.406177836E+04, -7.023729171E+03, 5.582903813E+02,
                -1.952394635E+01, 2.560740231E-01
            ),
        ),
        "s" => array(
            "-0.235" => null, // For below -50(-0.235 mV) deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "1.874" => array(// For below 250(1.875 mV) degC
                0, 1.84949460E+02, -8.00504062E+01,
                1.02237430E+02, -1.52248592E+02, 1.88821343E+02,
                -1.59085941E+02, 8.23027880E+01, -2.34181944E+01,
                2.79786260E+00
            ),
            "11.950" => array(// For below 1200(11.950 mV) deg C
                1.291507177E+01, 1.466298863E+02, -1.534713402E+01,
                3.145945973E+00, -4.163257839E-01, 3.187963771E-02,
                -1.291637500E-03, 2.183475087E-05, -1.447379511E-07,
                8.211272125E-09
            ),
            "17.536" => array(// For below 1664.5(17.536 mV) deg C
                -8.087801117E+01, 1.621573104E+02, -8.536869453E+00,
                4.719686976E-01, -1.441693666E-02, 2.081618890E-04
            ),
            "18.693" => array(// For below 1768.1(18.693 mV) deg C
                5.333875126E+04, -1.235892298E+04, 1.092657613E+03,
                -4.265693686E+01, 6.247205420E-01
            ),
        ),
        "t" => array(
            "-5.603" => null, // For below -200(-5.603 mV) deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "0" => array(// For below 0(0 mV) degC
                0, 2.5949192E+01, -2.1316967E-01,
                7.9018692E-01, 4.2527777E-01, 1.3304473E-01,
                2.0241446E-02, 1.2668171E-03
            ),
            "20.872" => array(// For below and 400(20.872 mV) deg C
                0, 2.592800E+01, -7.602961E-01,
                4.637791E-02, -2.165394E-03, 6.048144E-05, -7.293422E-07
            ),
        ),
    );
    /** These are the coeffients of the thermocouple equasion */
    private $_revCoeffients = array(
        "b" => array(
            "0" => null, // For below 250 deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "630.615" => array(// For below 700(2.431 mV) degC
                "c" => array(
                    0, -0.246508183460E-03, 0.590404211710E-05, -0.132579316360E-08,
                    0.156682919010E-11, -0.169445292400E-14, 0.629903470940E-18
                ),
                "a" => array(0, 0, 0),
            ),
            "1820" => array(// For between 0(0 mV) and 1372 deg C
                "c" => array(
                    -0.389381686210E+01, 0.285717474700E-01, -0.848851047850E-04,
                    0.157852801640E-06, -0.168353448640E-09, 0.111097940130E-12,
                    -0.445154310330E-16, 0.989756408210E-20, -0.937913302890E-24
                ),
                "a" => array(0, 0, 0),
            ),
        ),
        "e" => array(
            "-270" => null, // For below -270 deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "0" => array(// For below 0 degC
                "c" => array(
                    0, 0.586655087080E-01, 0.454109771240E-04,
                    -0.779980486860E-06, -0.258001608430E-07, -0.594525830570E-09,
                    -0.932140586670E-11, -0.102876055340E-12, -0.803701236210E-15,
                    -0.439794973910E-17, -0.164147763550E-19, -0.396736195160E-22,
                    -0.558273287210E-25, -0.346578420130E-28
                ),
                "a" => array(0, 0, 0),
            ),
            "1000" => array(// For below 1000 deg C
                "c" => array(
                    0, 0.586655087100E-01, 0.450322755820E-04, 
                    0.289084072120E-07, -0.330568966520E-09, 0.650244032700E-12,
                    -0.191974955040E-15, -0.125366004970E-17, 0.214892175690E-20,
                    -0.143880417820E-23, 0.359608994810E-27
                ),
                "a" => array(0, 0, 0),
            ),
        ),
        "j" => array(
            "-210" => null, // For below -210 deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "760" => array(// For below 760 degC
                "c" => array(
                    0, 0.503811878150E-01, 0.304758369300E-04,
                    -0.856810657200E-07, 0.132281952950E-09, -0.170529583370E-12,
                    0.209480906970E-15, -0.125383953360E-18, 0.156317256970E-22
                ),
                "a" => array(0, 0, 0),
            ),
            "1200" => array(// For below 1200 deg C
                "c" => array(
                    0.296456256810E+03, -0.149761277860E+01, 0.317871039240E-02,
                    -0.318476867010E-05, 0.157208190040E-08, -0.306913690560E-12
                ),
                "a" => array(0, 0, 0),
            ),
        ),
        "k" => array(
            "-270" => null, // For below -270 deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "0" => array(// For below 0 degC
                "c" => array(
                    0, 0.394501280250E-1, 0.236223735980E-4, -0.328589067840E-6,
                    -0.499048287770E-8, -0.675090591730E-10, -0.574103274280E-12,
                    -0.310888728940E-14, -0.104516093650E-16, -0.198892668780E-19,
                    -0.163226974860E-22
                ),
                "a" => array(0, 0, 0),
            ),
            "1372" => array(// For below 1372 deg C
                "c" => array(
                    -0.176004136860E-1, 0.389212049750E-1, 0.185587700320E-4,
                    -0.994575928740E-7, 0.318409457190E-9, -0.560728448890E-12,
                    0.560750590590E-15, -0.320207200030E-18, 0.971511471520E-22,
                    -0.121047212750E-25
                ),
                "a" => array(0.1185976, -0.1183432E-3, 0.1269686E3),
            ),
        ),
        "n" => array(
            "-270" => null, // For below -270 deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "0" => array(// For below 0 degC
                "c" => array(
                    0, 0.261591059620E-01, 0.109574842280E-04,
                    -0.938411115540E-07, -0.464120397590E-10, -0.263033577160E-11,
                    -0.226534380030E-13, -0.760893007910E-16, -0.934196678350E-19

                ),
                "a" => array(0, 0, 0),
            ),
            "1300" => array(// For below 1300 deg C
                "c" => array(
                    0, 0.259293946010E-01, 0.157101418800E-04,
                    0.438256272370E-07, -0.252611697940E-09, 0.643118193390E-12,
                    -0.100634715190E-14, 0.997453389920E-18, -0.608632456070E-21,
                    0.208492293390E-24, -0.306821961510E-28

                ),
                "a" => array(0, 0, 0),
            ),
        ),
        "r" => array(
            "-50" => null, // For below -270 deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "1064.180" => array(// For below 1064.180 degC
                "c" => array(
                    0, 0.528961729765E-02, 0.139166589782E-04,
                    -0.238855693017E-07, 0.356916001063E-10, -0.462347666298E-13,
                    0.500777441034E-16, -0.373105886191E-19, 0.157716482367E-22,
                    -0.281038625251E-26
                ),
                "a" => array(0, 0, 0),
            ),
            "1664.500" => array(// For below 1664.5 deg C
                "c" => array(
                    0.295157925316E+01, -0.252061251332E-02, 0.159564501865E-04,
                    -0.764085947576E-08, 0.205305291024E-11, -0.293359668173E-15
                ),
                "a" => array(0, 0, 0),
            ),
            "1768.1" => array(// For below 1768.1 deg C
                "c" => array(
                    0.152232118209E+03, -0.268819888545E+00, 0.171280280471E-03,
                    -0.345895706453E-07, -0.934633971046E-14
                ),
                "a" => array(0, 0, 0),
            ),
        ),
        "s" => array(
            "-50" => null, // For below -270 deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "1064.180" => array(// For below 1064.180 degC
                "c" => array(
                    0, 0.540313308631E-02, 0.125934289740E-04,
                    -0.232477968689E-07, 0.322028823036E-10, -0.331465196389E-13,
                    0.255744251786E-16, -0.125068871393E-19, 0.271443176145E-23
                ),
                "a" => array(0, 0, 0),
            ),
            "1664.500" => array(// For below 1664.5 deg C
                "c" => array(
                    0.132900444085E+01, 0.334509311344E-02, 0.654805192818E-05,
                    -0.164856259209E-08, 0.129989605174E-13
                ),
                "a" => array(0, 0, 0),
            ),
            "1768.1" => array(// For below 1768.1 deg C
                "c" => array(
                    0.146628232636E+03, -0.258430516752E+00, 0.163693574641E-03,
                    -0.330439046987E-07, -0.943223690612E-14
                ),
                "a" => array(0, 0, 0),
            ),
        ),
        "t" => array(
            "-270" => null, // For below -270 deg C
            // From http://srdata.nist.gov/its90/type_k/kcoefficients_inverse.html
            "0" => array(// For below 0 degC
                "c" => array(
                    0, 0.387481063640E-01, 0.441944343470E-04,
                    0.118443231050E-06, 0.200329735540E-07, 0.901380195590E-09,
                    0.226511565930E-10, 0.360711542050E-12, 0.384939398830E-14,
                    0.282135219250E-16, 0.142515947790E-18, 0.487686622860E-21,
                    0.107955392700E-23, 0.139450270620E-26, 0.797951539270E-30
                ),
                "a" => array(0, 0, 0),
            ),
            "400" => array(// For below 400 deg C
                "c" => array(
                    0, 0.387481063640E-01, 0.332922278800E-04,
                    0.206182434040E-06, -0.218822568460E-08, 0.109968809280E-10,
                    -0.308157587720E-13, 0.454791352900E-16, -0.275129016730E-19
                ),
                "a" => array(0, 0, 0),
            ),
        ),
    );
    /**
    * Gets an item
    *
    * @param string $name The name of the property to get
    *
    * @return null
    */
    public function get($name)
    {
        $ret = parent::get($name);
        if ($name == "extraValues") {
            $ret[4] = $this->input()->device()->dataChannels()->select();
        }
        return $ret;
    }
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
        bcscale(self::DECIMAL_PLACES);
        $Am    = pow(2, 23);
        $Rin   = $this->getExtra(0);
        $Rbias = $this->getExtra(1);
        $Vref  = $this->getExtra(2);
        $type  = $this->getExtra(3);
        $ref   = $this->getExtra(4);

        $A = $this->inputBiasCompensation($A, $Rin, $Rbias);
        $Va = ($A / $Am) * $Vref;
        $T = $this->_getThermocouple($Va, $data[$ref]["value"], $type);
        if (is_null($T)) {
            return null;
        }
        return round($T, $this->get('maxDecimals'));
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param float $V     Voltage output of thermocouple in milliVolts
    * @param float $TCold Cold junction temperature in degrees C
    * @param float $type  Thermocouple type
    *
    * @return mixed The temperature
    */
    private function _getThermocouple($V, $TCold, $type = "k")
    {
        foreach ((array)array_keys($this->_coeffients[$type]) as $k) {
            if ($V < (float)$k) {
                if (empty($this->_coeffients[$type][$k])) {
                    break;
                }
                $T = 0;
                $c = &$this->_coeffients[$type][$k];
                for ($i = 0; isset($c[$i]); $i++) {
                    /* This is required so when php converts the float to a string
                     * it is *NOT* in scientific notation.  bc functions don't seem
                     * to like scientific notation, and they require string inputs */
                    $coef = number_format($c[$i], self::DECIMAL_PLACES, '.', '');
                    $T = bcadd($T, bcmul($coef, bcpow($V, $i)));
                }
                $T += $TCold;
                break;
            }
        }
        return $T;
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
        bcscale(self::DECIMAL_PLACES);
        $Am    = pow(2, 23);
        $Rin   = $this->getExtra(0);
        $Rbias = $this->getExtra(1);
        $Vref  = $this->getExtra(2);
        $type  = $this->getExtra(3);
        $ref   = $this->getExtra(4);

        if ($Vref == 0) {
            return null;
        }
        $Va = $this->_revThermocouple($value, $data[$ref]["value"], $type);
        if (is_null($Va)) {
            return null;
        }
        $A = ($Va / $Vref) * $Am;
        $Amod = $this->inputBiasCompensation($A, $Rin, $Rbias);
        if ($Amod != 0) {
            $A = $A * ($A / $Amod);
        }
        return (int)round($A);
    }
    /**
    * Changes a raw reading into a output value
    *
    * @param float $T     Voltage output of thermocouple in milliVolts
    * @param float $TCold Cold junction temperature in degrees C
    * @param float $type  Thermocouple type
    *
    * @return mixed The temperature
    */
    private function _revThermocouple($T, $TCold, $type = "k")
    {
        $V = null;
        if (!is_array($this->_revCoeffients[$type])) {
            return null;
        }
        foreach ((array)array_keys($this->_revCoeffients[$type]) as $k) {
            if ($T < (float)$k) {
                if (empty($this->_revCoeffients[$type][$k])) {
                    break;
                }
                $V = 0;
                $T -= $TCold;
                $c = &$this->_revCoeffients[$type][$k]["c"];
                for ($i = 0; isset($c[$i]); $i++) {
                    /* This is required so when php converts the float to a string
                     * it is *NOT* in scientific notation.  bc functions don't seem
                     * to like scientific notation, and they require string inputs */
                    $coef = number_format($c[$i], self::DECIMAL_PLACES, '.', '');
                    $V = bcadd($V, bcmul($coef, bcpow($T, $i)));
                }
                $a = &$this->_revCoeffients[$type][$k]["a"];
                $a0 = number_format($a[0], self::DECIMAL_PLACES, '.', '');
                $a1 = number_format($a[1], self::DECIMAL_PLACES, '.', '');
                $a2 = number_format($a[2], self::DECIMAL_PLACES, '.', '');
                $exp = (float)bcmul($a1, bcpow(bcsub($T, $a2), 2));
                $adder = bcmul($a0, exp($exp));
                $V = (float)bcadd($V, $adder);
                break;
            }
        }
        return $V;
    }
}


?>
