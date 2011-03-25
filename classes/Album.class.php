<?php
  class Album extends Common {
    public $id; // Integer
    public $name; // Text
    public $year; // Integer
    public $description; // Text
    public $coverart_thumb; // base64 encoded image
    public $coverart_full; // base64 encoded image
    
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
    
    public function getYear() {
      return $this->year;
    }

    public function clearCoverArtThumb() {
      $this->coverart_thumb = null;
    }
    
    public function clearCoverArtFull() {
      $this->coverart_full = null;
    }
    
    private function load($id) {
      global $_DB;
      $rs = $_DB->query("SELECT name, year, description, coverart_thumb, coverart_full FROM #__album WHERE id='".addslashes($id)."'");
      if ($rec = $rs->fetch_array()) {
        $this->id = $id;
        $this->name = $rec['name'];
        $this->year = $rec['year'];
        $this->description = $rec['description'];
        $this->coverart_thumb = $rec['coverart_thumb'];
        $this->coverart_full = $rec['coverart_full'];
      }
    }
    
    public function update($artists = Array()) {
      global $_DB;
      
      if (isset($this->id) && ($this->id > 0)) {
        $_DB->query("UPDATE #__album SET name='".addslashes(trim($this->getName()))."', year='".addslashes(trim($this->getYear()))."', description='".addslashes(trim($this->description))."', coverart_thumb='".addslashes($this->coverart_thumb)."', coverart_full='".addslashes($this->coverart_full)."' WHERE id='".addslashes($this->getId())."' LIMIT 1");
      } else {
        $this->id = $_DB->query("INSERT INTO #__album (name, year, description, coverart_thumb, coverart_full, creation, creation_user, creation_remoteaddr) VALUES ('".addslashes(trim($this->getName()))."', '".addslashes(trim($this->getYear()))."', '".addslashes(trim($this->description))."', '".addslashes($this->coverart_thumb)."', '".addslashes($this->coverart_full)."', NOW(), '".addslashes($_USER->id)."', '".$_SERVER['REMOTE_ADDR']."')");      
      }
      
      foreach ($artists as $artist) {
        if ($artist instanceof Artist) {
          $_DB->query("REPLACE INTO #__artist_album (artist, album) VALUES ('".addslashes($artist->getId())."', '".addslashes($this->getId())."')");
        }
      }
    }
  }
?>
