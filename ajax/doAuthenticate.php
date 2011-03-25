<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  
  
  $response = Array();
  $response['success'] = false;

  if (isset($_REQUEST['username']) && isset($_REQUEST['password'])) {
    if ($_USER->authenticate($_REQUEST['username'], $_REQUEST['password'], Array( 
        'web_version' => $_REQUEST['web_version'],
        'web_browserclass' => $_REQUEST['web_browserclass']
      ))) {
      $response['success'] = true;
    }
  }
  print json_encode($response);
?>