<?php
/**
    $Id: unitConversion.inc.php 268 2006-10-13 20:38:43Z prices $    
*/
class unitConversion {

    var $units = array(
        '&deg;C' => array(
            '&deg;F' => 'CtoF',
        ),
        '&deg;F' => array(
            '&deg;C' => 'FtoC',
        ),
        'A' => array(
            'mA' => 'toMilli',
        ),
        'V' => array(
            'mV' => 'toMilli',
        ),
        'numDir' => array(
            'Direction' => 'numDirtoDir',
        ),
    );
    var $unitsNonDiff = array(
        
    );
    var $unitsDiff = array(
        'Revs' => array(
            'RPM' => 'CnttoRPM',
        ),
        'HalfRevs' => array(
            'RPM' => 'hCnttoRPM',
            'MPH' => 'hCnttoMPH',
        ),
    );
    


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
        if ($type == 'diff') {
            foreach($this->unitsDiff as $f => $to) {
                foreach($to as $t => $func) {
                    $ret[$f][$t] = $t;
                }
            }
        } else {
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
    function CnttoRPM ($cnt, $time, $type) {
        if ($type == 'diff') {
            $rpm = ($cnt/$time)*60;
            return($rpm);
        } else {
            return(NULL);        
        }    
    }

    /**
    @brief
    @param
    @return
    */
    function hCnttoRPM ($cnt, $time, $type) {
        if ($type == 'diff') {
            $rpm = (($cnt/2)/$time)*60;
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
    function hCnttoMPH ($cnt, $time, $type) {
        if ($type == 'diff') {
            $ACFreq = $cnt/$time;
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
	function numDirtoDir($ndir, $time, $type) {

        return $ndir;
	}

}

?>