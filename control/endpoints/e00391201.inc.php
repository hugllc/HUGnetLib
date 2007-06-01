<?php
	/**
		$Id: e00391201.inc.php 52 2006-05-14 20:51:23Z prices $
		@file control/endpoints/e00391201.inc.php	

		$Log: e00391201.inc.php,v $
		Revision 1.3  2005/10/18 20:13:46  prices
		Periodic
		
		Revision 1.2  2005/08/10 13:47:19  prices
		Periodic commit.
		
		Revision 1.1  2005/05/31 18:12:54  prices
		Inception.
		
	*/

	include(dirname(__FILE__).'/../group.inc.php');
	$form = new HTML_QuickForm('fetControl');
    $form->addElement('hidden', 'module', $_REQUEST['module']);
	$form->addElement('hidden', 'hugnetengr_op', $_REQUEST['hugnetengr_op']);
	$form->addElement('hidden', 'DeviceKey');
	$form->addElement('hidden', 'noFetchSetup', TRUE);
	for($fet = 0; $fet < 4; $fet++) {
		$location = (isset($devInfo['Location'][$fet])) ? $devInfo['Location'][$fet] : "FET ".$fet;
		$form->addElement('header', NULL, $location." : ".$devInfo['FET'.$fet.'pMode']." Mode");
		$options = array(0 => 'Digital', 1 => 'Analog - Hi Z', 2 => 'Analog - Voltage', 3 => 'Analog - Current');
		$form->addElement('select', 'FET'.$fet.'Mode', "Mode:", $options);
		switch($devInfo['FET'.$fet.'Mode'])
		{
			case 0:
				$radio = array();
				$radio[] = $form->createElement('radio', 'FET'.$fet, NULL, 'Off', 0);
				$radio[] = $form->createElement('radio', 'FET'.$fet, NULL, 'On', 128);
				$form->addGroup($radio, NULL, "Power:");
				break;
			case 1:
				break;
			default:
				$form->addElement('text', 'FET'.$fet, "Set Point:", array('size' => 3, 'maxlength' => 3));
				$form->addRule('FET'.$fet, "Set Point must be numeric", 'numeric', NULL, 'client');
				$form->addRule('FET'.$fet, "Set Point can not be empty", 'required', NULL, 'client');
				$form->addElement('select', 'FET'.$fet.'Mult', "Multiplier:", array(0,1,2,3));
				break;
		}
	}
	$form->setConstants($devInfo);
	$form->addElement('submit', 'postSetup', 'Update');
	if (isset($_REQUEST['postSetup']) && $form->validate()) {
		$pktData = array();
		$mode = 0;
		for($i = 0; $i < 4; $i++) {
			$data = (int) $_REQUEST['FET'.$i.'Mode'] & 0x03;
			$data <<= (2*$i);
			$mode |= $data;			
		}
		$pktData[] = $mode;
		for($i = 0; $i < 4; $i++) {
			switch($_REQUEST['FET'.$fet.'Mode'])
			{
				case 0:
					if ($_REQUEST['FET'.$i] == 0) {
						$pktData[] = 0;
					} else {
						$pktData[] = 0x80;
					}
					break;
				case 1:
					$pktData[] = 0;
					break;
				default:
					$pktData[] = (int) $_REQUEST['FET'.$i];
					break;
			}
		}

		for($i = 0; $i < 4; $i++) {
			$pktData[] = (int) $_REQUEST['FET'.$i.'Mult'];
		}
		$return = $endpoint->setConfig($devInfo, 4, $pktData);

        $url = getMyURL(array("DeviceKey"))."?module=".$_REQUEST['module']."&hugnetengr_op=".$_REQUEST['hugnetengr_op']."&DeviceKey=".$devInfo['DeviceKey']."&mode=control";
		if ($return) header("Location: ".$url);

	}
	print $text.$form->toHTML();
//die("THERE");	

?>