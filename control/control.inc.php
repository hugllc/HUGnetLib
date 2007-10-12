<?php
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
