<php
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

	@file history/history_e00391201.inc.php
	@brief History include that goes with the e00391201.inc.php driver.

	$Log: e00391201.inc.php,v $
	Revision 1.3  2005/08/10 13:47:19  prices
	Periodic commit.
	
	Revision 1.2  2005/06/15 23:29:16  prices
	The user can now specify the table or the graph or both.
	
	Revision 1.1  2005/06/07 01:32:05  prices
	The history has been moved over to the new system.  Although I may move the
	control table at the top back to manual forms.  I can get it smaller if I do
	that.
	
	Revision 1.12  2005/04/25 22:26:59  prices
	Lots of documentation changes and changes to remove all traces of DragonFlyPortal.
	
	Revision 1.11  2005/04/23 18:33:23  prices
	Modified to remove dependency on DragonFlyPortal
	
	Revision 1.10  2005/04/05 13:37:27  prices
	Added lots of documentation.
	
	Revision 1.9  2005/01/18 20:44:56  prices
	Rebuilding stuff to display moisture sensors.
	
*/
/**
 *	@cond WEBDOC
 */

	$hTable = new dfTable('History', array('id' => 'History', 'name' => 'History'));

	$labels = array();
	$labelFormat = array();
   for ($i = 0; $i < ($devInfo["ActiveSensors"]-1); $i+=2) {
		if (!empty($useLocation["Loc".($i/2)])) {
	    	$label = $useLocation["Loc".($i/2)];
		} else if (is_array($devInfo["Labels"])) {
	    	$label = $devInfo["Labels"][$i/2].($i+1);
		} else {	
	    	$label = "Sensor".$i;
		}
		$labels[$i] = str_replace(",", "-", $label)." Current";
		$labels[$i+1] = str_replace(",", "-", $label)." Voltage";
		$labelFormat[$i] = array('style' => 'text-align: center; white-space: normal;');
		$labelFormat[$i+1] = array('style' => 'text-align: center; white-space: normal;');
   }
   $i = $devInfo["ActiveSensors"]-1;
   $labels[$i] = $devInfo["Labels"][$i];
	$labelFormat[$i] = array('style' => 'text-align: center; white-space: normal;');
	$tableHeader = array('Date' => 'Date');
	$tableFormat = array('Date' => array('style' => 'text-align: center; white-space: nowrap;'));
	$tableHeader = array_merge($tableHeader, $labels);
	$tableFormat = array_merge($tableFormat, $labelFormat);
	$fill = '<span style="color: darkred;">Bad</span>';
	$hTable->createList($tableHeader, $fill, $headerPeriod);

	$max1 = 1;
	$min1 = 30;
	$max2 = .5;
	$min2 = 10;
	$dates = array();
	$data = array(array());
	$count = 0;
	$table = array();
	foreach ($useHistory as $hist) {
		$tRow = array();
      for ($i = 0; $i < $devInfo["ActiveSensors"]; $i++) {
			$d = $hist["Data".$i];
			if (trim(strtoupper($devInfo["Units"][$i])) == "A") {
				if ($d > $max2) $max2 = $d;
				if ($d < $min2) $min2 = $d;
			} else {
				if ($d > $max1) $max1 = $d;
				if ($d < $min1) $min1 = $d;
			}
			$tRow[$i] = $d;
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
 		$dates[] = $tRow['Date'] = date($dateformat, strtotime($hist["Date"]));
		if ($Show != 'graph') {
			$hTable->addListRow($tRow);
		}
		$count++;
	}
	$dataprint = "";

	if ($Show != 'table') {
		// The graph needs them opposite of the table.
		foreach($data as $key => $d) {
			$data[$key] = array_reverse($d);
		}

		$dates = array_reverse($dates);

	}


	if (!isset($Title)) $Title = "History for device ".$DeviceID;
     	
   $max2 *= 1.1;
   $max1 *= 1.1;
   $min2 *= .9;
   $min1 *= .9;

   
   $Label1 = "Voltage";
   $Label2 = "Current";
   $Units1 = "V";
   $Units2 = "A";
   
   
	if ($Show != 'table') {
	  	include(dirname(__FILE__)."/../graph.inc.php");
	}

	if ($Show != 'graph') {
	 	$hTable->finishList($tableFormat);
		print $hTable->toHTML();
	}
/**
 *	@endcond
 */

?>
