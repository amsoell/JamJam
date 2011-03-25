<?php
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  require('..'.DS.'include'.DS.'framework.php');  

  $ui = new UI();
  $q = new Queue();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  <head>
  <title>radio</title>
  <meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
  <link rel="apple-touch-icon" href="/favicon.iphone.png">
    <link rel="stylesheet" type="text/css" href="/css/iPhoneIntegration.css" />
  </head>
  <body>
<?php
  $nowplaying = $q->getNowPlaying();
  $upcoming = $q->getUpcoming();
  if ($nowplaying instanceof Request) {
?>
    <h1>Now Playing</h1>
    <ul>
      <li class="name" style="text-align: center">
        <embed target="myself" type="audio/mpeg" loop="true" src="/images/cover_full.php?id=<?php print $nowplaying->track->album->getId();?>" href="/i/play.php" autoplay="true" width="200" height="200"></embed>
      </li>    
      <li class="name">Track: <span class="secondary"><?php print $nowplaying->track->getName(); ?></span></li>
      <li class="artist">Artist: <span class="secondary"><?php print $nowplaying->track->artist[0]->getName(); ?></span></li>
    </ul>
<?php
  }
?>  
    <h1>Coming Up</h1>
    <ul>
<?php
  foreach ($upcoming as $request) {
?>
      <li class="request"><?php print $request->track->getName(); ?> <span class="secondary"><?php print $request->track->artist[0]->getName(); ?></li>
<?php
  }
?>
    </ul>
  </body>
</html>
