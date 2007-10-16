<?php
/*
HUGnetLib is a library of HUGnet code
Copyright (C) 2007 Hunt Utilities Group, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 3
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
?>
<?php
/**
    $Id$    
*/
class unitConversion {

    var $units = array(
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
        'A' => array(
            'longName' => 'Amps',
            'varType' => 'float',
            'convert' => array(
                'mA' => 'toMilli',
            ),
        ),
        'V' => array(
            'longName' => 'Volts',
            'varType' => 'float',
            'convert' => array(
                'mV' => 'toMilli',
            ),
        ),
        'mA' => array(
            'longName' => 'milliamps',
            'varType' => 'float',
            'convert' => array(
                'A' => 'fromMilli',
            ),
        ),
        'mV' => array(
            'longName' => 'millivolts',
            'varType' => 'float',
            'convert' => array(
                'V' => 'fromMilli',
            ),
        ),
        '&#176;' => array(
            'longName' => 'Compass Degrees',
            'varType' => 'float',
            'mode' => 'raw',        
            'convert' => array(
                'Direction' => 'numDirtoDir',
            ),
        ),        
        'counts' => array(
            'longName' => 'Counts',
            'varType' => 'int',
            'mode' => 'diff',
            'convert' => array(
                'RPM' => 'CnttoRPM',    
                'PPM' => 'CnttoRPM',
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
        'RPM' => array(
            'longName' => 'Revolutions Per Minute',
            'mode' => 'diff',
            'varType' => 'float',
        ),  
        'PPM' => array(
            'longName' => 'Pulses Per Minute',
            'mode' => 'diff',
            'varType' => 'float',
        ),
        'MPH' => array(
            'longName' => 'Miles Per Hour',
            'mode' => 'diff',
            'varType' => 'float',
        ),
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
        '%' => array(
            'longName' => 'Relative Humidity',
            'varType' => 'float',
        ),
        '&#34;' => array(
            'longName' => 'Relative Humidity',
            'varType' => 'float',
        ),
        'W/m^2' => array(
            'longName' => 'Watts per Meter Squared',
            'varType' => 'float',
        ),
        
    );
        
    function preferredUnit($unit) {
        if (isset($this->units[$unit]['preferred'])) {
            return $this->units[$unit]['preferred'];
        } else {
            return $unit;
        }
    }    
    
    function graphable($unit) {
        $unit = trim($unit);
        if (($this->units[$unit]['varType'] == 'int') || ($this->units[$unit]['varType'] == 'float')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }    
        
    function getDataType($from, $to, $default = 'all') {
        if (trim(strtolower($default)) == 'ignore') return $default;
        if (isset($this->unitsDiff[$from]['mode'])) {
            return $this->unitsDiff[$from]['mode'];
        }
        return $default;
    }

    function getConvFunct($from, $to, $type) {
        if ($to == $from) return NULL;
        
        $ret = $this->units[$from][$to];
        if ($ret !== NULL) return $ret;
        if (isset($this->units[$from]['convert'][$to])) {
            if (!isset($this->units[$to]['mode']) || ($this->units[$to]['mode'] == $type)) {
                return $this->units[$from]['convert'][$to];
            }
        }
        return NULL;
    }

    function getPossConv($type, $from=NULL) {

        $ret = array();
        foreach($this->units as $f => $to) {
            if (is_array($to['convert'])) {
                if (!isset($to['mode']) || ($to['mode'] == $type)) {
                    foreach($to['convert'] as $t => $func) {
                        $ret[$f][$t] = $t;
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
    @brief
    @param
    @return
    
    */
	function CtoF($c, $time, $type) {
		$F = ((9*$c)/5);
		if ($type != 'diff') $F += 32;
        return($F);
	}

    /**
    @brief
    @param
    @return
    
    */
	function FtoC($f, $time, $type) {
		if ($type != 'diff') $f -= 32;
		return((5*($f-32))/9);
	}
    /**
    @brief
    @param
    @return
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
    @brief
    @param
    @return

    AC Freq = (MPH + 0.1) / 1.6965
    
    MPH = ACFreq * 1.6965 - 0.1    
    
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
    @brief
    @param
    @return
    
    */
	function toMilli($V, $time, $type) {
		return($V*1000);
	}
    /**
    @brief
    @param
    @return
    
    */
	function fromMilli($V, $time, $type) {
		return($V/1000);
	}

    /**
    @brief
    @param
    @return
    
    */
	function fromCenti($V, $time, $type) {
		return($V/100);
	}

   /**
    @brief
    @param
    @return
    
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

    function kWhTokW ($val, $time, $type, $extra) {
        if (empty($time)) return NULL;
        if ($type != "diff") return NULL;
        return ($val / (abs($time) / 3600));
    }

    function kWhToW ($val, $time, $type, $extra) {
        $val = unitConversion::kWhTokW($val, $time, $type, $extra);
        if (is_null($val)) return $val;
        return $val * 1000;
    }

}


?>
