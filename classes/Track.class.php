<?php
  class Track extends Common {
    public $id; // Integer
    public $name; // String
    public $album; // Album
    public $artist; // Array[Artist]
    public $tag; // Array[Tag]
    public $tracknumber; // Integer
    public $length;
    public $displayLength;
    public $filtermask;
    public $lastqueue;
    public $queueable;
    private $attributes;
    private $path; // String
    
    public function __construct($id=null) {         
      $this->attributes = Array();
      $this->artist = Array();
      $this->tag = Array();
      if (!is_null($id)) {
        $this->load($id);
      }
    }
    
    public function setQueueable($status) {
      global $_DB;
    
//      $_DB->query("UPDATE #__track SET filtermask=filtermask+IF(!filtermask&".FILTER_QUEUEABLE.",".FILTER_QUEUEABLE.",0) where id='".$this->id."'");
      $_DB->query("UPDATE #__track SET lastqueue=NOW() where id='".$this->id."'");
      
      return true;
    }
    
    public function increasePlayCount() {
      global $_DB;

      $_DB->query("UPDATE #__track SET playCount=playCount+1 WHERE id='".$this->id."'");
    }
    
    public function increaseQueueCount() {
      global $_DB;

      $_DB->query("UPDATE #__track SET queueCount=queueCount+1 WHERE id='".$this->id."'");
    }
    
    public function getId() {
      return $this->id;
    }
    
    public function getPath() {
      return $this->path;
    }
    
    public function getName() {
      return $this->name;
    }
    
    public function getTrackNumber() {
      return $this->tracknumber;
    }
    
    public function getAlbumId() {
      if ($this->album instanceof Album) {
        return $this->album->getId();
      } else {
        return false;
      }
    }
    
    public function getAlbumName() {
      return $this->album->getName();
    }
    
    public function getAlbumYear() {
      return $this->album->getYear();
    }
    
    public function getArtists() {
      return $this->artist;
    }
    
    public function getAttribute($attr) {
      if (array_key_exists($attr, $this->attributes)) {
        return $this->attributes[$attr];
      } else {
        return false;
      }
    }
    
    public function setPath($path) {
      $this->path = $path;
    }

    public function displayLength() {
      return intval($this->length/60).':'.(substr('0'.$this->length % 60, -2));
    }

    public function isQueueable() {
      return $this->filtermask&FILTER_QUEUEABLE;
    }
    
    public function addTag($tag) {
      global $_DB;
      global $_USER;
    
      if (! ($tag instanceof Tag)) {
        $tag = new Tag($tag);
      }
      $this->tag[] = $tag;
      
      $_DB->query("REPLACE INTO #__track_tag (track, tag, user, tagged, remoteaddr) VALUES ('".$this->id."', '".$tag->id."', '".$_USER->id."', NOW(), '".addslashes($_SERVER['REMOTE_ADDR'])."')");
    }

    public function removeTag($tag) {
      global $_DB;
      global $_USER;
    
      if (! ($tag instanceof Tag)) {
      } elseif (is_numeric($tag)) {
        $tag = new Tag($tag);
      } else {
        return false;
      }
      
      $_DB->query("DELETE FROM #__track_tag WHERE track='".$this->id."' AND tag='".$tag->id."' LIMIT 1");
    }
    
    public function getArray() {
      $out = Array();
      
      $out['id'] =  $this->getId();
      $out['name'] = $this->getName();
      $out['track'] = $this->getTrackNumber();
      $out['artists'] = Array();
      
      $artists = $this->getArtists();
      for ($j=0; $j<count($artists); $j++) {
        $artist = $artists[$j];
        $artist_obj['id'] = $artist->getId();
        $artist_obj['name'] = $artist->getName();
        
        $out['artists'][] = $artist_obj;
        unset($artist_obj);
      }
      
      $album_obj['id'] = $this->getAlbumId();
      $album_obj['name'] = $this->getAlbumName();
      $album_obj['year'] = $this->getAlbumYear();
      
      $out['album'] = $album_obj;
      
      return $out;
    }
    
    public function getJson() {
      return json_encode($this->getArray());
    }
    
    private function load($id) {
      global $_DB;
      
      $rs = $_DB->query("SELECT name, album, tracknumber, length, path, lastqueue, filtermask FROM #__track WHERE id='".addslashes($id)."'");
      if ($rec = $rs->fetch_array()) {
        $this->id = $id;
        $this->name = $rec['name'];
        $this->tracknumber = $rec['tracknumber'];
        $this->path = $rec['path'];
        $this->album = new Album($rec['album']);
        $this->length = $rec['length'];
        $this->displayLength = intval($this->length/60).':'.substr('0'.($this->length%60), -2);
        $this->filtermask = $rec['filtermask'];
        $this->lastqueue = $rec['lastqueue'];
        $this->queueable = !is_null($rec['lastqueue']);
      }
      
      $rs = $_DB->query("SELECT artist FROM #__artist_track WHERE track='".addslashes($id)."'");
      while ($rec = $rs->fetch_array()) {
        $artist = new Artist($rec['artist']);
        $this->artist[] = $artist;
      }
      
      $rs = $_DB->query("SELECT tag FROM #__track_tag WHERE track='".addslashes($id)."'");
      while ($rec = $rs->fetch_array()) {
        $tag = new Tag($rec['tag']);
        $this->tag[] = $tag;
      }
      
      $this->attributes['track_id'] = $this->id;
      if ($this->album instanceof Album) $this->attributes['album_id'] = $this->album->id;
      $this->attributes['artist_id'] = (is_array($this->artist)&&(count($this->artist)>0))?$this->artist[0]->id:null;
    }  
    
    public function update() {
      global $_DB;
      
      if (isset($this->id) && ($this->id > 0)) {
        $_DB->query("UPDATE #__track SET name='".addslashes(trim($this->getName()))."', album='".addslashes($this->getAlbumId())."', tracknumber='".addslashes($this->getTrackNumber())."', path='".addslashes(trim($this->getPath()))."', length='".addslashes($this->length)."', bitrate='".addslashes($this->bitrate)."' WHERE id='".addslashes($this->getId())."' LIMIT 1");
      } else {
        $this->id = $_DB->query("INSERT INTO #__track (name, album, tracknumber, path, length, bitrate) VALUES ('".addslashes(trim($this->getName()))."', '".addslashes($this->getAlbumId())."', '".addslashes($this->getTrackNumber())."', '".addslashes(trim($this->getPath()))."', '".addslashes($this->length)."', '".addslashes($this->bitrate)."')");
      }
      
      foreach ($this->artist as $artist) {
        $_DB->query("REPLACE INTO #__artist_track (artist, track) VALUES ('".addslashes($artist->getId())."', '".addslashes($this->getId())."')");
      }
    }
      
  }
?>
