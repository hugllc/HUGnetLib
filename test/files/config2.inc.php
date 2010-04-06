<?php
// Need to make sure this file is not added to the code coverage
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

$config["hugnet_database"] = "MyDatabase";
$config["script_gatewaykey"] = 2;
$config["servers"][0]["driver"] = "mysql";
$config["servers"][0]["host"] = "10.2.5.23";
$config["servers"][0]["user"] = "user";
$config["servers"][0]["password"] = 'password';
$config["sockets"][0]["GatewayIP"] = "10.2.3.5";
$config["sockets"][0]["GatewayPort"] = 2001;
$config["poll_enable"] = true;
$config["config_enable"] = true;
$config["control_enable"] = false;
$config["check_enable"] = true;
$config["check_send_daily"] = true;
$config["analysis_enable"] = true;
$config["admin_email"] = "you@yourdomain.com";
?>