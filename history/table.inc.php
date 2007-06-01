<?php
/**
	$Id: table.inc.php 52 2006-05-14 20:51:23Z prices $

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