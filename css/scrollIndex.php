<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  

  $rs = $_DB->query('SELECT COUNT(id) AS totalartists FROM #__artist');
  if ($rec = $rs->fetch_array()) {
    $totalartists = $rec['totalartists'];
    
    $rs = $_DB->query("SELECT DISTINCT UPPER(SUBSTR(sortname, 1, 1)) AS firstletter, COUNT(UPPER(SUBSTR(sortname, 1, 1))) AS instances FROM #__artist WHERE UPPER(SUBSTR(sortname, 1, 1)) REGEXP '^[A-Z]' GROUP BY UPPER(SUBSTR(sortname, 1, 1)) ORDER BY firstletter");
    $offset = 0;    
    while ($rec = $rs->fetch_array()) {
      $height = $rec['instances']/$totalartists*100;
      if ($height<2) {
        $offset += (2 - $height);
        $height = 2;
      } elseif ($offset>0) {
        $height -= $offset;
        $offset = 0;
      } 
      
      print ".scrollLink#".strtolower($rec['firstletter'])." {\n";
      print "  height: ".($height)."%\n";
      print "}\n\n";
    }
  }
?>
