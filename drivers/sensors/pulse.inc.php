<?php
/**
	$Id: winddirection.inc.php 121 2007-07-19 13:13:02Z prices $
	@file sensors/resistive.inc.php
	@brief Class for dealing with resistive sensors
	
	
*/
/**
	@brief class for dealing with resistive sensors.
*/
if (!class_exists('pulseCounter')) {
    $this->add_generic(array("Name" => "pulseCounter", "Type" => "sensor", "Class" => "pulseCounter"));
    
    
    class pulseCounter extends sensor_base
    {
    
        /**
            This defines all of the sensors that this driver deals with...
        */
        // PPM = Pulses per minute
        var $sensors = array(
            0x70 => array(
                'generic' => array(
                    "longName" => "Generic Pulse Counter",
                    "unitType" => "Pulses",
                    "validUnits" => array('PPM', 'counts'),
                    "storageUnit" =>  'PPM',
                    "unitModes" => array(
                        'PPM' => 'diff',
                        'counts' => 'raw,diff',
                    ),
                    "checkFunction" => "pulseCheck",

                ),
                'genericRevolver' => array(
                    "longName" => "Generic Revolving Thingy",
                    "unitType" => "Revolutional Speed",
                    "validUnits" => array('PPM', 'counts', 'RPM'),
                    "storageUnits" =>  'PPM',
                    "unitModes" => array(
                        'PPM' => 'diff',
                        'RPM' => 'diff',
                        'counts' => 'raw,diff',
                    ),
                    "extraText" => "Counts per Revolution",
                ),
                'maximumAnemometer' => array(
                    "longName" => "Maximum Inc type Hall Effect Anemometer",
                    "unitType" => "Wind Speed",
                    "validUnits" => array('MPH'),
                    "storageUnit" =>  'MPH',
                    "unitModes" => array(
                        'MPH' => 'diff',
                    ),
                    "function" => "maximumAnemometer",
                    "checkFunction" => "diffCheck",
                ),
                'maximumRainGauge' => array(
                    "longName" => "Maximum Inc rain gauge",
                    "unitType" => "Rain Fall",
                    "validUnits" => array('&#34;'),
                    "storageUnit" =>  '&#34;',
                    "unitModes" => array(
                        '&#34;' => 'diff',
                    ),
                    "mult" => 0.01,
                    "checkFunction" => "diffCheck",
                    "doTotal" => TRUE,
                ),
                'bravo3motion' => array(
                    "longName" => "DSC Bravo 3 Motion Sensor",
                    "unitType" => "Counts",
                    "validUnits" => array('counts', 'PPM'),
                    "storageUnit" =>  'counts',
                    "unitModes" => array(
                        'counts' => 'diff,raw',
                        'PPM' => 'diff',
                    ),
                    "checkFunction" => "diffCheck",
                    "doTotal" => TRUE,
                ),
                'wattnode' => array(
                    "longName" => "CCS WattNode Pulse Output Power Meter",
                    "unitType" => "Power",
                    "validUnits" => array('kWh'),
                    "storageUnit" =>  'kWh',
                    "function" => "WattNode",
                    "unitModes" => array(
                        'kWh' => 'raw,diff',
                    ),
                    "extraText" => "Watt Hours / Pulse",
                    "extraDefault" => 5,
                    "doTotal" => TRUE,
                ),
            ),
        );
    

        /**
            This implements the function:
                Freq = (Speed + 0.1)/1.6965
            or:
                Speed = (Freq * 1.6965) - 0.1
            
            Freq = Pulses/Time
        */
        function maximumAnemometer($val, $TC, $extra, $deltaT=NULL) {
            if (empty($deltaT) || ($deltaT < 0)) return NULL;
            if ($val <= 0) return 0;
            $speed = (($val / $deltaT) * 1.6965) - 0.1;
            if ($speed < 0) $speed = 0;
            return $speed;
        }
        
        function diffCheck($value, $units) {
            if ($value < 0) return FALSE;
            return TRUE;
        }

        function WattNode($val, $TC, $extra, $deltaT=NULL) {
            if (empty($deltaT) || ($deltaT < 0)) return NULL;
            $Wh = $val * $extra;
            return $Wh / 1000;
        }


    }
    
}


?>
