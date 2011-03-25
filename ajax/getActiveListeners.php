<?php
require_once('/srv/radio/configuration.php');
$config = new Config();
require_once($config->site_root.'/include/framework.php');
require_once('/srv/radio/root/include/getid3/getid3.php');

echo json_encode(User::getActiveListeners());
?>