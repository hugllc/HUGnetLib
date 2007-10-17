<?php
/**
 *   Everything to do with Units.
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
 *   @subpackage unitConversion
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$    
 *
 */
/**
 *   Class for doing unit conversions and storing unit information
 *   
 *   All units must be added to this class if they are to be used in HUGnet
 *   code.  This is because this tells everything else how to deal with these
 *   units and how to convert between them.  If units are not added here then
 *   many things will not work with the units.
 *
 *   @subpackage unitConversion
 */
class unitConversion {

    /**
     *  This is the array that defines all of our units and how to
     * display and use them.
     *  @var array
     *
     */
    var $units = array(
        'temperature' => array(
            '&#176;C' => array(
                'longName' => '&#176;C',
                'varType' => 'float',
                'convert' => array(
                    '&#176;F' => 'CtoF',
                ),
                'preferred' => '&#176;F',
            ),
            '&#176;F' => array(
                'longName' => '&#176;F',
                'varType' => 'float',
                'convert' => array(
                    '&#176;C' => 'FtoC',
                ),
            ),
        ),
        'current' => array(
            'A' => array(
                'longName' => 'Amps',
                'varType' => 'float',
                'convert' => array(
                    'mA' => 'toMilli',
                ),
            ),
            'mA' => array(
                'longName' => 'milliamps',
                'varType' => 'float',
                'convert' => array(
                    'A' => 'fromMilli',
                ),
            ),
        ),
        'voltage' => array(
            'V' => array(
                'longName' => 'Volts',
                'varType' => 'float',
                'convert' => array(
                    'mV' => 'toMilli',
                ),
            ),
            'mV' => array(
                'longName' => 'millivolts',
                'varType' => 'float',
                'convert' => array(
                    'V' => 'fromMilli',
                ),
            ),
        ),
        'direction' => array(
            '&#176;' => array(
                'longName' => 'Compass Degrees',
                'varType' => 'float',
                'mode' => 'raw',        
                'convert' => array(
                    'Direction' => 'numDirtoDir',
                ),
            ),
            'Direction' => array(
                'longName' => 'Direction',
                'varType' => 'text',
                'mode' => 'raw',
                'convert' => array(
                    '&#176;' => 'DirtonumDir',
                ),
            ),  
        ),
        'counting' => array(
            'counts' => array(
                'longName' => 'Counts',
                'varType' => 'int',
                'mode' => 'diff',
                'convert' => array(
                    'RPM' => 'CnttoRPM',    
                    'PPM' => 'CnttoRPM',
                ),
            ),
            'PPM' => array(
                'longName' => 'Pulses Per Minute',
                'mode' => 'diff',
                'varType' => 'float',
            ),
            'RPM' => array(
                'longName' => 'Revolutions Per Minute',
                'mode' => 'diff',
                'varType' => 'float',
            ),  
        ),
        'speed' => array(
            'MPH' => array(
                'longName' => 'Miles Per Hour',
                'mode' => 'diff',
                'varType' => 'float',
            ),
        ),
        'power' => array(
            'kWh' => array(
                'longName' => 'Kilowatt Hours',
                'varType' => 'float',
                'convert' => array(
                    'Wh' => 'toMilli',
                    'kW' => 'kWhTokW',
                    'W' => 'kWhToW',
                ),
            ),
            'kW' => array(
                'longName' => 'Kilowatts',
                'mode' => 'diff',
                'varType' => 'float',
                'convert' => array(
                    'W' => 'toMilli',
                ),
            ),
            'W' => array(
                'longName' => 'Watts',
                'mode' => 'diff',
                'varType' => 'float',
                'convert' => array(
                    'kW' => 'fromMilli',
                ),
            ),
            'Wh' => array(
                'longName' => 'Watt Hours',
                'varType' => 'float',
                'convert' => array(
                    'kWh' => 'fromMilli',
                    'W' => 'kWhTokW',
                ),
            ),
        ),
        'humidity' => array(
            '%' => array(
                'longName' => 'Relative Humidity',
                'varType' => 'float',
            ),
        ),
        'rain' => array(
            '&#34;' => array(
                'longName' => 'Rain Fall',
                'varType' => 'float',
            ),
        ),
        'light' => array(
            'W/m^2' => array(
                'longName' => 'Watts per Meter Squared',
                'varType' => 'float',
            ),
        ),
    );

    /**
     * Checks to see if there is a unit that is preferred over the one given.
     *
     * @param string $unit The unit to check
     * @return string The preferred unit
     */
    function preferredUnit($unit) {
        $u = $this->findUnit($unit);
        if (isset($u['preferred'])) {
            return $u['preferred'];
        } else {
            return $unit;
        }
    }    
    
