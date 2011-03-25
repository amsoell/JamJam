<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  
  
  if (isset($_REQUEST['track']) && is_numeric($_REQUEST['track']) && isset($_REQUEST['tag']) && ($track = new Track($_REQUEST['track']))) {
    $tag = new Tag($_REQUEST['tag']);

    $response['success'] = ($track->addTag($tag)!==false);
    $response['tags'] = $track->tag;
  }
  
  echo json_encode($response);
?>