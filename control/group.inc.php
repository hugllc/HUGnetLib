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
		@file control/group.inc.php	

		$Log: group.inc.php,v $
		Revision 1.2  2005/10/18 20:13:46  prices
		Periodic
		
		Revision 1.1  2005/05/31 18:12:54  prices
		Inception.
		
	*/

 
	$gForm = new HTML_QuickForm('Group');

   	$gForm->addElement('hidden', 'module', $_REQUEST['module']);
	$gForm->addElement('hidden', 'hugnetengr_op', $_REQUEST['hugnetengr_op']);
	$gForm->addElement('hidden', 'DeviceKey');
	$gForm->addElement('hidden', 'noFetchSetup', TRUE);
	$gForm->addElement('header', NULL, "General Options");
	$gForm->addElement('text', 'DeviceGroup', "Group:", array('size' => 6, 'maxlength' => 6));
	$gForm->addElement('text', 'BoredomThreshold', "Boredom:", array('size' => 3, 'maxlength' => 3));
	$gForm->addRule('DeviceGroup', 'Group can not be empty', 'required', NULL, 'client');	
	$gForm->addRule('DeviceGroup', 'Group must be alphanumeric', 'alphanumeric', NULL, 'client');	
	$gForm->addRule('BoredomThreshold', 'Boredom can not be empty', 'required', NULL, 'client');	
	$gForm->addRule('BoredomThreshold', 'Boredom must be numeric', 'numeric', NULL, 'client');	

	$gForm->setDefaults($devInfo);
	$gForm->addElement('submit', 'postGroup', 'Update');


	if (isset($_REQUEST['postGroup']) && $gForm->validate()) {
		$DeviceGroup = (string) $_REQUEST['DeviceGroup'];
		$DeviceGroup = substr($DeviceGroup, 0, 6);
		$DeviceGroup = str_pad($DeviceGroup, 6, "0", STR_PAD_LEFT);
		$Boredom = (int) $_REQUEST['BoredomThreshold'];
		$return = $endpoint->setConfig($devInfo, 0, array($DeviceGroup, $Boredom));
		if ($return) header("Location: ".$_SERVER['PHP_SELF']."?DeviceKey=".$devInfo['DeviceKey']);

	}
	print $gForm->toHTML();
	

?>