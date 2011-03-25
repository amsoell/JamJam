<?php
  class Artist extends Common {
    public $id; // Integer
    public $name; // String
    public $sortname; // String
    private $creator; // User
    private $creation; // Date
    private $remoteaddr; // String
    
    
    public function __construct($id=null) {
      if (!is_null($id)) {
        $this->load($id);
      }    
    }
    
    public function getId() {
      return $this->id;
    }
    
    public function getName() { 
      return $this->name;
    }

    private function load($id) {
      global $_DB;
      $rs = $_DB->query("SELECT name, sortname, creator, creation, remoteaddr FROM #__artist WHERE id='".addslashes($id)."'");
      if ($rec = $rs->fetch_array()) {
        $this->id = $id;
        $this->name = $rec['name'];
        $this->sortname = $rec['sortname'];
        $this->creator = $rec['creator'];
        $this->creation = strtotime($rec['creation']);
        $this->remoteaddr = $rec['remoteaddr'];
      }
    }    
  }
?>
