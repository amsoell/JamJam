<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php');  

  $q = new Queue();

  switch (ACTION) {
    case 'enqueue':
      if (isset($_REQUEST['track']) && is_numeric($_REQUEST['track']) && ($track = new Track($_REQUEST['track']))) {
        $r = new Request($track);
        $q = new Queue();   
         
        $response['success'] = $q->enqueue($r, true);
      }
    
      break;
    case 'dequeue':
      if (isset($_REQUEST['request']) && is_numeric($_REQUEST['request']) && ($r = new Request($_REQUEST['request']))) {
        $response['success'] = $q->dequeue($r);
      }

      break;
    case 'move':
      if (isset($_REQUEST['orig']) && isset($_REQUEST['dest']) && is_numeric($_REQUEST['orig']) && is_numeric($_REQUEST['dest'])) {
        $allowed = true;
        $current_r = Array();
        for ($i=$_REQUEST['orig']; (($_REQUEST['orig']>$_REQUEST['dest']) && ($i>=$_REQUEST['dest'])) || (($_REQUEST['orig']<$_REQUEST['dest']) && ($i<=$_REQUEST['dest'])); (($_REQUEST['orig']>$_REQUEST['dest']) && $i--) || (($_REQUEST['orig']<$_REQUEST['dest']) && $i++)) {        
          $r = new Request($i);
          $current_r[] = $r;
          if (1<>1) $allowed = false;
        }
        
        if ($allowed) $q->reorder($_REQUEST['orig'], $_REQUEST['dest']);
      }
      break;
  }

  $response['queue']['upcoming'] = $q->getUpcoming();
  $response['queue']['nowplaying'] = $q->getNowPlaying();
  echo json_encode($response);
?>