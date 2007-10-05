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

	@file history/history.inc.php
	@brief File that should be included if you want to print out the history graph/table.

	This file automatically decided which history driver to use and prints out the
	graph and table.

	$Log: history.inc.php,v $
	Revision 1.34  2005/10/18 20:13:46  prices
	Periodic
	
	Revision 1.33  2005/08/10 13:47:19  prices
	Periodic commit.
	
	Revision 1.32  2005/06/15 23:29:16  prices
	The user can now specify the table or the graph or both.
	
	Revision 1.31  2005/06/07 01:32:05  prices
	The history has been moved over to the new system.  Although I may move the
	control table at the top back to manual forms.  I can get it smaller if I do
	that.
	
	Revision 1.30  2005/05/31 20:51:25  prices
	Fixes and additions.
	
	Revision 1.29  2005/05/03 14:34:34  prices
	Added '' in the where clause for retrieving the device information so that
	the database knows it is a string, and not a column name.
	
	Revision 1.28  2005/04/25 22:26:59  prices
	Lots of documentation changes and changes to remove all traces of DragonFlyPortal.
	
	Revision 1.27  2005/04/23 18:33:23  prices
	Modified to remove dependency on DragonFlyPortal
	
	Revision 1.26  2005/04/05 13:37:27  prices
	Added lots of documentation.
	
	Revision 1.25  2005/01/18 20:44:56  prices
	Rebuilding stuff to display moisture sensors.
	
*/
/**
 *	@cond WEBDOC
 */

//	$pDays = $userPref->getPref('HUGnetHistoryDays');
	if (isset($_REQUEST["days"])) {
		$days = $_REQUEST["days"];
		if ($days != $pDays) {
//			$userPref->setPref('HUGnetHistoryDays', (int)$days);
		}
	} else {
		if (empty($pDays) || !is_numeric($pDays)) {
			$days = 1;
		} else {
			$days = $pDays;
		}
	}

//	$pType = $userPref->getPref('HUGnetHistoryType');
	if (isset($_REQUEST["Type"]) && !empty($_REQUEST["Type"])) {
		$Type = $_REQUEST["Type"];
		if ($Type != $pType) {
//			$userPref->setPref('HUGnetHistoryType', $Type);
		}
	} else {
		if (empty($pType)) {
			$Type = 'History';
		} else {
			$Type = $pType;
		}
	}

//	$pShow = $userPref->getPref('HUGnetHistoryShow');
	if (isset($_REQUEST["Show"]) && !empty($_REQUEST["Show"])) {
		$Show = $_REQUEST["Show"];
		if ($Show != $pShow) {
//			$userPref->setPref('HUGnetHistoryShow', $Show);
		}
	} else {
		if (empty($pShow)) {
			$Show = 'Both';
		} else {
			$Show = $pShow;
		}
	}


