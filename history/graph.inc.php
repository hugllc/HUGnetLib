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
	@file history/graph.inc.php
	@brief Prints out a graph using the graphing applet.
	
	$Log: graph.inc.php,v $
	Revision 1.11  2005/10/18 20:13:46  prices
	Periodic
	
	Revision 1.10  2005/08/10 13:47:19  prices
	Periodic commit.
	
	Revision 1.9  2005/06/07 01:32:05  prices
	The history has been moved over to the new system.  Although I may move the
	control table at the top back to manual forms.  I can get it smaller if I do
	that.
	
	Revision 1.8  2005/05/31 20:51:25  prices
	Fixes and additions.
	
	Revision 1.7  2005/04/25 22:26:59  prices
	Lots of documentation changes and changes to remove all traces of DragonFlyPortal.
	
	Revision 1.6  2005/04/23 18:33:23  prices
	Modified to remove dependency on DragonFlyPortal
	
	Revision 1.5  2005/04/05 13:37:27  prices
	Added lots of documentation.
	
*/
/**
 *	@cond WEBDOC
 */
?>
<div style="text-align: center;">
<!--<applet code="com.objectplanet.chart.LineChartApplet" archive="/applets/chart.jar" style="margin: 1%; width: 98%;" height=500 VIEWASTEXT name=Applet1>-->
<applet code="com.objectplanet.chart.LineChartApplet" archive="/mod/hugnetengr/files/applets/chart.jar" width="100%" height="500" viewastext="viewastext" name="Applet1">
	<param name="title" value="<?php print $Title; ?>"/>
	<param name="sampleCount" value="<?php print $count; ?>"/>
	<param name="seriesCount" value="<?php print $devInfo["ActiveSensors"]; ?>"/>
	<param name="seriesLabels" value="<?php print implode(",", $labels); ?>"/>
	<param name="sampleLabels" value="<?php print implode(",", $dates); ?>"/>
<?php
	foreach($data as $key => $v) {
		if (is_array($v) && (count($v) > 0)) {
			 print "	<param name=\"series".($key)."_values\" value=\"".implode(",",$v)."\"/>\n";
		}
	}

//<param name="rangeOn_2 value="false">
?>
	<param name="rangeOn_2" value="true"/>

	<param name="range" value="<?php print $max1; ?>"/>
	<param name="lowerRange" value="<?php print $min1; ?>"/>
	<param name="range_2" value="<?php print $max2; ?>"/>
	<param name="lowerRange_2" value="<?php print $min2; ?>"/>
	<param name="rangeDecimalCount" value="0"/>
	<param name="titleOn" value="true"/>
	<param name="legendOn" value="true"/>

<?php
	for($i = 0; $i < $devInfo["ActiveSensors"]; $i++) {
		if (isset($devInfo["DisplayOrder"][$i])) {
			$s = $devInfo["DisplayOrder"][$i];
		} else {
			$s = $i;
		}
		print "<param name=\"seriesRange_".$i."\" value=\"";
		if (trim(strtoupper($devInfo["Units"][$s])) == strtoupper($Units2)) {
			print "2";
		} else {
			print "1";
		}
		print "\"/>\n";
	}
?>		


	<param name="legendPosition" value="right"/>
	<param name="sampleLabelsOn" value="true"/>
	<param name="autoLabelSpacingOn" value="true"/>
	<param name="rangeAdjusterOn" value="true"/>
	<param name="sampleScrollerOn" value="true"/>

	<param name="valueLinesOn" value="true"/>
	<param name="background" value="lightgray"/>
	<param name="frameOn" value="false"/>

	<param name="valueLinesColor" value="lightgray"/>
	<param name="sampleColors" value="black, blue, cyan, gray, green, magenta, orange, pink, red, yellow, lightGray"/>

	<param name="rangeAxisLabel_2" value="<?php print $Label2." (".$Units2.")"; ?>"/>
	<param name="rangeAxisLabelAngle_2" value="270"/>
	<param name="rangeAxisLabel" value="<?php print $Label1." (".$Units1.")"; ?>"/>
	<param name="rangeAxisLabelAngle" value="270"/>

	<param name="sampleLabelAngle" value="270"/>
	<param name="sampleAxisLabel" value="Time"/>
	
	<param name="rangeAdjusterOn" value="true"/>
	<param name="rangeAdjusterOn_2" value="true"/>
	<param name="rangeAdjusterPosition" value="left"/>
	<param name="rangeAdjusterPosition_2" value="right"/>
	<param name="sampleScrollerOn" value="true"/>
	
</applet>
</div>
<?php
/**
 *	@endcond
 */
?>