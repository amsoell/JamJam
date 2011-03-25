<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  
  
  if (isset($_REQUEST['track']) && is_numeric($_REQUEST['track'])) {
    $t = new Track($_REQUEST['track']);
    echo json_encode($t->tag);
  }
?>