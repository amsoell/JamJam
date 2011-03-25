<?php
//------------------------------------------------------------------
/// \file   Request.class.php
/// \brief  Request object submitted by user
/// \author A. M. Soell
/// \date   4/23/2008
/// \version  3.0
///
/// This object is an element of the Queue containing information
/// about each requested track
///
// $Id:$
//-------------------------------------------------------------------


//
///	\brief	Request class
///
/// Holds information about each request in the queue
//
  class Request extends Common {
    public $id; // Unique identifier
    public $track; // Track
    public $dedication; // Dedication object
    public $user; // User object
    private $remoteaddr; // IP Address
    
    public function __construct($obj=null) {
      if (!is_null($obj)) {
        $this->load($obj);
      }
    }
    
    public function getTrackId() {
      return $this->track->getId();
    }
    
    public function getRemoteAddr() {
      return $this->remoteaddr;
    }
    
    private function load($obj) {
      global $_DB;
      
      if (is_object($obj) && (is_a($obj, "Track"))) {
        $this->track = $obj;
        if (array_key_exists('REMOTE_ADDR', $_SERVER)) $this->remoteaddr = $_SERVER['REMOTE_ADDR'];
      } elseif (is_numeric($obj)) {
        $rs = $_DB->query("SELECT id, track, user, remoteaddr FROM #__queue WHERE id='$obj'");
        
        if ($rec = $rs->fetch_array()) {
          $this->id = $rec['id'];
          $this->remoteaddr = $rec['remoteaddr'];
          $this->track = new Track($rec['track']);
        } else {
          return false;
        }
      } else {
        return false;
      }
    }
  }
  