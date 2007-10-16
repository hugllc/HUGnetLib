<?php
/**
 *   <pre>
 *   HUGnetLib is a library of HUGnet code
 *   Copyright (C) 2007 Hunt Utilities Group, LLC
 *   
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 3
 *   of the License, or (at your option) any later version.
 *   
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *   
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *   </pre>
 *
 *   @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *   @package HUGnetLib
 *   @subpackage Endpoints
 *   @copyright 2007 Hunt Utilities Group, LLC
 *   @author Scott Price <prices@hugllc.com>
 *   @version $Id$    
 *
 */

	include(dirname(__FILE__).'/../group.inc.php');
//print get_stuff($devInfo);
	$form = new HTML_QuickForm('fetControl');
	$form->addElement('hidden', 'DeviceKey');
	$form->addElement('hidden', 'noFetchSetup', TRUE);
	$form->addElement('header', NULL, 'Device Specific Options');
	$form->addElement('text', 'TimeConstant', 'Time Constant:', array('size' => 3, 'maxlength' => 3));
	foreach($devInfo['Types'] as $sensor => $type) {

		$options = $endpoint->drivers[$devInfo['Driver']]->sensorTypes;
		$form->addElement('select', 'Types['.$sensor.']', "Sensor ".$sensor." Type:", $options);
	}
	$form->addRule('TimeConstant', 'Time Constant can not be empty', 'required', NULL, 'client');	
	$form->addRule('TimeConstant', 'Time Constant must be numeric', 'numeric', NULL, 'client');	
	$form->setDefaults($devInfo);
	$form->addElement('submit', 'postSetup', 'Update');
	if (isset($_REQUEST['postSetup']) && $form->validate()) {

		$pktData = (is_array($_REQUEST['Types'])) ? $_REQUEST['Types'] : array();
		$pktData[-1] = (int) $_REQUEST['TimeConstant'];
		ksort($pktData);
		foreach($pktData as $key => $val) {
			$pktData[$key] = (int) $val;		
		}
		$return = $endpoint->setConfig($devInfo, 4, $pktData);

		if ($return) header("Location: ".$_SERVER['PHP_SELF']."?DeviceKey=".$devInfo['DeviceKey']);

	}
	print $text.$form->toHTML();
	

?>