    /**
     * Checks to see if a particular unit is able to be graphed
     *
     * @param string $unit The unit to check
     * @return bool Whether the unit can be graphed or not
     */
    function graphable($unit) {
        $unit = trim($unit);
        $u = $this->findUnit($unit);
        if ($u === FALSE) return FALSE;
        if (($u['varType'] == 'int') || ($u['varType'] == 'float')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }    
    /**
     * Checks if a unit exists and returns the information on it if it does.
     *
     * Returns FALSE if the unit is not found
     *
     * @param string $unit The unit to find
     * @return array the array of unit information if it is found
     */
    function findUnit($unit) {

        foreach($this->units as $key => $value) {
            if (isset($value[$unit])) return $this->units[$key][$unit];
        }
        return FALSE;

    }

    /**
     * Gets the data type for this particular unit
     *
     * @param string $from The starting unit
     * @param string $to The unit to be converted into
     * @param string $default The data type to use if none is specified
     * @return string The data type to use
     */
    function getDataType($from, $to, $default = 'all') {
        if (trim(strtolower($default)) == 'ignore') return $default;
        $u = $this->findUnit($from);
        if ($u !== FALSE) {
            if (isset($u['mode'])) {
                return $u['mode'];
            }
        }
        return $default;
    }

    /**
     * Gets the conversion function to convert $from to $to
     *
     * @param string $from The starting unit
     * @param string $to The unit to be converted into
     * @param string $type The data type to use
     * @return string NULL if no function exists, the function name otherwise. 
     */
    function getConvFunct($from, $to, $type) {
        if ($to == $from) return NULL;
        $f = $this->findUnit($from);
        $t = $this->findUnit($to);
        if ($u === FALSE) return NULL;
        if (isset($f['convert'][$to])) {
            if (!isset($t['mode']) || ($t['mode'] == $type)) {
                return $f['convert'][$to];
            }
        }
        return NULL;
    }
    /**
     * Gets all possible conversions if $from == NULL.  Otherwise
     *
     * it gets all possible conversions from $from.
     * @param string $from The starting unit
     * @param string $type The data type to use if none is specified
     * @return array The possible conversions
     */

    function getPossConv($type, $from=NULL) {

        $ret = array();
        foreach($this->units as $f => $cat) {
            foreach($cat as $c => $to) {
                if (is_array($to['convert'])) {
                    if (!isset($to['mode']) || ($to['mode'] == $type)) {
                        foreach($to['convert'] as $t => $func) {
                            $ret[$f][$t] = $t;
                        }
                    }
                }
            }
        }

        foreach($ret as $key => $val) {
            $ret[$key][$key] = $key;        
        }
        if ($from == NULL) {
            return $ret;
        } else {
            // We can always convert from and to the same unit.
            $ret[$from][$from] = $from;
            return $ret[$from];
        }
    }

    /**
     *  Converts from &#176; C to &#176; F.
     *
     *  If the temperature is differential we can't add 32 like we would
     *  for an absolute temperature.  This is because it is already factored
     *  out by the subtraction in the difference.
     *
     * @param float $c The temperature in C
     * @param int $time The time in seconds between this record and the last.
     * @param string $type The type of data (diff, raw, etc)
     * @return float The temperature in F
     */
	function CtoF($c, $time, $type) {
		$F = ((9*$c)/5);
		if ($type != 'diff') $F += 32;
        return($F);
	}

    /**
     *  Converts from &#176; F to &#176; C.
     *
     *  If the temperature is differential we can't subtract 32 like we would
     *  for an absolute temperature.  This is because it is already factored
     *  out by the subtraction in the difference.
     *
     * @param float $f The temperature in F
     * @param int $time The time in seconds between this record and the last.
     * @param string $type The type of data (diff, raw, etc)
     * @return float The temperature in C
    */
	function FtoC($f, $time, $type) {
		if ($type != 'diff') $f -= 32;
		return((5*($f-32))/9);
	}
    /**
     * Change counts into revolutions per minute
     *
     * @param int $cnt The number of counts
     * @param int $time The time in seconds between this record and the last.
     * @param string $type The type of data (diff, raw, etc)
     * @param int $cntPerRev the number of counts per revolution
     * @return float NULL if not differential data, the RPM otherwise
    */
    function CnttoRPM ($cnt, $time, $type, $cntPerRev) {
        if ($cntPerRev <= 0) $cntPerRev = 1;
        if ($type == 'diff') {
            $rpm = ($cnt/$time/$cntPerRev)*60;
            return($rpm);
        } else {
            return(NULL);        
        }    
    }

    /**
     * The following formula is given in the datasheet
     *   AC Freq = (MPH + 0.1) / 1.6965
     *   
     *   MPH = ACFreq * 1.6965 - 0.1    
     *
     *  This function has moved to the driver for pulse counters.
     *   
     * @param int $cnt The number of counts since the last record.
     * @param int $time The time in seconds between this record and the last.
     * @param string $type The type of data (diff, raw, etc)
     * @param int $cntPerRev the number of counts per revolution
     * @return float NULL if data is not differential, MPH otherwise
     * @deprecated
    */
    function CnttoMPH ($cnt, $time, $type, $cntPerRev) {
        if ($cntPerRev <= 0) $cntPerRev = 1;
        if ($type == 'diff') {
            $ACFreq = $cnt/$time/$cntPerRev;
            $MPH = ($ACFreq * 1.6965) - 0.1;
            if ($MPH < 0) $MPH = 0;
            return($MPH);
        } else {
            return(NULL);        
        }    
    }


    /**
     * Converts a unit to milli.  meters to millimeters for example.
     *
     * @param float/int $W The number to work on
     * @param int $time The time in seconds between this record and the last.
     * @param string $type The type of data (diff, raw, etc)
     * @return float W*1000
    */
	function toMilli($W, $time, $type) {
		return $W*1000;
	}
    /**
     * Converts a unit from milli.  millimeters to meters for example.
     *
     * @param float/int $W The number to work on
     * @param int $time The time in seconds between this record and the last.
     * @param string $type The type of data (diff, raw, etc)
     * @return float W/1000
    */
	function fromMilli($W, $time, $type) {
		return $W/1000;
	}

    /**
     * Converts a unit to centi.  meters to centimeters for example.
     *
     * @param float/int $W The number to work on
     * @param int $time The time in seconds between this record and the last.
     * @param string $type The type of data (diff, raw, etc)
     * @return float W/100
    */
	function fromCenti($W, $time, $type) {
		return $W/100 ;
	}

   /**
     * Converts from a numeric compass direction to a textual direction abbreviation.
     *
     * So this converts 0 &#176; into 'N', 22.5 &#176; into 'NNE', etc.
     * This function is set up so that any number greater than the previous number
     * but less than or equal to the current number is taken for that direction.  So
     * if we got an input of 10 &#176; then it would return 'NNE'.
     *
     * If the number give is out of range (less than 0 or greater than 360) 'N' is
     * returned.
     *
     * @param float $ndir The numeric direction from 0 to 360 &#176;
     * @param int $time The time in seconds between this record and the last.
     * @param string $type The type of data (diff, raw, etc)
     * @return string The text direction
    */
	function numDirtoDir($ndir, $time, $type) {
        if ($ndir <= 0) return "N";
        if ($ndir <= 22.5) return "NNE";
        if ($ndir <= 45) return "NE";
        if ($ndir <= 67.5) return "ENE";
        if ($ndir <= 90) return "E";
        if ($ndir <= 112.5) return "ESE";
        if ($ndir <= 135) return "SE";
        if ($ndir <= 157.5) return "SSE";
        if ($ndir <= 180) return "S";
        if ($ndir <= 202.5) return "SSW";
        if ($ndir <= 225) return "SW";
        if ($ndir <= 247.5) return "WSW";
        if ($ndir <= 270) return "W";
        if ($ndir <= 292.5) return "WNW";
        if ($ndir <= 315) return "NW";
        if ($ndir <= 337.5) return "NNW";
        return "N";
	}

   /**
     * Converts from a textual direction abbreviation to a numberic compass direction.
     *
     * So this converts 'N' into 0 &#176; into 'N', 'NNE' into 22.5 &#176;, etc.
     *     
     * This function returns 0 if it gets an abbreviation that it does not understand.
     *
     *
     * @param string $ndir The text direction
     * @param int $time The time in seconds between this record and the last.
     * @param string $type The type of data (diff, raw, etc)
     * @return float The text direction from 0 to 360 &#176;
    */
	function DirtonumDir($ndir, $time, $type) {
	    $ndir = trim(strtoupper($ndir));
        if ($ndir == "N") return 0;
        if ($ndir == "NNE") return 22.5;
        if ($ndir == "NE") return 45;
        if ($ndir == "ENE") return 67.5;
        if ($ndir == "E") return 90;
        if ($ndir == "ESE") return 112.5;
        if ($ndir == "SE") return 135;
        if ($ndir == "SSE") return 157.5;
        if ($ndir == "S") return 180;
        if ($ndir == "SSW") return 202.5;
        if ($ndir == "SW") return 225;
        if ($ndir == "WSW") return 247.5;
        if ($ndir == "W") return 270;
        if ($ndir == "WNW") return 292.5;
        if ($ndir == "NW") return 315;
        if ($ndir == "NNW") return 337.5;
        return 0;
	}

    /**
     *  This function changes kWh into kW
     *
     *  It does this by dividing the delta time out of it.  I am
     *  not sure if this is a valid way to do it.
     *
     * @param float $val The input value
     * @param int $time The time in seconds between this record and the last.
     * @param string $type The type of data (diff, raw, etc)
     * @param mixed $extra The extra information from the sensor.
     * @return float The kW value
     */
    function kWhTokW ($val, $time, $type, $extra) {
        if (empty($time)) return NULL;
        if ($type != "diff") return NULL;
        return ($val / (abs($time) / 3600));
    }

    /**
     *  This function changes kWh into W
     *
     * @param float $val The input value
     * @param int $time The time in seconds between this record and the last.
     * @param string $type The type of data (diff, raw, etc)
     * @param mixed $extra The extra information from the sensor.
     * @uses unitConversion::kWhTokW()
     * @return float The W value
     */
    function kWhToW ($val, $time, $type, $extra) {
        $val = unitConversion::kWhTokW($val, $time, $type, $extra);
        if (is_null($val)) return $val;
        return $val * 1000;
    }

}


?>
