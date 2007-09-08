<?php
/**
	$Id: winddirection.inc.php 121 2007-07-19 13:13:02Z prices $
	@file sensors/resistive.inc.php
	@brief Class for dealing with resistive sensors
	
	
*/
require_once("sensor.inc.php");
/**
	@brief class for dealing with resistive sensors.
*/
class pulseCounter extends sensor_base
{

    /**
        This defines all of the sensors that this driver deals with...
    */
    var $sensors = array(
        0x70 => array(
            'maximumAnemometer' => array(
                "longName" => "Maximum Inc type #41 Anemometer",
                "validUnits" => array('RPM', 'MPH', 'counts'),
                "defaultUnits" =>  'MPH',
                "mult" => 0.5,
            ),
            'maximumRainGauge' => array(
                "longName" => "Maximum Inc rain gauge",
                "validUnits" => array('&#34;'),
                "defaultUnits" =>  '&#34;',
                "mult" => .01,
            ),
            'genericRevolver' => array(
                "longName" => "Generic Revolving Thingy",
                "validUnits" => array('counts', 'RPM'),
                "defaultUnits" =>  'RPM',
                "extra" => "Counts per Revolution"
            ),
            'generic' => array(
                "longName" => "Generic Pulse Counter",
                "validUnits" => array('counts'),
                "defaultUnits" =>  'counts',
            ),
        ),
    );
    var $defaultSensor = "generic";

}




?>
