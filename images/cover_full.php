<?php
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  require('..'.DS.'include'.DS.'framework.php');  

  $album = new Album($_REQUEST['id']);
  if ($album instanceof Album) {
    header('Content-Type: image/jpeg');  
    print base64_decode($album->coverart_full);
  }
?>