<?php
  class Catalog extends Common {
    
    public function __construct() {
    }
    
    public function getRandomTrack($filters=Array()) {
      global $_DB;
      $config = new Config();
   
      $sql = 'SELECT FLOOR(RAND() * COUNT(*)) AS offset FROM #__track WHERE ';
      $sql .= 'lastqueue IS NOT NULL AND ';
      $sql .= '1=1';
 
      $rs = $_DB->query($sql);
      if ($rec = $rs->fetch_array()) {
      	$offset = $rec['offset'];
      } else {
        $offset = 0;
      }

      $sql = 'SELECT id FROM #__track WHERE ';
      $sql .= 'lastqueue IS NOT NULL AND ';
      $sql .= '1=1';      
      $sql .= ' LIMIT '.$offset.', 1';
      

      $rs = $_DB->query($sql);
      if ($rec = $rs->fetch_array()) {
        $trackid = $rec['id'];
      }
      $track = new Track($trackid);

      foreach ($filters as $filter) {
        if (! $filter->filter($track)) {
          unset($track);
          $track = $this->getRandomTrack($filters);
        }
      }
      
      return $track;
    }
    
    public function getArtists() {
      global $_DB;

      $rs = $_DB->query("SELECT id FROM #__artist ORDER BY sortname");
      $artists = Array();
      while ($rec = $rs->fetch_array()) {
        $artists[] = new Artist($rec['id']);
      }
      
      return $artists;
    }
    
    public function getAlbumsByArtist($artist, $getThumb=true, $getFull=true) {
      global $_DB;
      
      $rs = $_DB->query("SELECT #__album.id FROM #__artist_album INNER JOIN #__album ON #__artist_album.album=#__album.id WHERE #__artist_album.artist='$artist' ORDER BY #__album.year DESC");

      $albums = Array();
      while ($rec = $rs->fetch_array()) {
        $a = new Album($rec['id']);
        if (!$getThumb) $a->clearCoverArtThumb();
        if (!$getFull) $a->clearCoverArtFull();
        $albums[] = $a;
      }
      
      return $albums;
    }
    
    public function getTracksByAlbum($album, $getAlbumData=true) {
      global $_DB;
      
      $rs = $_DB->query("SELECT #__track.id FROM #__track WHERE #__track.album='$album' ORDER BY tracknumber");
      $tracks = Array();
      while ($rec = $rs->fetch_array()) { 
        $track = new Track($rec['id']);
        if (!$getAlbumData) $track->album = null;
        $tracks[] = $track;
      }
      
      return $tracks;
    }    
    
    public function getTags() {
      global $_DB;
      
      $rs = $_DB->query("SELECT #__tag.id, #__tag.name FROM #__tag ORDER BY #__tag.name");
      $tags = Array();
      while ($rec = $rs->fetch_array()) {
        $tag['id'] = $rec['id'];
        $tag['name'] = $rec['name'];
        $tags[] = $tag;
      }
      
      return $tags;
    }
    
    public function search($query, $limit) {
      global $_DB;
      
      $rs = $_DB->query("SELECT id FROM #__track WHERE name LIKE '%".addslashes($query)."%' UNION SELECT id FROM #__track WHERE MATCH (name) AGAINST ('".addslashes($query)."') LIMIT $limit");
      $tracks = Array();
      while ($rec = $rs->fetch_array()) {
        $tracks[] = new Track($rec['id']);
      }
      
      return $tracks;
    }
    
    public function searchArtist($query, $limit=1) {
      global $_DB;
      
      $rs = $_DB->query("SELECT id FROM #__artist WHERE MATCH (name) AGAINST ('".addslashes($query)."') LIMIT $limit");
      if ($rec = $rs->fetch_array()) {
        return (new Artist($rec['id']));
      } else {
        return false;
      }
    }
    
    public function addCompleteAlbum($album, $artists, $tracks, $tmpdir) {
      global $_DB;
      $config = new Config();
      
      $album->update($artists);
      foreach ($tracks as $track) {
        if (file_exists($config->site_root.DS.'administrator'.DS.'files'.DS.$tmpdir.DS.$track->getPath())) {
          if (!file_exists($config->track_root.DS.$artists[0]->name)) mkdir($config->track_root.DS.$artists[0]->name);
          if (!file_exists($config->track_root.DS.$artists[0]->name.DS.$album->getName())) mkdir($config->track_root.DS.$artists[0]->name.DS.$album->getName());
          rename($config->site_root.DS.'administrator'.DS.'files'.DS.$tmpdir.DS.$track->getPath(), $config->track_root.DS.$artists[0]->name.DS.$album->getName().DS.$track->getPath());
          $track->album = $album;
          $track->artist = $artists;
          $track->setPath(DS.$artists[0]->name.DS.$album->getName().DS.$track->getPath());
          $track->update();
        }
      }
    }
    
    private function addAlbum($album) {
      global $_DB;
      
      
    }
  }
?>
