<?php
//------------------------------------------------------------------
/// \file   Queue.class.php
/// \brief  Music queue object
/// \author A. M. Soell
/// \date   4/21/2008
/// \version  3.0
///
/// This object manages the music queue and all interactions with
/// the queue, including interfacing with the Shoutcast processes.
///
// $Id:$
//-------------------------------------------------------------------


//
///	\brief	Queue class
///
/// Manages all aspects of the music queue
//
  class Queue extends Common {
    private $queue; // Array

//
/// \brief  Constructor method
///
/// Initialize the queue object
///
//      
    public function __construct() {
    }
    
//
/// \brief  Enqueue a Track item
///
/// Adds the referenced Track to the play queue
///
/// \param[in]  request        Request object to add to queue
//
    public function enqueue($request, $setQueueable=true, $filters=Array()) {
      global $_DB;

      if (! $this->inQueue($request)) {
        $request->track->increaseQueueCount();    
        $_DB->query("INSERT INTO #__queue (track, queued, remoteaddr) VALUES ('".$request->track->id."', NOW(), '".$request->getRemoteAddr()."')");
        if ($setQueueable) $request->track->setQueueable(true);
        
        return true;
      } else {
        $this->setErrorCode(ERR_IN_QUEUE);
        return false;
      }
    }
    
    public function dequeue($request) {
      global $_DB;
      
      if ($this->inQueue($request)) {
        $_DB->query("DELETE FROM #__queue WHERE #__queue.id='".$request->id."' AND status='".Q_STATUS_INQUEUE."' LIMIT 1");
        return true;
      } else {
        return false;
      }
    }
    
    public function inQueue($request) {
      global $_DB;
      
      $rs = $_DB->query("SELECT id FROM #__queue WHERE status='".Q_STATUS_INQUEUE."' AND track='".$request->getTrackId()."'");
      return ($rs->num_rows()>0)?true:false;
    }

    public function flush() {
      global $_DB;
      $_DB->query("UPDATE #__queue SET status='".Q_STATUS_EXPIRED."' WHERE status<>'".Q_STATUS_EXPIRED."'");
    }
    
//
/// \brief  Write the shoutcast queue file
///
/// Writes the current queue state to the shoutcast queue text file
///
//    
    public function writeQueue() {
      global $_DB;
      $config = new Config();
    
      $fh = fopen($config->queue, 'w');
      $rs = $_DB->query("SELECT #__track.path FROM #__queue INNER JOIN #__track ON #__queue.track=#__track.id WHERE #__queue.status='".Q_STATUS_INQUEUE."' ORDER BY #__queue.queued, #__queue.id");
      while ($rec = $rs->fetch_array()) {
        fwrite($fh, $config->track_root.$rec['path']."\n");
      }
      fclose($fh);    
      unset($config);
    }

//
/// \brief  Update the queue
///
/// Updates the queue database to not include tracks that have played and adds random tracks to fill all empty slots
///
//  
    public function updateQueue() {
      global $_DB;
    
    
      $config = new Config();
      $logtail = shell_exec("tail -n 20 ".$config->log_trans);
      $logtail = explode("\n", $logtail);
      $logtail = array_reverse($logtail);
   
      $i=0;
      do { } while ((++$i<(count($logtail)-1)) && (!strpos($logtail[$i], SC_CURRENT_TRACK_DELIMITER)>0));
      $current_path = substr(strrchr($logtail[$i], SC_CURRENT_TRACK_DELIMITER), strlen(SC_CURRENT_TRACK_DELIMITER));
      $rs = $_DB->query("select #__queue.id, #__queue.track ".
                  "from #__queue ".
                  "inner join #__track on #__queue.track=#__track.id ".
                  "where #__queue.status='".Q_STATUS_INQUEUE."' AND ".
                  "path LIKE '%".addslashes($current_path)."'");
      if ($rec = $rs->fetch_array()) {
        // NEW TRACK IS PLAYING
        $current_track = new Track($rec['track']);

        // Notify stream of new track
        $ch = curl_init();
        $null = fopen('/dev/null', 'w');
        curl_setopt($ch, CURLOPT_URL, 'http://'.$config->server_stream_hostname.':'.$config->server_stream_port.'/admin.cgi?pass='.urlencode($config->server_stream_password).'&mode=updinfo&song='.rawurlencode($current_track->name.' - '.$current_track->artist[0]->name));
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0)");
        curl_setopt($ch, CURLOPT_FILE, $null);
        curl_exec($ch);
        curl_close($ch);
        fclose($null);
      
        $current_track->increasePlayCount();
        $_DB->query("UPDATE #__queue SET status='".Q_STATUS_NOWPLAYING."' WHERE status='".Q_STATUS_INQUEUE."' AND id='".$rec['id']."'");
        $_DB->query("UPDATE #__queue SET status='".Q_STATUS_EXPIRED."' WHERE status IN ('".Q_STATUS_INQUEUE."', '".Q_STATUS_NOWPLAYING."') AND id<'".$rec['id']."'");

      }
      
      unset($config);
    }
    
    public function reorder($orig, $dest) {
      for ($i=$orig; 
           (($orig>$dest) && ($i>$dest)) || (($orig<$dest) && ($i<=$dest)); 
           (($orig>$dest) && $i--) || (($orig<$dest) && $i++)) {            

        $r = new Request($i);
        $current_r[] = $r;
      }
      
      $current_r[] = array_shift($current_r);
      
      for ($i=$orig; 
           (($orig>$dest) && ($i>$dest)) || (($orig<$dest) && ($i<=$dest)); 
           (($orig>$dest) && $i--) || (($orig<$dest) && $i++)) {            
        $this->replaceRequest($i, array_shift($current_r));
      }      
    }
    
    public function replaceRequest($request_id, $request) {
      global $_DB;
    
      $_DB->query("UPDATE #__queue SET track='".$request->track->id."' WHERE id='".$request_id."' AND status='".Q_STATUS_INQUEUE."'");
    }
    
//
/// \brief  Get the size of the queue
///
/// Gets the total number of tracks in the queue
///
/// \return								integer value of queue size
//      
    public function getSize() {
      global $_DB;
          
      $rs = $_DB->query("SELECT COUNT(*) AS queue_size FROM #__queue WHERE STATUS='".Q_STATUS_INQUEUE."'");
      if ($rec = $rs->fetch_array()) {
        return $rec['queue_size'];
      } else {
        return 0;
      }
    }
    
//
/// \brief  Reload the shoutcast queue
///
/// Sends the USR1 signal to the shoutcast process to reload the queue
///
//      
    public function updateServer() {
      $ret = @shell_exec('killall -USR1 '.SC_DSP_EXE);
    }    
    
    public function getUpcoming() {
      global $_DB;
      $_upcoming = Array();
      
      $rs = $_DB->query("SELECT id FROM #__queue WHERE STATUS='".Q_STATUS_INQUEUE."' ORDER BY queued, id");
      while ($rec = $rs->fetch_array()) {
        $_upcoming[] = new Request($rec['id']);
      }
      
      return $_upcoming;
    }
    
    public function getNowPlaying() {
      global $_DB;
      
      $rs = $_DB->query("SELECT id FROM #__queue WHERE STATUS='".Q_STATUS_NOWPLAYING."' ORDER BY queued, id LIMIT 1");
      if ($rec = $rs->fetch_array()) {
        $_nowplaying = new Request($rec['id']);
      }
      
      return $_nowplaying;

    }
  }
?>
