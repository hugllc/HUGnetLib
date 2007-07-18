<?php
/**
	$Id: e00391200.inc.php 52 2006-05-14 20:51:23Z prices $
	@file history/history_e00391200.inc.php
	@brief History include that goes with the e00391200.inc.php driver.


	$Log: e00391200.inc.php,v $
	Revision 1.12  2005/09/26 01:08:20  prices
	It now doesn't convert pulse counters to "&#176;F" units.  ;)
	
	Revision 1.11  2005/09/26 01:02:53  prices
	It was forcing the units on the first side of the graph to be deg F.
	
	Revision 1.10  2005/09/26 00:55:59  prices
	Fixed it so the Pulse counter displays correctly.
	
	Revision 1.9  2005/08/30 01:29:19  prices
	Fixed the label on the second axiz
	
	Revision 1.8  2005/08/30 00:51:32  prices
	
	It now displays light correctly.
	
	Revision 1.7  2005/08/25 23:03:59  prices
	Fixed some problems.
	
	Revision 1.6  2005/08/10 13:47:19  prices
	Periodic commit.
	
	Revision 1.5  2005/06/15 23:29:16  prices
	The user can now specify the table or the graph or both.
	
	Revision 1.4  2005/06/15 19:16:22  prices
	It handles missing records differently now.  It flags them and puts a gap
	in the graph.
	
	Revision 1.3  2005/06/15 15:40:23  prices
	Some changes in how the driver checks for bad readings.  Also how it displays the results of that.
	
	Revision 1.2  2005/06/09 03:30:46  prices
	Put in a temporary log10() around the moisture sensor data.
	
	Revision 1.1  2005/06/07 01:32:05  prices
	The history has been moved over to the new system.  Although I may move the
	control table at the top back to manual forms.  I can get it smaller if I do
	that.
	
	Revision 1.22  2005/04/25 22:26:59  prices
	Lots of documentation changes and changes to remove all traces of DragonFlyPortal.
	
	Revision 1.21  2005/04/23 18:33:23  prices
	Modified to remove dependency on DragonFlyPortal
	
	Revision 1.20  2005/04/21 20:07:34  prices
	It now includes parts of dragonflyportal if they are needed.
	
	Revision 1.19  2005/04/05 13:37:27  prices
	Added lots of documentation.
	
	Revision 1.18  2005/02/18 19:45:29  prices
	It now strips commas out of the labels.
	
	Revision 1.17  2005/01/18 20:44:56  prices
	Rebuilding stuff to display moisture sensors.
	
*/
/**
 *	@cond WEBDOC
 */

	$hTable = new dfTable('History', array('id' => 'History', 'name' => 'History'));


	$sep = "";
	$labels = array();
	$labelFormat = array();
   for ($i = 0; $i < $devInfo["ActiveSensors"]; $i++) {
		if (isset($devInfo["DisplayOrder"][$i])) {
			$s = $devInfo["DisplayOrder"][$i];
		} else {
			$s = $i;
		}

		if (!empty($useLocation["Loc".($i)])) {
	    	$labels[$i] = $useLocation["Loc".($s)];
		} else if (is_array($devInfo["Labels"])) {
	    	$labels[$i] = $devInfo["Labels"][$s].($i);;
		} else {	
	    	$labels[$i] = "Sensor".$i;			
		}
		$labels[$i] = str_replace(",", "-", $labels[$i]);
		$labelFormat[$i] = array('style' => 'text-align: center; white-space: normal;');
		$sep = ",";
   }

	$tableHeader = array('Date' => 'Date');
	$tableFormat = array('Date' => array('style' => 'text-align: center; white-space: nowrap;'));
	$tableHeader = array_merge($tableHeader, $labels);
	$tableFormat = array_merge($tableFormat, $labelFormat);
	$fill = '<span style="color: darkred;">Bad</span>';
	$hTable->createList($tableHeader, $fill, $headerPeriod);

	$max1 = -5000000;
	$min1 = 5000000;
	$max2 = -500000000;
	$min2 = 500000000;
	$dates = array();
	$data = array(array());
	$count = 0;
	$table = array();

