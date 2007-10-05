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
	@file history/rawhistory.inc.php
	@brief Test file that prints out the raw history
		
	$Log: rawhistory.inc.php,v $
	Revision 1.5  2005/04/25 22:26:59  prices
	Lots of documentation changes and changes to remove all traces of DragonFlyPortal.
	
	Revision 1.4  2005/04/05 13:37:27  prices
	Added lots of documentation.
	
	
*/
/**
 *	@cond WEBDOC
 */
	if (!is_array($hnservers)) $hnservers = ""; 
	if (!isset($dateformat)) $dateformat = "h:ia m/d";
	if (!isset($tabledateformat)) $tabledateformat = "m/d/Y h:ia";

	if (!isset($EndDate)) $EndDate = date("Y-m-d H:i:s", time());
	if (!isset($StartDate)) $StartDate =  date("Y-m-d H:i:s", time() - (86400*1));

	$endpoint->device->lookup($DeviceID, "DeviceID");
	$history = new e00391200_history($hnservers);
	$history->SetRange("Date", $StartDate, $EndDate);
	$history->DefaultSortBy = "Date asc";
	$history->lookup($endpoint->device->lookup[0]["DeviceKey"], "DeviceKey");

	$max = -50;
	$min = 1000000000;
	$dates = array();
	$data = array(array());
	$count = 0;
	$table = array();
	foreach ($history->lookup as $hist) {
		$array = array_merge($endpoint->device->lookup[0], $hist);
		$hist = $endpoint->DecodeData($array);
		$dates[] = date($dateformat, strtotime($hist["Date"]));
		$table[date($tabledateformat, strtotime($hist["Date"]))][0] = $hist["DataIndex"];
		$table[date($tabledateformat, strtotime($hist["Date"]))][1] = $hist["TimeConstant"];
      for ($i = 0; $i < $endpoint->device->lookup[0]["ActiveSensors"]; $i++) {
	     	$data[$i+1][] = $hist[raw][($i)];
      	if ($hist[raw][$i] > $max) $max = $hist[raw][$i];
      	if ($hist[raw][$i] < $min) $min = $hist[raw][$i];
			$table[date($tabledateformat, strtotime($hist["Date"]))][$i+2] = $hist[raw][($i)];
      }
      $count++;
	}
	$max *= 1.05;
	$min *= .95;
	$dataprint = "";
//	foreach($data as $key => $v) {
	for ($key = 1; $key <= $endpoint->device->lookup[0]["ActiveSensors"]; $key++) {
		if (is_array($data[$key])) {
			$dataprint .= "<param name=series".($key-1)."_values value=\"".implode(",",$data[$key])."\">\n";
		}
	}
	$loc = new e00391200_location($hnservers);
	$loc->lookup($endpoint->device->lookup[0]["DeviceKey"], "DeviceKey");

	$tlabels[0] = "Index";
	$tlabels[1] = "Time Constant";
   for ($i = 0; $i < $endpoint->device->lookup[0]["ActiveSensors"]; $i++) {
		if (!empty($loc->lookup[0]["Loc".$i])) {
	    	$labels[$i+2] = $loc->lookup[0]["Loc".$i];
		} else {
	    	$labels[$i+2] = "Sensor ".$i+1;			
		}
		$tlabels[$i+2] = $labels[$i+2];
   }

	if (!isset($Title)) $Title = "History for device ".$DeviceID;
      	
   $fmax = ((9*$max)/5) +32;
   $fmin = ((9*$min)/5) +32;
?>


<applet
code="com.objectplanet.chart.LineChartApplet"
archive="/applets/chart.jar"
width=590 height=400 VIEWASTEXT id=Applet1>
<param name=title value="<?php print $Title; ?>">
<param name=sampleCount value=<?php print $count; ?>>
<param name=seriesCount value=<?php print $endpoint->device->lookup[0]["ActiveSensors"]; ?>>
<param name=seriesLabels value="<?php print implode(",", $labels); ?>">
<param name=sampleLabels value="<?php print implode(", ", $dates); ?>">
<?php
	print $dataprint;	

//<param name=rangeOn_2 value="false">
?>

<param name=range value=<?php print $max; ?>>
<param name=lowerRange value=<?php print $min; ?>>
<param name=rangeDecimalCount value=0>
<param name=titleOn value=true>
<param name=legendOn value=true>

<param name=legendPosition value=right>
<param name=sampleLabelsOn value=true>
<param name=autoLabelSpacingOn value=true>
<param name=rangeAdjusterOn value=true>
<param name=sampleScrollerOn value=true>

<param name=valueLinesOn value=true>
<param name=background value=lightgray>
<param name=frameOn value=false>

<param name=rangeAdjusterOn value="false">
<param name=rangeAdjusterOn_2 value="false">

<param name=valueLinesColor value=lightgray>
<param name=sampleColors value="black, blue, cyan, darkGray, gray, green, magenta, orange, pink, red, yellow, lightGray">

<param name=rangeAxisLabel value="Temp (C)">
<param name=rangeAxisLabelAngle value="270">

<param name=sampleLabelAngle value="270">
<param name=sampleAxisLabel value="Time">
</applet>

<table>
<?php
	$table = array_reverse($table);
	print "   <tr>\n      <td class=\"header\">Time</td>";
	foreach($tlabels as $l) {
		print "      <td class=\"header\">".$l."</td>";
	}
	print "   </tr>\n";
	foreach($table as $key => $v) {
		if ($row == "row1") {
			$row = "row2";
		} else {
			$row = "row1";
		}
		print "   <tr>\n      <td class=\"".$row."\" style=\"white-space: nowrap;\">".$key."</td>";
		foreach($v as $l) {
			print "      <td class=\"".$row."\" style=\"text-align: center;\">".dechex($l)."</td>";
		}
		print "   </tr>\n";
	}
	
?>
</table>		
<?php
/**
 *	@endcond
 */
?>
