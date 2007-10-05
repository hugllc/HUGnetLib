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

	@file history/table.inc.php
	@brief Prints out a data table.

	$Log: table.inc.php,v $
	Revision 1.3  2005/04/25 22:26:59  prices
	Lots of documentation changes and changes to remove all traces of DragonFlyPortal.
	
	Revision 1.2  2005/04/05 13:37:27  prices
	Added lots of documentation.
	
	Revision 1.1  2005/01/18 20:44:56  prices
	Rebuilding stuff to display moisture sensors.
	
*/
/**
 *	@cond WEBDOC
 */
?>
<table>
<?php
	$table = array_reverse($table);
	$index = 0;
	foreach($table as $key => $v) {

		if (($index++ % 20) == 0) {		
			print "   <tr>\n      <td class=\"header\">Time</td>";
			foreach($labels as $l) {
				print "      <td class=\"header\">".$l."</td>";
			}
			print "   </tr>\n";
		}

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