<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php'); 

  $response = Array();
  $response['success'] = false;
  
  $c = new Catalog();

  $artists = Array();
  $artists[] = new Artist($_REQUEST['artistid']);

  $album = new Album();
  $album->name = $_REQUEST['albumname'];
  $album->year = $_REQUEST['year'];
  $album->description = $_REQUEST['albumdescription'];
  $album->coverart_full = $_REQUEST['coverart_full'];
  $album->coverart_thumb = $_REQUEST['coverart_thumb'];
  
  $tracks = Array();
  foreach ($_REQUEST['track'] as $trackinfo) {
    $track = new Track();
    $track->name = $trackinfo['name'];
    $track->tracknumber = $trackinfo['tracknumber'];
    $track->length = $trackinfo['length'];
    $track->setPath($trackinfo['path']);
    
    $tracks[] = $track;
  }
  
  $response['success'] = $c->addCompleteAlbum($album, $artists, $tracks, $_REQUEST['tmpdir']);
  
  print json_encode($response);
?>
