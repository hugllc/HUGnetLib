<?php
	/**
		$Id$
		@file control/endpoints/e00391201.inc.php	

		$Log: e00392100.inc.php,v $
		Revision 1.7  2005/06/04 16:22:38  prices
		Removed some debug code
		
		Revision 1.6  2005/06/04 01:45:28  prices
		I think I finally got everything working again from changing the packet
		structure to accept multiple packets at the same time.
		
		Revision 1.5  2005/06/03 17:11:41  prices
		Lots of changes and fixes.
		
		Revision 1.4  2005/06/01 23:33:27  prices
		Add a printout.
		
		Revision 1.3  2005/06/01 23:24:52  prices
		Many fixes.
		
		Revision 1.2  2005/05/31 20:51:25  prices
		Fixes and additions.
		
		Revision 1.1  2005/05/31 18:12:54  prices
		Inception.
		
	*/

	include(dirname(__FILE__).'/../group.inc.php');
	require_once('lib/tables.inc.php');

	$driver = &$endpoint->drivers[$devInfo['Driver']];
	$returnTo = $_SERVER['PHP_SELF']."?DeviceKey=".$devInfo['DeviceKey'];

	if ($devInfo['FWPartNum'] == '0039-20-01-C') {
		$form = new HTML_QuickForm('fetControl');
		$form->addElement('hidden', 'DeviceKey', $DeviceKey);
//		$form->addElement('hidden', 'noFetchSetup', TRUE);
		$form->addElement('header', NULL, '0039-20-01-C HUGnet Controller Options');
		$form->addElement('header', NULL, 'HUGnet Power');
		for($i = 0; $i < 2; $i++) {
			$radio = array();
			$radio[] = $form->createElement('radio', 'HUGnetPower['.$i.']', NULL, 'Off', 0);
			$radio[] = $form->createElement('radio', 'HUGnetPower['.$i.']', NULL, 'On', 1);
			$form->addGroup($radio, NULL, "Output ".($i+1));
		}
		$form->addRule('TimeConstant', 'Time Constant can not be empty', 'required', NULL, 'client');	
		$form->addRule('TimeConstant', 'Time Constant must be numeric', 'numeric', NULL, 'client');	
		$form->setDefaults($devInfo);
		$form->addElement('submit', 'postPower', 'Update');
		if (isset($_REQUEST['postPower']) && $form->validate()) {
			$power = $driver->setPower($devInfo, $_REQUEST['HUGnetPower'][0], $_REQUEST['HUGnetPower'][1]);
			if ($power !== FALSE) header("Location: ".$returnTo);

		}
		print $form->toHTML();
	}
	$pForm = new HTML_QuickForm('ProgControl');
	$pForm->addElement('hidden', 'DeviceKey', $DeviceKey);
	$pForm->addElement('hidden', 'noFetchSetup', TRUE);
	$pForm->addElement('header', NULL, 'Program Control');
	if ($devInfo['bootLoader']) {
		$crc = $driver->GetApplicationCRC($devInfo);
		if ($crc == $devInfo['CRC']) {
			$pForm->addElement('submit', 'runProg', 'Run Application');
		} else {
			$pForm->addElement('static', NULL, NULL, '<span class="error">Bad Application ('.$crc.' != '.$devInfo['CRC'].').  Please reload</span>');
		}
	} else {
		$pForm->addElement('submit', 'crashProg', 'Crash Application');	
	}

	$cols = array(
		'load' => '',
		'FWPartNum' => "Firmware Part",
		'FirmwareVersion' => "Version",
		'FirmwareStatus' => "Status",
		'Date' => "Upload Date",
	);
	$format = array(
	);

	$table = new dfTable();
	
	$table->createList($cols);
	$found = FALSE;
	$driver->firmware->reset();
//	$driver->firmware->setDontSelect('FirmwareCode, FirmwareData');
	$driver->firmware->addOrder('FWPartNum');
	$driver->firmware->addOrder('FirmwareVersion', TRUE);
	$driver->firmware->addWhere("HWPartNum='0039-21'");
	$driver->firmware->addWhere("FWPartNum<>'0039-20-06-C'");
	$ret = $driver->firmware->getAll();
	foreach($ret as $val) {
		if (($val['FirmwareVersion'] == $devInfo['FWVersion'])
			&& ($val['FWPartNum'] == $devInfo['FWPartNum']))
		{
			$val['load'] = "Running";
			$found = TRUE;
		} else {
			if ($devInfo['bootLoader']) {
				$val['load'] = '<a href="'.$_SERVER['PHP_SELF'].'?DeviceKey='.$DeviceKey.'&loadProg&FirmwareKey='.$val['FirmwareKey'].'&noFetchSetup">load</a>';
			} else {
				$val['load'] = '';
			}
		}
		$table->addListRow($val);
	}
	if ($devInfo['bootLoader']) {
		$pForm->addElement('static', NULL, NULL, '<span class="error">Bootloader Running: '.$devInfo['FWPartNum'].' v'.$devInfo['FWVersion'].'</span>');
	} else if (!$found) {
		$pForm->addElement('static', NULL, NULL, '<span class="error">Unknown Application Found: '.$devInfo['FWPartNum'].' v'.$devInfo['FWVersion'].'</span>');
	}

	$table->finishList($format);
	$pForm->addElement('static', NULL, NULL, $table->toHTML());
	if (isset($_REQUEST['runProg']) && $pForm->validate()) {
		$driver->RunApplication($devInfo);
		header("Location: ".$returnTo);		
	} else if (isset($_REQUEST['crashProg']) && $pForm->validate()) {
		$driver->RunBootloader($devInfo);
		header("Location: ".$returnTo);
	} else if (isset($_REQUEST['loadProg'])) {
		ob_end_clean();					 // End buffering and discard
		header("Refresh: 10,url=".$returnTo);
		$driver->loadProgram($devInfo, $devInfo, $_REQUEST['FirmwareKey']);
		print '<a href="'.$returnTo.'">back</a>';
		die();
	}
	print $pForm->toHTML();
	
	
	
	
	
	
//print get_stuff($devInfo);

?>