<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  
  
  if (isset($_REQUEST['request']) && is_numeric($_REQUEST['request'])) {
    $r = new Request($_REQUEST['request']);

    echo json_encode($r);
  }
?>
