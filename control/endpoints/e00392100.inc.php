<?php
/**
 * This is the control code for the 0039-21 controller boards.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007-2009 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Drivers
 * @package    HUGnetLib
 * @subpackage Endpoints
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @version SVN: $Id$    
 *
 */

require dirname(__FILE__).'/../group.inc.php';
require_once 'lib/tables.inc.php';

$driver = &$endpoint->drivers[$devInfo['Driver']];
$returnTo = $_SERVER['PHP_SELF']."?DeviceKey=".$devInfo['DeviceKey'];

if ($devInfo['FWPartNum'] == '0039-20-01-C') {
    $form = new HTML_QuickForm('fetControl');
    $form->addElement('hidden', 'DeviceKey', $DeviceKey);
//        $form->addElement('hidden', 'noFetchSetup', true);
    $form->addElement('header', null, '0039-20-01-C HUGnet Controller Options');
    $form->addElement('header', null, 'HUGnet Power');
    for ($i = 0; $i < 2; $i++) {
        $radio = array();
        $radio[] = $form->createElement('radio', 'HUGnetPower['.$i.']', null, 'Off', 0);
        $radio[] = $form->createElement('radio', 'HUGnetPower['.$i.']', null, 'On', 1);
        $form->addGroup($radio, null, "Output ".($i+1));
    }
    $form->addRule('TimeConstant', 'Time Constant can not be empty', 'required', null, 'client');    
    $form->addRule('TimeConstant', 'Time Constant must be numeric', 'numeric', null, 'client');    
    $form->setDefaults($devInfo);
    $form->addElement('submit', 'postPower', 'Update');
    if (isset($_REQUEST['postPower']) && $form->validate()) {
        $power = $driver->setPower($devInfo, $_REQUEST['HUGnetPower'][0], $_REQUEST['HUGnetPower'][1]);
        if ($power !== false) header("Location: ".$returnTo);

    }
    print $form->toHTML();
}
$pForm = new HTML_QuickForm('ProgControl');
$pForm->addElement('hidden', 'DeviceKey', $DeviceKey);
$pForm->addElement('hidden', 'noFetchSetup', true);
$pForm->addElement('header', null, 'Program Control');
if ($devInfo['bootLoader']) {
    $crc = $driver->GetApplicationCRC($devInfo);
    if ($crc == $devInfo['CRC']) {
        $pForm->addElement('submit', 'runProg', 'Run Application');
    } else {
        $pForm->addElement('static', null, null, '<span class="error">Bad Application ('.$crc.' != '.$devInfo['CRC'].').  Please reload</span>');
    }
} else {
    $pForm->addElement('submit', 'crashProg', 'Crash Application');    
}

$cols   = array(
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
$found = false;
$driver->firmware->reset();
//    $driver->firmware->setDontSelect('FirmwareCode, FirmwareData');
$driver->firmware->addOrder('FWPartNum');
$driver->firmware->addOrder('FirmwareVersion', true);
$driver->firmware->addWhere("HWPartNum='0039-21'");
$driver->firmware->addWhere("FWPartNum<>'0039-20-06-C'");
$ret = $driver->firmware->getAll();
foreach ($ret as $val) {
    if (($val['FirmwareVersion'] == $devInfo['FWVersion'])
        && ($val['FWPartNum'] == $devInfo['FWPartNum']))
    {
        $val['load'] = "Running";
        $found = true;
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
    $pForm->addElement('static', null, null, '<span class="error">Bootloader Running: '.$devInfo['FWPartNum'].' v'.$devInfo['FWVersion'].'</span>');
} else if (!$found) {
    $pForm->addElement('static', null, null, '<span class="error">Unknown Application Found: '.$devInfo['FWPartNum'].' v'.$devInfo['FWVersion'].'</span>');
}

$table->finishList($format);
$pForm->addElement('static', null, null, $table->toHTML());
if (isset($_REQUEST['runProg']) && $pForm->validate()) {
    $driver->RunApplication($devInfo);
    header("Location: ".$returnTo);        
} else if (isset($_REQUEST['crashProg']) && $pForm->validate()) {
    $driver->RunBootloader($devInfo);
    header("Location: ".$returnTo);
} else if (isset($_REQUEST['loadProg'])) {
    ob_end_clean();                     // End buffering and discard
    header("Refresh: 10,url=".$returnTo);
    $driver->loadProgram($devInfo, $devInfo, $_REQUEST['FirmwareKey']);
    print '<a href="'.$returnTo.'">back</a>';
    die();
}
print $pForm->toHTML();

?>