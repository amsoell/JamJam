<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="refresh" content="60" />
  </head>
  <body>
<?php
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  
  
  $q = new Queue();
  $nowplaying = $q->getNowPlaying();
  print $nowplaying->track->artist[0]->getName().' :: '.$nowplaying->track->getName()."<br>\n";
  
  print "<hr>";
  
  $upcoming = $q->getUpcoming();
  foreach ($upcoming as $request) {
    print $request->track->artist[0]->getName().' :: '.$request->track->getName()."<br>\n";
  }
?>
  </body>
</html>