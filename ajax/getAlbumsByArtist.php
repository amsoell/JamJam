<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  
  
  if (isset($_REQUEST['artist']) && is_numeric($_REQUEST['artist'])) {
    $c = new Catalog();
    echo json_encode($c->getAlbumsByArtist($_REQUEST['artist'], true, false));
  }
?>