//	$pPlaces = $userPref->getPref('HUGnetPrecision');
	if (empty($pPlaces) || !is_numeric($pPlaces)) {
		$decimal_places = 4;
	} else {
		$decimal_places = $pPlaces;
	}

	if (!(isset($StartDate) || $Bare)) {
		if (is_array($_REQUEST["StartDate"])) {
			$year = (isset($_REQUEST['StartDate']['Y'])) ? $_REQUEST['StartDate']['Y'] : (int) date("Y");
			$month = (isset($_REQUEST['StartDate']['Y'])) ? $_REQUEST['StartDate']['F'] : (int) date("m");
			$day = (isset($_REQUEST['StartDate']['Y'])) ? $_REQUEST['StartDate']['d'] : (int) date("d");
			$hour = (isset($_REQUEST['StartDate']['Y'])) ? $_REQUEST['StartDate']['h'] : (int) date("h");
			$min = (isset($_REQUEST['StartDate']['Y'])) ? $_REQUEST['StartDate']['i'] : (int) date("i");
			$am = (isset($_REQUEST['StartDate']['Y'])) ? $_REQUEST['StartDate']['a'] : (int) date("a");
		
			$StartDate = $year.'-'.$month.'-'.$day.' '.$hour.':'.$min.' '.$am;
			$StartDate = date("Y-m-d H:i:s", strtotime($StartDate));
			$_SESSION["HUGnet"]["HistoryStart"] = $StartDate;
		} else if (is_array($_SESSION["HUGnet"]["HistoryStart"])) {

			$StartDate = $_SESSION["HUGnet"]["HistoryStart"];
		} else if (isset($devInfo["LastHistory"]) && ($devInfo["LastHistory"] != "0000-00-00 00:00:00")) {
			$StartDate = $devInfo["LastHistory"];
		} else if (isset($devInfo["LastPoll"]) && ($devInfo["LastPoll"] != "0000-00-00 00:00:00")) {
			$StartDate = $devInfo["LastPoll"];
		} else {	
			$StartDate = date("Y-m-d H:i:s");
		}
	}
	
	if (!(isset($EndDate) || $Bare)) {
		if (isset($_REQUEST["EndDate"])) {
			$EndDate = $_REQUEST["StartDate"];
		} else {
			$EndDate =  date("Y-m-d H:i:s", strtotime($StartDate) - (86400*$days));
		}
	}


	if (!is_array($devInfo)) {
		$endpoint->device->reset();
		if (isset($DeviceID)) {
			$endpoint->device->setWhere("DeviceID='".$DeviceID."'");
			$devInfo = $endpoint->device->getAll();
			$devInfo = $devInfo[0];
		} else if (isset($DeviceKey)) {
			$devInfo = $endpoint->device->get($DeviceKey);
		}
		$devInfo = $endpoint->DriverInfo($devInfo);
	}

	$selectdateformat = "d F Y  h:i a";
	switch(strtoupper($Type)) {
		case "15MIN":
			$dateformat = "g:ia m/d";
			$tabledateformat = "m/d/Y g:ia";
			break;
		case "HOURLY":
			$dateformat = "ga m/d";
			$tabledateformat = "m/d/Y ga";
			$selectdateformat = "d F Y  h a";
			break;
		case "DAILY":
			$dateformat = "m/d";
			$tabledateformat = "m/d/Y";
			$selectdateformat = "d F Y";
			break;
		case "WEEKLY":
			$dateformat = "m/d/Y";
			$tabledateformat = "m/d/Y";
			$selectdateformat = "d F Y";
			if ($days < 7) $days = 7;
			break;
		case "MONTHLY":
			$dateformat = "m";
			$tabledateformat = "m/Y";
			$selectdateformat = "F Y";
			if ($days < 31) $days = 31;
			break;
		case "YEARLY":
			$dateformat = " Y ";
			$tabledateformat = " Y ";
			$selectdateformat = "Y";
			if ($days < 365) $days = 365;
			break;
	}
	
	
	$dateformat = "h:ia m/d";
	$tabledateformat = "m/d/Y h:i:s a";
	$averagetypes = array("History" => "History");
	switch(strtoupper($devInfo["MinAverage"])) {
		case "15MIN":
			$averagetypes["15Min"] = "15 Min Average";
		case "HOURLY":
			$averagetypes["Hourly"] = "Hourly Average";
		case "DAILY":
			$averagetypes["Daily"] = "Daily Average";
		case "WEEKLY":
			$averagetypes["Weekly"] = "Weekly Average";
		case "MONTHLY":
			$averagetypes["Monthly"] = "Monthly Average";
		case "YEARLY":
			$averagetypes["Yearly"] = "Yearly Average";
	}

	if (!isset($averagetypes[$Type])) $Type = "History";


   if (!isset($Title)) $Title = $devInfo["DeviceJob"]." (".$devInfo["DeviceLocation"].")";


	if ($Bare != TRUE) {
	
	
		// History type select box
		$typeForm = new HTML_QuickForm("devForm", 'get');
		$typeForm->addElement('hidden', 'DeviceKey', $devInfo['DeviceKey']);
    	$typeForm->addElement('hidden', 'module', $_REQUEST['module']);
	    $typeForm->addElement('hidden', 'hugnetengr_op', $_REQUEST['hugnetengr_op']);
		$typeForm->addElement('select', 'Type', 'Type:', $averagetypes, array('onChange' => 'submit();'));	

		// History type select box
		$showForm = new HTML_QuickForm("showForm", 'get');
    	$showForm->addElement('hidden', 'module', $_REQUEST['module']);
	    $showForm->addElement('hidden', 'hugnetengr_op', $_REQUEST['hugnetengr_op']);
		$showForm->addElement('hidden', 'DeviceKey', $devInfo['DeviceKey']);
		$options = array(
			'both' => 'Both',
			'graph' => 'Graph',
			'table' => 'Table',
		);
		$showForm->addElement('select', 'Show', 'Show:', $options, array('onChange' => 'submit();'));	

		// Date select box
		$dateForm = new HTML_QuickForm('dateForm', 'get');
    	$dateForm->addElement('hidden', 'module', $_REQUEST['module']);
	    $dateForm->addElement('hidden', 'hugnetengr_op', $_REQUEST['hugnetengr_op']);
		$dateForm->addElement('hidden', 'DeviceKey', $devInfo['DeviceKey']);
		$options = array(
			'language' => 'en',
			'format' => $selectdateformat,
			'minYear' => 2003,
			'maxYear' => date("Y"),
		);
		$date =& $dateForm->createElement('date', 'StartDate', 'Start:', $options);
		$go =& $dateForm->createElement('submit', 'dateSubmit', 'Go');
		$dateForm->addGroup(array($date, $go),NULL,  'Start:');
		$dateForm->setDefaults(array('StartDate' => strtotime($StartDate)));	
	
		// Days select box
		$daysForm = new HTML_QuickForm('daysForm', 'get');
   	    $daysForm->addElement('hidden', 'module', $_REQUEST['module']);
	    $daysForm->addElement('hidden', 'hugnetengr_op', $_REQUEST['hugnetengr_op']);
		$daysForm->addElement('hidden', 'DeviceKey', $devInfo['DeviceKey']);
		$d =& $daysForm->createElement('text', 'days', 'Days:', array("size" => '5', 'maxlength' => '5'));
		$d->setValue($days);
		$daysGo =& $daysForm->createElement('submit', 'daysSubmit', 'Go');
		$daysForm->addGroup(array($d, $daysGo),NULL,  'Days:');
		$daysForm->setConstants(array('days' => $days));	
	
		if (isset($_REQUEST['daysSubmit']) && $daysForm->validate()) {
		
		}
	
	
		// Now lets build the tables
		$topTable = new HTML_Table();
		$topTable->setCellContents(0, 1, $typeForm->toHTML());	
		$topTable->setCellContents(0, 2, $showForm->toHTML());	
		$topTable->setCellContents(0, 3, '<a href="'.$_SERVER["SCRIPT_NAME"].'?DeviceKey='.$devInfo['DeviceKey'].'">Current</a>');		

		$botTable = new HTML_Table();
		$botTable->setCellContents(0, 0, $daysForm->toHTML());	
		$botTable->setCellContents(0, 1, $dateForm->toHTML());	
	
		// Now we put it all together.
		print '<div class="bg_medium" style="white-space: nowrap; padding:3px; margin: 5px;">';
		print $topTable->toHTML();
		print $botTable->toHTML();	
		print '</div>';
	} // 


	if ($Type != "History") {
		$history = &$endpoint->drivers[$devInfo["Driver"]]->average;
		if (is_object($history)) {
			$history->reset();
			$history->addWhere("Type='".strtoupper($Type)."'");
		}
	} else {
		$history = &$endpoint->drivers[$devInfo["Driver"]]->history;
		if (is_object($history)) {
			$history->reset();
		}
	}

	if (is_object($history)) {
		$history->addWhere("Date>='".$EndDate."'");
		$history->addWhere("Date<='".$StartDate."'");
		$history->addWhere("DeviceKey='".$devInfo["DeviceKey"]."'");
		$history->addOrder("Date", TRUE);
		$useHistory = $history->getAll();

	}

	$loc = &$endpoint->drivers[$devInfo["Driver"]]->location;
	if (is_object($loc)) {
		$useLocation = $loc->get($devInfo["DeviceKey"]);
	} else {
		$useLocation = array();
	}

	if (!is_array($useHistory)) $useHistory = array();

//	$headerPeriod = $userPref->getPref('hugnetHeaderPeriod');
	if ($headerPeriod === NULL) {
		$headerPeriod = 50;
	}
	
	print '<div>';
	if (!include("endpoints/".$devInfo['Driver'].".inc.php"))
	{
		print "I don't know how to display this data<br/>";
	}
	print '</div>';

/**
 *	@endcond
 */

?>
