<?php
  $config = new Config();
  
  require($config->site_root.'/include/defines.php');
  require_once($config->site_root.'/classes/Common.class.php');  
  require_once($config->site_root.'/classes/Database.class.php');
  require_once($config->site_root.'/classes/Artist.class.php');
  require_once($config->site_root.'/classes/Album.class.php');
  require_once($config->site_root.'/classes/Track.class.php'); 
  require_once($config->site_root.'/classes/Catalog.class.php');   
  require_once($config->site_root.'/classes/Queue.class.php');    
  require_once($config->site_root.'/classes/Request.class.php');      
  require_once($config->site_root.'/classes/User.class.php');    
  require_once($config->site_root.'/classes/Filter.class.php');     
  require_once($config->site_root.'/classes/Tag.class.php');   
  require_once($config->site_root.'/classes/UI.class.php');     
  session_start();
  
  define('ACTION', array_key_exists('action', $_REQUEST)?strtolower(trim($_REQUEST['action'])):'');
  $_DB = new Database($config->db_server, $config->db_user, $config->db_password, $config->db_database);
  if (!array_key_exists('user', $_SESSION)) {
    $_SESSION['user'] = new User();
  }
  $_USER = &$_SESSION['user'];  
  
  switch (ACTION) {
    case "logout":
      $_USER->deauthenticate();
      break;
  }
  
  function debug($msg) {
    $config = new Config();
    if ($config->debug) echo $msg."<br>";
  }
?>