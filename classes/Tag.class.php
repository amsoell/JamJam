<?php
//------------------------------------------------------------------
/// \file   Tag.class.php
/// \brief  Track tag class
/// \author A. M. Soell
/// \date   5/13/2008
/// \version  3.0
///
/// This object manages tags applies to tracks
///
// $Id:$
//-------------------------------------------------------------------


//
///	\brief	Tag class
///
/// Manages all aspects of the tag attributes
//
  class Tag extends Common {
    public $id;
    public $name;

//
/// \brief  Constructor method
///
/// Initialize the queue object
///
//      
    public function __construct($val=null) {
      if (is_numeric($val)) {
        $this->load($val);
      } elseif (!is_null($val)) {
        $this->create($val);
      }
    }
    
    public function getName() {
      return $this->name;
    }
    
    private function create($tag) {
      global $_DB;
      global $_USER;
      
      if ($id = $_DB->query("INSERT INTO #__tag (name, user, remoteaddr) VALUES ('".addslashes($tag)."', '".$_USER->id."', '".addslashes($_SERVER['REMOTE_ADDR'])."')")) {
        return $this->load($id);
      } else {
        return false;
      }
    }

    private function load($id) {
      global $_DB;
      
      if (is_numeric($id)) {
        $rs = $_DB->query("SELECT id, name FROM #__tag WHERE id='$id' LIMIT 1");
        if ($rec = $rs->fetch_array()) {
          $this->id = $rec['id'];
          $this->name = $rec['name'];
        } else {
          return false;
        }
      } else {
        return false;
      }
    }
  }
?>
