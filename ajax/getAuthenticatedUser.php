<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  
  
  print json_encode($_USER);
?>