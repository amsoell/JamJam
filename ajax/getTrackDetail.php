<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  
  
  $response['success'] = false;
  if (isset($_REQUEST['track']) && is_numeric($_REQUEST['track'])) {
    if ($t = new Track($_REQUEST['track'])) {
      $response['success'] = true;
      $response['track'] = $t;
      if (strlen($_REQUEST['expanded'])<=0) {
        unset($response['track']->coverart_full);
        unset($response['track']->filtermask);
        unset($response['track']->lastqueue);
      }
    }
  }
  
  print json_encode($response);
?>