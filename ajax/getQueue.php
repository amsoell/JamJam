<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  
  
  $q = new Queue();
  $response['nowplaying'] = $q->getNowPlaying();
  $response['upcoming'] = $q->getUpcoming();
  
  if (strlen($_REQUEST['expanded'])<=0) {  
    unset($response['nowplaying']->track->album->coverart_full);
    unset($response['nowplaying']->track->album->description);  
    for ($i=0; $i<count($response['upcoming']); $i++) {
      unset($response['upcoming'][$i]->track->album->coverart_full);
      unset($response['upcoming'][$i]->track->album->description);
    }
  }  
  echo json_encode($response);
?>