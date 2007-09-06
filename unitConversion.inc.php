<?php
/**
    $Id$    
*/
class unitConversion {

    var $units = array(
        '&#176;C' => array(
            '&#176;F' => 'CtoF',
        ),
        '&#176;F' => array(
            '&#176;C' => 'FtoC',
        ),
        'A' => array(
            'mA' => 'toMilli',
        ),
        'V' => array(
            'mV' => 'toMilli',
        ),
    );
    var $unitsNonDiff = array(
        '&#176;' => array(
            'Direction' => 'numDirtoDir',
        ),        
    );
    var $unitsDiff = array(
        'counts' => array(
            'RPM' => 'CnttoRPM',
            'MPH' => 'CnttoMPH',
            'Inches Rain' => 'fromCenti',
        ),
    );
    function checkDataTypes(&$units, &$dTypes) {
        if (!is_array($units)) return FALSE;
        foreach($params['Units'] as $key => $val) {
            $dTypes[$key] = $this->getDataType($units[$key], $val, $dTypes[$key]);
        }
        return TRUE;        
    }    
    
    function getDataType($from, $to, $default = 'all') {
        if (trim(strtolower($default)) == 'ignore') return $default;
        if (isset($this->unitsDiff[$from][$to])) {
            return 'diff';
        }
        if (isset($this->unitsNonDiff[$from][$to])) {
            return 'raw';
        }
        return $default;
    }

    function getConvFunct($from, $to, $type) {
        if ($to == $from) return NULL;
        
        $ret = $this->units[$from][$to];
        if ($ret !== NULL) return $ret;
        if ($type == 'diff') {
            return $this->unitsDiff[$from][$to];
        } else {
            return $this->unitsNonDiff[$from][$to];        
        }
    }

    function getPossConv($type, $from=NULL) {

        $ret = array();
        foreach($this->units as $f => $to) {
            foreach($to as $t => $func) {
                $ret[$f][$t] = $t;
            }
        }
        if (($type == 'diff') || ($type == 'all')) {
            foreach($this->unitsDiff as $f => $to) {
                foreach($to as $t => $func) {
                    $ret[$f][$t] = $t;
                }
            }
        }
        if (($type != 'diff') || ($type == 'all')) {
            foreach($this->unitsNonDiff as $f => $to) {
                foreach($to as $t => $func) {
                    $ret[$f][$t] = $t;
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
    function CnttoRPM ($cnt, $time, $type, $cntPerRev=1) {
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
    function CnttoMPH ($cnt, $time, $type, $cntPerRev=1) {
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

}

?>