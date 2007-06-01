<?php
/**
	$Id: control.inc.php 52 2006-05-14 20:51:23Z prices $

	@file control/control.inc.php
	@brief File that should be included if you want to print out the history graph/table.

	$Log: control.inc.php,v $
	Revision 1.7  2005/10/18 20:13:46  prices
	Periodic
	
	Revision 1.6  2005/08/10 13:47:19  prices
	Periodic commit.
	
	Revision 1.5  2005/06/17 14:02:15  prices
	Fixed some problems in how it retrieved the data from the endpoint.
	
	Revision 1.4  2005/06/04 16:23:06  prices
	Fixed how it gets the config and updates it.
	
	Revision 1.3  2005/06/04 01:45:28  prices
	I think I finally got everything working again from changing the packet
	structure to accept multiple packets at the same time.
	
	Revision 1.2  2005/06/01 23:24:52  prices
	Many fixes.
	
	Revision 1.1  2005/05/31 18:12:54  prices
	Inception.
	
	
*/
/**
 *	@cond WEBDOC
 */

	if (!@include("endpoints/".$devInfo['Driver'].".inc.php"))
	{
		print "I don't know how to control this endpoint<br>";
	}
	
//	die ("ASDF");
/**
 *	@endcond
 */

?>
