<?php
	/**
		$Id: e00391200.inc.php 52 2006-05-14 20:51:23Z prices $
		@file control/endpoints/e00391201.inc.php	

		$Log: e00391200.inc.php,v $
		Revision 1.1  2005/05/31 18:12:54  prices
		Inception.
		
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