//	$Label1 = "Temperature";
//	$Units1 = "&#176;C";
	$Label1 = NULL;
	$Units1 = NULL;

	$Label2 = NULL;
	$Units2 = NULL;
//	$Label2 = "Resistance";
//	$Units2 = "k Ohms";

	$count = 0;
	$lastDate = FALSE;
	
	foreach($useHistory as $hist) {
		$tRow = array();
//		$dates[] = $tRow['Date'] = date($dateformat, strtotime($hist["Date"]));

		for ($i = 0; $i < $devInfo["ActiveSensors"]; $i++) {
			if ($hist["Data".$i] !== NULL) {

				if ($Units1 === NULL) {
					$Units1 = trim($devInfo["Units"][$i]);
					$Label1 = trim($devInfo["Labels"][$i]);
				}
				if ($Units1 == "Counts") $decimal_places = 0;
				if (trim(strtoupper($devInfo["Units"][$i])) == strtoupper($Units1)) {
					if ($Units1 == "&#176;C") {
						$d = CtoF($hist["Data".$i]);
					} else {
						$d = $hist["Data".$i];
					}
					if ($d > $max1) $max1 = $d;
					if ($d < $min1) $min1 = $d;
				} else {
					if ($Units2 === NULL) {
						$Units2 = trim($devInfo["Units"][$i]);
						$Label2 = trim($devInfo["Labels"][$i]);
					}
					if (trim(strtoupper($devInfo["Units"][$i])) == strtoupper($Units2)) {
						$d = $hist["Data".$i];
						// Put in temporarily to view history data for moisture sensors.
//						$d = log($d);
						if ($d > $max2) $max2 = $d;
						if ($d < $min2) $min2 = $d;
					} else {
						$d = $hist["Data".$i];
					}
				}	

			} else {
				$d = NULL;
			}

			$tRow[$i] = number_format($d, $decimal_places);
			if ($d === NULL) $d = 'x';  // This causes the chart to skip this value
			$data[$i][] = $d;
  		}


		$theDate = strtotime($hist["Date"]);
		if ($lastDate !== FALSE) {
			$pollInt = ($devInfo['PollInterval']*60);
			$badRows = 0;
			while ($theDate < ($lastDate - ($pollInt*2))) {
				$lastDate -= $pollInt;
				$dates[] = date($dateformat, $lastDate);
				for ($i = 0; $i < $devInfo["ActiveSensors"]; $i++) $data[$i][] = 'x';// This causes the chart to skip this value
//				$hTable->addListRow(array('Date' => date($dateformat, $lastDate)));
				$count++;
				$badRows++;
			}
			if ($Show != 'graph') {
				if ($badRows > 1) $hTable->addListDividerRow($badRows.' Missing Records', array('class' => 'error'));
			}
		}
		$lastDate = $theDate;
		$dates[] = $tRow['Date'] = date($dateformat, $theDate);

		if ($Show != 'graph') {
			$hTable->addListRow($tRow);
		}
		$count++;
	}

	if ($Units1 == "&#176;C") $Units1 = "&#176;F";


	if ($Show != 'table') {
		// The graph needs them opposite of the table.
		foreach($data as $key => $d) {
			$data[$key] = array_reverse($d);
		}
		$dates = array_reverse($dates);
		$max1 += 3;
		$min1 -= 3;
	}

	
	if (!isset($Title)) $Title = "History for device ".$DeviceID;
      	
   if ($max1 < $min1) {
   	$max1 = 0;
   	$min1 = 0;
   }
   if ($max2 < $min2) {
  		$max2 = 0;
  		$min2 = 0;
  	}


	if ($Show != 'table') {
		$SeriesCount = $devInfo["ActiveSensors"];
		include(dirname(__FILE__)."/../graph.inc.php");
	}
	if ($Show != 'graph') {
		$hTable->finishList($tableFormat);
		print $hTable->toHTML();
	}
//	include(dirname(__FILE__)."/table.inc.php");


	function CtoF($c) {
		return(((9*$c)/5)+32);
	}

	function FtoC($f) {
		return((5*($f-32))/9);
	}
/**
 *	@endcond
 */
	
?>
