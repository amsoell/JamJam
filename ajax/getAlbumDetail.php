<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  
  
  if (isset($_REQUEST['album']) && is_numeric($_REQUEST['album'])) {
    $album['detail'] = new Album($_REQUEST['album']);
    $c = new Catalog();
    $album['tracks'] = $c->getTracksByAlbum($_REQUEST['album'], false);
    echo json_encode($album);
  }
?>
