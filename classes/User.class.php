<?php
//------------------------------------------------------------------
/// \file   User.class.php
/// \brief  User object
/// \author A. M. Soell
/// \date   5/6/2008
/// \version  3.0
///
/// This object manages all user authentication and preferences
///
// $Id:$
//-------------------------------------------------------------------


//
///	\brief	User class
///
/// This object manages all user authentication and preferences
//
  class User extends Common {
    public $id;
    public $email;
    public $name;
    public $password;
    public $remoteaddr;
    public $lastseen;
    public $streamagent;
    public $underruns;
    public $connecttime;
    public $streamuid;
    private $forcechange;
    private $creationdate;
    private $creationremoteaddr;
    public $authenticated;
    private $loaded;
    public $logged;
  
    public function __construct($obj=null) {
      $this->authenticated = false;
      $this->loaded = false;
      $this->logged = false;
    
      if (is_numeric($obj)) {
        return $this->load($obj);
      } elseif (is_array($obj) && array_key_exists('email', $obj) && array_key_exists('name', $obj) && array_key_exists('password', $obj)) {
        return $this->create($obj);
      } else {
        return true;
      }
    }
    
    public function getActiveListeners() {
      $config = new Config();
      $activeListeners = Array();
    
      $ch = curl_init();
      $null = fopen('/dev/null', 'w');
      curl_setopt($ch, CURLOPT_URL, 'http://'.$config->server_stream_hostname.':'.$config->server_stream_port.'/admin.cgi?pass='.urlencode($config->server_stream_password).'&mode=viewxml&page=3');
      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0)");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $xml_response = curl_exec($ch);
      curl_close($ch);
      fclose($null);
      
      if ($xml = new SimpleXMLElement($xml_response)) {
        foreach ($xml->LISTENERS->LISTENER as $listener) {
          if ($u = User::getUserByFingerprint(strval($listener->HOSTNAME))) {
            $u->setStreamAgent(strval($listener->USERAGENT));
            $u->setUnderruns(intval($listener->UNDERRUNS));
            $u->setConnectTime(intval($listener->CONNECTTIME));
            $u->setStreamUid(intval($listener->UID));
            $activeListeners[] = $u;
          }
        }
      }
      
      return $activeListeners;
    }
    
    public function getUserByFingerprint($ip, $useragent=null) {
      global $_DB;
      
      $rs = $_DB->query("SELECT id FROM #__user WHERE remoteaddr='".addslashes($ip)."' LIMIT 1");
      if ($rec = $rs->fetch_array()) {
        $u = new User($rec['id']);
        return $u;
      } else {
        return false;
      }
    }
    
    public function isAuthenticated() {
      return $this->authenticated;
    }
    
    public function forcePasswordChange($status=true) {
      global $_DB;
      
      return $_DB->query("UPDATE #__user SET forcechange='".($status?'1':'0')."' WHERE id='".$this->id."' LIMIT 1");
    }
    
    public function updateLastSeen() {
      $this->lastseen = now();
      $this->update();
    }
    
    public function updatePassword($newpassword, $oldpassword=null) {
      global $_DB;
      
      if (is_null($oldpassword) || (sha1(trim($oldpassword)) == $this->password)) {
        return $_DB->query("UPDATE #__user SET password='".addslashes(sha1(trim($newpassword)))."' WHERE id='".$this->id."' LIMIT 1");
      } else {
        return false;
      }
    }
    
    private function update() {
      global $_DB;
      
      if (is_numeric($this->id)) {
        return $_DB->query("UPDATE #__user SET email='".addslashes($this->email)."', ".
                                               "name='".addslashes($this->name)."', ".
                                         "remoteaddr='".addslashes($this->remoteaddr)."', ".
                                           "lastseen='".addslashes($this->lastseen)."', ".
                                        "forcechange='".addslashes($this->forcechange)."' ".
                           "WHERE id='".$this->id."' LIMIT 1");
      } else {
        return false;
      }
    }
    
    public function authenticate($username, $password, $sighting_tokens=Array()) {
      global $_DB;
      
      $this->authenticated = false;
      
      $rs = $_DB->query("SELECT id, password FROM #__user WHERE email='".$username."' LIMIT 1");
      if ($rec = $rs->fetch_array()) {
        if (sha1(trim($password)) == $rec['password']) {
          $_DB->query("UPDATE #__user SET lastseen=NOW(), remoteaddr='".$_SERVER['REMOTE_ADDR']."' WHERE id='".$rec['id']."' LIMIT 1");
          $this->authenticated = true;
          $this->load($rec['id']);
          $this->logSighting(array_merge(Array(
              'web_remoteaddr' => $_SERVER['REMOTE_ADDR'],
              'web_useragent'  => $_SERVER['HTTP_USER_AGENT']
            ), $sighting_tokens));
          
          return true;
        } else {  
          $this->setErrorCode(ERR_USER_INVALID_PASSWORD);
          return false;
        }
      } else {
        $this->setErrorCode(ERR_USER_NOT_FOUND);
        return false;
      }
    }
    
    public function setStreamAgent($streamagent) {
      $this->streamagent = $streamagent;
    }
    
    public function setUnderruns($underruns) {
      $this->underruns = $underruns;
    }
    
    public function setConnectTime($connecttime) {
      $this->connecttime = $connecttime;
    }
    
    public function setStreamUid($uid) {
      $this->streamuid = $uid;
    }
    
    public function deauthenticate() {
      $this->id = null;
      $this->email = null;
      $this->name = null;
      $this->password = null;
      $this->remoteaddr = null;
      $this->lastseen = null;
      $this->forcechange = null;
      $this->creationdate = null;
      $this->creationremoteaddr = null;
      $this->authenticated = null;
      $this->loaded = null;
      $this->__construct();
    }
    
    public function logSighting($properties) {
      global $_DB;
      
      $columns = "";
      $values  = "";
      $set = "";
      $where   = "";
      
      foreach ($properties as $property_key => $property_value) {
        $columns .= ", ".$property_key;
        $values  .= ", '".addslashes(trim($property_value))."'";  
        $set     .= ", ".$property_key."='".addslashes(trim($property_value))."'";
        $where   .= " @OP@ (".$property_key."='".addslashes(trim($property_value))."' OR ".$property_key." IS NULL)";
      }
      
      $rs = $_DB->query("SELECT id FROM #__sighting WHERE user='".$this->id."' AND (1=1".str_replace('@OP@', 'AND', $where).") ORDER BY seen DESC limit 1");
      if ($rec = $rs->fetch_array()) {
        if ($_DB->query("UPDATE #__sighting SET seen=NOW()".$set." WHERE id='".$rec['id']."' LIMIT 1")) {
          $this->logged = true;
        }
      } else {
        if ($_DB->query("INSERT INTO #__sighting (user, seen".$columns.") VALUES ('".$this->id."', NOW()".$values.")")) {
          $this->logged = true;
        }
      }
    }
    
    private function load($id) {
      global $_DB;
      
      $this->loaded = false;
      if (is_numeric($id)) {
        $rs = $_DB->query("SELECT id, email, name, password, remoteaddr, forcechange, lastseen, creationdate, creationremoteaddr FROM #__user WHERE id='$id' LIMIT 1");
        
        if ($rec = $rs->fetch_array()) {
          $this->id = $rec['id'];
          $this->email = $rec['email'];
          $this->name = $rec['name'];
          $this->password = $rec['password'];
          $this->remoteaddr = $rec['remoteaddr'];
          $this->lastseen = strtotime($rec['lastseen']);
          $this->forcechange = $rec['forcechange'];
          $this->creationdate = strtotime($rec['creationdate']);
          $this->creationremoteaddr = $rec['creationremoteaddr'];
          
          $this->loaded = true;
          
          return true;
        } else {
          return false;
        }
      }
    }
    
    private function create($userdetail) {
      global $_DB;
      
      if (array_key_exists('email', $userdetail) && array_key_exists('name', $userdetail) && array_key_exists('password', $userdetail)) {
        if ($id = $_DB->query("INSERT INTO #__user (email, name, password, remoteaddr, lastseen, creationdate, creationremoteaddr) VALUES ('".addslashes($userdetail['email'])."','".addslashes($userdetail['name'])."','".addslashes(sha1(trim($userdetail['password'])))."','".addslashes(array_key_exists('remoteaddr', $userdetail)?$userdetail['remoteaddr']:(array_key_exists('remoteaddr', $_SERVER)?$_SERVER['remoteaddr']:''))."',NOW(),NOW(),'".addslashes(array_key_exists('remoteaddr', $userdetail)?$userdetail['remoteaddr']:(array_key_exists('remoteaddr', $_SERVER)?$_SERVER['remoteaddr']:''))."')", true)) {
          return $this->load($id);
        } else {
          return false;
        }
      } else {
        return false;
      }
    }
  }
?>
