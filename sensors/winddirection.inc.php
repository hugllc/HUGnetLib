<?php
/**
	$Id$
	@file sensors/resistive.inc.php
	@brief Class for dealing with resistive sensors
	
	
*/
require_once("sensor.inc.php");
/**
	@brief class for dealing with resistive sensors.
*/
class windDirectionSensor extends sensor_base
{

    /**
        This defines all of the sensors that this driver deals with...
    */
    var $sensors = array(
        0x6F => array(
            'maximum-inc' => array(
                "longName" => "Maximum Inc wind direction sensor",
                "validUnits" => array('&#176;', 'Direction'),
                "defaultUnits" =>  '&#176;',
                "function" => "maximumIncSensor",
            ),
        ),
    );


    function maximumIncSensor($ndir) {
        $dir = NULL;        

        if ($ndir & (1<<0)) $dir1 = 0;   //N
        if ($ndir & (1<<2)) $dir1 = 90;  //E
        if ($ndir & (1<<4)) $dir1 = 180; //S
        if ($ndir & (1<<6)) $dir1 = 270; //W

        if ($ndir & (1<<1)) $dir2 = 45;    //NE
        if ($ndir & (1<<3)) $dir2 = 135;    //SE
        if ($ndir & (1<<5)) $dir2 = 225;    //SW
        if ($ndir & (1<<7)) $dir2 = 315;    //NW

        // This code encompances all posible good values.  It is safe
        // in that respect.  It could probably be speeded up, though.  ;)
        if (!is_null($dir1) && is_null($dir2)) 
        {
            $dir = $dir1;
        } else if (is_null($dir1) && !is_null($dir2)) 
        {
            $dir = $dir2;
        } else 
        {
            if ($dir1 == 0) {
                if ($dir2 == 45) {
                    $dir = 22.5;
                } else if ($dir2 == 315) {
                    $dir = 337.5;
                }
            } else if ($dir1 == 90) {
                if ($dir2 == 45) {
                    $dir = 67.5;
                } else if ($dir2 == 135) {
                    $dir = 112.5;
                }
            } else if ($dir1 == 180) {
                if ($dir2 == 135) {
                    $dir = 157.5;
                } else if ($dir2 == 225){
                    $dir = 202.5;
                }
            } else if ($dir1 == 270) {
                if ($dir2 == 225) {
                    $dir = 247.5;
                } else if ($dir2 == 315) {
                    $dir = 292.5;
                }
            }
        }
        
        return $dir;
    }
}




?>
