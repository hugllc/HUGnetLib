<?php
/**
 * This is the control code for the 0039-12 endpoints with other daughter boards
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 * @version SVN: $Id$    
 *
 */

    include(dirname(__FILE__).'/../group.inc.php');
    $form = new HTML_QuickForm('relayControl');
    $form->addElement('hidden', 'DeviceKey');
    $form->addElement('hidden', 'noFetchSetup', true);
    for ($fet = 0; $fet < 2; $fet++) {
        $location = (isset($devInfo['Location'][$fet])) ? $devInfo['Location'][$fet] : "Relay ".$fet;
        $form->addElement('header', null, $location);
        $radio = array();
        $radio[] = $form->createElement('radio', 'RELAY'.$fet, null, 'Off', 0);
        $radio[] = $form->createElement('radio', 'RELAY'.$fet, null, 'On', 128);
        $form->addGroup($radio, null, "Power:");
    }
    $form->setConstants($devInfo);
    $form->addElement('submit', 'postSetup', 'Update');
    if (isset($_REQUEST['postSetup']) && $form->validate()) {
        $pktData = array();
        for ($i = 0; $i < 2; $i++) {
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