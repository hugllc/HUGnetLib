<?php
/**
	$Id: historycompare.inc.php 52 2006-05-14 20:51:23Z prices $
	@file history/historycompare.inc.php
	@brief file for comparing histories.
	@warning Test file.  Not for production use.
	
	$Log: historycompare.inc.php,v $
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

	$dev1 = explode(",", $Device1Compare);
	$dev2 = explode(",", $Device2Compare);
	$out1 = explode(",", $Device1Output);
	$out2 = explode(",", $Device2Output);
	
	if (count($dev1) < count($dev2)) { 
		$ActiveSensors = count($dev1);
	} else {
		$ActiveSensors = count($dev2);
	}
	$ActiveSensors += count($out1);
	$ActiveSensors += count($out2);

	$history = new e00391200_history($hnservers);
	$history->SetRange("Date", $StartDate, $EndDate);
	
	$max = -50;
	$min = 150;
	$max2 = -50;
	$min2 = 150;
	$table1 = array();
	$table2 = array();
	$tdates = array();
	$tc1 = array();
	$tc2 = array();
	$warning = "";

	$endpoint->device->lookup($Device2Name, "DeviceID");
	$DevKey2 = $endpoint->device->lookup[0]["DeviceKey"];
	$history->lookup($endpoint->device->lookup[0]["DeviceKey"], "DeviceKey");
	foreach ($history->lookup as $hist) {
      for ($i = 0; $i < $endpoint->device->lookup[0]["ActiveSensors"]; $i++) {
			$table2[date($tabledateformat, strtotime($hist["Date"]))][$i] = $hist["Data".$i];
      }
		$tc1[date($tabledateformat, strtotime($hist["Date"]))] = $hist["TimeConstant"];
	}

	$endpoint->device->lookup($Device1Name, "DeviceID");
	$DevKey1 = $endpoint->device->lookup[0]["DeviceKey"];
	$history->lookup($endpoint->device->lookup[0]["DeviceKey"], "DeviceKey");

	foreach ($history->lookup as $hist) {
		$tdates[] = date($tabledateformat, strtotime($hist["Date"]));
      for ($i = 0; $i < $endpoint->device->lookup[0]["ActiveSensors"]; $i++) {
			$table1[date($tabledateformat, strtotime($hist["Date"]))][$i] = $hist["Data".$i];
      }
		$tc2[date($tabledateformat, strtotime($hist["Date"]))] = $hist["TimeConstant"];
	}
	
	
	$table = array();
	$data = array();
	$dates = array();
	$range = array();

	$count = 0;
   foreach ($tdates as $thedate) {
   	if (isset($table1[$thedate]) && isset($table2[$thedate])) {
   		if ($tc1[$thedate] == $tc2[$thedate]) {
				$dates[] = date($dateformat, strtotime($thedate));
		      foreach(explode(",", $Device1Output) as $k => $i) {
		   		$table[$thedate][$i] = $table1[$thedate][$i];
		   		$data[$i][] = $table[$thedate][$i];
					$range[$i] = 2;
		      	if ($table[$thedate][$i] > $max2) $max2 = $table[$thedate][$i];
		      	if ($table[$thedate][$i] < $min2) $min2 = $table[$thedate][$i];
		      }
		      foreach(explode(",", $Device2Output) as $k => $i) {
		   		$table[$thedate][$i+10] = $table2[$thedate][$i];
		   		$data[$i+10][] = $table[$thedate][$i+10];
					$range[$i+10] = 2;
		      	if ($table[$thedate][$i+10] > $max2) $max2 = $table[$thedate][$i+10];
		      	if ($table[$thedate][$i+10] < $min2) $min2 = $table[$thedate][$i+10];
		      }
		      		      foreach($dev1 as $k => $i) {
		   		$table[$thedate][$i+20] = $table1[$thedate][$i] - $table2[$thedate][$dev2[$k]];
		   		$data[$i+20][] = $table[$thedate][$i+20];
					$range[$i+20] = 1;
		      	if ($table[$thedate][$i+20] > $max) $max = $table[$thedate][$i+20];
		      	if ($table[$thedate][$i+20] < $min) $min = $table[$thedate][$i+20];
		      }
		      $count++;
   		} else {
   			$twarning = "Some records were omitted because the TimeConstants didn't match<BR>";
   		}
   	}
   }
	$warning .= $twarning;

	$max += 3;
	$min -= 3;
	$max2 += 3;
	$min2 -= 3;
	$dataprint = "";
	$key = 0;
	foreach($data as $k => $v) {
	   if (is_array($v)) {
			$dataprint .= "<param name=series".$key."_values value=\"".implode(",",$v)."\">\n";
			$dataprint .= "<param name=seriesRange_".$key." value=\"".$range[$k]."\">\n";
			$key++;
		}
	}
	$loc = new e00391200_location($hnservers);
	$loc->lookup($DevKey1, "DeviceKey");

	$labels = array();
   foreach($out1 as $k => $i) {
 		if (!empty($loc->lookup[0]["Loc".$i])) {
	    	$labels[$i] = $loc->lookup[0]["Loc".$i]." (".$Device1Name.")";
		} else {
	    	$labels[$i] = "Sensor ".($i+1)." (".$Device1Name.")";
		}
   }
	$loc->lookup($DevKey2, "DeviceKey");
   foreach($out2 as $k => $i) {
 		if (!empty($loc->lookup[0]["Loc".$i])) {
	    	$labels[$i+10] = $loc->lookup[0]["Loc".$i]." (".$Device2Name.")";
		} else {
	    	$labels[$i+10] = "Sensor ".($i+1)." (".$Device2Name.")";
		}
   }
	$loc->lookup($DevKey1, "DeviceKey");
   foreach($dev1 as $k => $i) {
 		if (!empty($loc->lookup[0]["Loc".$i])) {
	    	$labels[$i+20] = $loc->lookup[0]["Loc".$i]." (Diff)";
		} else {
	    	$labels[$i+20] = "Sensor ".($i+1)." (Diff)";			
		}
   }

	if (!isset($Title)) $Title = "History for device ".$DeviceID;


	$history->Select = array();	
	for ($i = 0; $i < $endpoint->device->lookup[0]["ActiveSensors"]; $i++) {
		$history->Select[] = "AVG(Data".$i.") as a".$i.", STD(Data".$i.") as s".$i.""; 
	}
	$history->SetRange("Date", "2003-11-06 12:30:00", date("Y-m-d H:i:s"));
	$history->lookup($DevKey1, "DeviceKey");
	$avg[0] = $history->lookup[0];
	$history->lookup($DevKey2, "DeviceKey");
	$avg[1] = $history->lookup[0];
   foreach($out1 as $k => $i) {
//		$table["Standard Dev"][$i] = number_format($avg[0]["s".$i], 2);
		$table["Average"][$i] = number_format($avg[0]["a".$i], 2);
   }
   foreach($out2 as $k => $i) {
//		$table["Standard Dev"][$i+10] = number_format($avg[1]["s".$i], 2);
		$table["Average"][$i+10] = number_format($avg[1]["a".$i], 2);
   }
   foreach($dev1 as $k => $i) {
//		$table["Standard Dev"][$i+20] = number_format($avg[0]["s".$i] - $avg[1]["s".$dev2[$k]], 2);
		$table["Average"][$i+20] = number_format($avg[0]["a".$i] - $avg[1]["a".$dev2[$k]], 2);
   }
	
      	
?>


<applet
code="com.objectplanet.chart.LineChartApplet"
archive="/applets/chart.jar"
width=590 height=400 VIEWASTEXT id=Applet1>
<param name=title value="<?php print $Title; ?>">
<param name=sampleCount value=<?php print $count; ?>>s
<param name=seriesCount value=<?php print $ActiveSensors; ?>>
<param name=seriesLabels value="<?php print implode(",", $labels); ?>">
<param name=sampleLabels value="<?php print implode(", ", $dates); ?>">
<?php
	print $dataprint;	

//<param name=rangeOn_2 value="false">
?>
<param name=rangeOn_2 value=true>

<param name=range value=<?php print $max; ?>>
<param name=lowerRange value=<?php print $min; ?>>
<param name=range_2 value=<?php print $max2; ?>>
<param name=lowerRange_2 value=<?php print $min2; ?>>
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
<param name=sampleColors value="black, blue, cyan, darkgray, gray, green, magenta, orange, pink, red, yellow, lightGray">

<param name=rangeAxisLabel_2 value="Absolute Temp (C)">
<param name=rangeAxisLabelAngle_2 value="270">
<param name=rangeAxisLabel value="Differential Temp (C)">
<param name=rangeAxisLabelAngle value="270">

<param name=sampleLabelAngle value="270">
<param name=sampleAxisLabel value="Time">
</applet>

<table>
<?php
	print "<p>".$warning."</p>";
	$table = array_reverse($table);
	print "   <tr>\n      <td class=\"header\">Time</td>";
	foreach($labels as $l) {
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
			print "      <td class=\"".$row."\" style=\"text-align: center;\">".$l."</td>";
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

