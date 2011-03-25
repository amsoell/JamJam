<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  
  
  $c = new Catalog();
  
  if (isset($_REQUEST['query'])) {
    $results = $c->searchArtist($_REQUEST['query']);
  } else {
    $results = $c->getArtists();
  }
  
  print json_encode($results);
?>
