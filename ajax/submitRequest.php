<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  
  
  if (isset($_REQUEST['track']) && is_numeric($_REQUEST['track']) && ($track = new Track($_REQUEST['track']))) {
    $r = new Request($track);
    $q = new Queue();   
     
    $response['success'] = $q->enqueue($r, true);
  }
  
  $q = new Queue();
  $response['queue']['upcoming'] = $q->getUpcoming();
  $response['queue']['nowplaying'] = $q->getNowPlaying();
  echo json_encode($response);
?>