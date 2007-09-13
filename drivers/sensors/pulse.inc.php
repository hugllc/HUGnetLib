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
        var $sensors = array(
            0x70 => array(
                'generic' => array(
                    "longName" => "Generic Pulse Counter",
                    "unitType" => "Pulses",
                    "validUnits" => array('counts'),
                    "defaultUnits" =>  'counts',
                ),
                'genericRevolver' => array(
                    "longName" => "Generic Revolving Thingy",
                    "unitType" => "Revolutional Speed",
                    "validUnits" => array('counts', 'RPM'),
                    "defaultUnits" =>  'RPM',
                    "extra" => "Counts per Revolution"
                ),
                'maximumAnemometer' => array(
                    "longName" => "Maximum Inc type #41 Anemometer",
                    "unitType" => "Wind Speed",
                    "validUnits" => array('RPM', 'MPH', 'counts'),
                    "defaultUnits" =>  'MPH',
                    "mult" => 0.5,
                ),
                'maximumRainGauge' => array(
                    "longName" => "Maximum Inc rain gauge",
                    "unitType" => "Rain Fall",
                    "validUnits" => array('&#34;'),
                    "defaultUnits" =>  '&#34;',
                    "mult" => .01,
                ),
            ),
        );
        var $defaultSensor = "generic";
    
    }
    
}


?>
