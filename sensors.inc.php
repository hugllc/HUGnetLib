<?php

/**
	$Id$
	
*/


/**
	@brief Interface class for the sensor database
*/
class sensors {
   var $table = "sensors";		//!< The name of the table to use
	var $primaryCol = "SensorKey";	 //!< This is the Field name for the key of the record

}

/**
	@brief Interface class for the sensor database
*/
class sensordevices {
   var $table = "sensordevices";		//!< The name of the table to use
	var $primaryCol = "SensorDevicesKey";	 //!< This is the Field name for the key of the record

}


?>
