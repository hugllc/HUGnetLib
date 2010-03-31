<?php
// Need to make sure this file is not added to the code coverage
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

$hugnet_config["hugnet_database"] = "MyDatabase";
$hugnet_config["script_gatewaykey"] = 2;
$hugnet_config["servers"][0]["driver"] = "mysql";
$hugnet_config["servers"][0]["host"] = "10.2.5.23";
$hugnet_config["servers"][0]["user"] = "user";
$hugnet_config["servers"][0]["password"] = 'password';
$hugnet_config["poll_enable"] = true;
$hugnet_config["config_enable"] = true;
$hugnet_config["control_enable"] = false;
$hugnet_config["check_enable"] = true;
$hugnet_config["check_send_daily"] = true;
$hugnet_config["analysis_enable"] = true;
$hugnet_config["admin_email"] = "you@yourdomain.com";
$hugnet_config["gatewayIP"] = "10.2.3.5";
$hugnet_config["gatewayPort"] = 2001;
?>