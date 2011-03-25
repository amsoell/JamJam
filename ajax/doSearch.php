<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  
  
  
  $query = explode(';', $_REQUEST['q']);
  $query = $query[0];

  if (isset($query) && isset($_REQUEST['limit']) && is_numeric($_REQUEST['limit'])) {
    $c = new Catalog();
    $tracks = $c->search($query, $_REQUEST['limit']);

    foreach ($tracks as $track) {
      echo json_encode($track), "\n";
    }
  }
?>