<?php
	/**
		$Id: e00391202.inc.php 52 2006-05-14 20:51:23Z prices $
		@file control/endpoints/e00391201.inc.php	

		$Log: e00391202.inc.php,v $
		Revision 1.1  2005/08/19 16:32:43  prices
		New for the relay board.
		
		Revision 1.2  2005/08/10 13:47:19  prices
		Periodic commit.
		
		Revision 1.1  2005/05/31 18:12:54  prices
		Inception.
		
	*/

	include(dirname(__FILE__).'/../group.inc.php');
	$form = new HTML_QuickForm('relayControl');
	$form->addElement('hidden', 'DeviceKey');
	$form->addElement('hidden', 'noFetchSetup', TRUE);
	for($fet = 0; $fet < 2; $fet++) {
		$location = (isset($devInfo['Location'][$fet])) ? $devInfo['Location'][$fet] : "Relay ".$fet;
		$form->addElement('header', NULL, $location);
		$radio = array();
		$radio[] = $form->createElement('radio', 'RELAY'.$fet, NULL, 'Off', 0);
		$radio[] = $form->createElement('radio', 'RELAY'.$fet, NULL, 'On', 128);
		$form->addGroup($radio, NULL, "Power:");
	}
	$form->setConstants($devInfo);
	$form->addElement('submit', 'postSetup', 'Update');
	if (isset($_REQUEST['postSetup']) && $form->validate()) {
		$pktData = array();
		for($i = 0; $i < 2; $i++) {
			if ($_REQUEST['RELAY'.$i] == 0) {
				$pktData[] = 0;
			} else {
				$pktData[] = 0x80;
			}
		}
		$return = $endpoint->setConfig($devInfo, 4, $pktData);

		if ($return) header("Location: ".$_SERVER['PHP_SELF']."?DeviceKey=".$devInfo['DeviceKey']);

	}
	print $text.$form->toHTML();
	

?>