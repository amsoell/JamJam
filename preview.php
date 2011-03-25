<?php
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'configuration.php');
  require('include'.DS.'framework.php');  

  if (isset($_REQUEST['track']) &&  is_numeric($_REQUEST['track'])) {
    if ($t = new Track($_REQUEST['track'])) {
      $config = new Config();
      if (file_exists($config->track_root.$t->getPath())) {
        $mp3 = file_get_contents($config->track_root.$t->getPath(), FILE_BINARY);
        header('Content-Type: application/octet-stream');
        header('Content-Length: '.filesize($config->track_root.$t->getPath()));
        header('Content-Disposition: attachment; filename="preview.mp3"');        
        print $mp3;
      }
    }
  }
