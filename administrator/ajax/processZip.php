<?php
  header("Cache-Control: no-cache");
  
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'..'.DS.'configuration.php');
  $config = new Config();
  require($config->site_root.DS.'include'.DS.'framework.php'); 
  require($config->site_root.DS.'classes'.DS.'dUnzip2.class.php');   
  require_once($config->site_root.DS.'include/getid3/getid3.php');  

  $response = Array();
  $response['success'] = false;
  if ((strlen($_REQUEST['albumarchive'])>0) && file_exists($config->site_root.DS.'administrator'.DS.'files'.DS.$_REQUEST['albumarchive'])) {
    $z = new dUnzip2($config->site_root.DS.'administrator'.DS.'files'.DS.$_REQUEST['albumarchive']);
    $tmpfolder = time();
    if (mkdir($config->site_root.DS.'administrator'.DS.'files'.DS.$tmpfolder)) {
      $archive_files = $z->getList();
      if ($z->unzipAll($config->site_root.DS.'administrator'.DS.'files'.DS.$tmpfolder, '', false)) {
        $response['tmpfolder'] = $tmpfolder;
        $response['success'] = true;
        foreach ($archive_files as $file) {
          if (($file['uncompressed_size']>0) && (strtolower(substr($file['file_name'], strrpos($file['file_name'], '.')))=='.mp3')) {
            $f = Array();
            $f['path'] = substr($file['file_name'], strrpos($file['file_name'], DS)+1);

            // analyze the file
            $getID3 = new getID3;
            $id3 = $getID3->analyze($config->site_root.DS.'administrator'.DS.'files'.DS.$tmpfolder.DS.$f['path']);
            if (array_key_exists('tags', $id3)) {
              $f['artist']            = trim($id3['tags']['id3v2']['artist'][0]);
              $f['album']             = trim($id3['tags']['id3v2']['album'][0]);
              $f['name']             = trim($id3['tags']['id3v2']['title'][0]);
              $f['track_number']      = trim($id3['tags']['id3v2']['track_number'][0]);
              $f['year']              = trim($id3['tags']['id3v2']['year'][0]);
              $f['bitrate']           = intval($id3['audio']['bitrate']/1000);
              $f['playtime_seconds']  = intval($id3['playtime_seconds']);
              $f['comments']          = trim($id3['tags']['id3v2']['comments'][0]);
  
              
              // extract coverart
              $cover = null;
              if (isset($getID3->info['id3v2']['APIC'][0]['data'])) {
                $cover = $getID3->info['id3v2']['APIC'][0]['data'];
              } elseif (isset($getID3->info['id3v2']['PIC'][0]['data'])) {
                $cover = $getID3->info['id3v2']['PIC'][0]['data'];
              } else {
                $cover = null;
              }
              
              if (! is_null($cover)) {
                $original= imagecreatefromstring($cover);
                $original_w = imagesx($original);
                $original_h = imagesy($original);
          
                $full_h = 200;
                $full_w = $original_w / $original_h * $full_h;
                $full = imagecreatetruecolor($full_w, $full_h);
                imagecopyresampled($full, $original, 0, 0, 0, 0, $full_w, $full_h, $original_w, $original_h);
                imagejpeg($full, $config->site_root.DS.'administrator'.DS.'files'.DS.$tmpfolder.DS."full.jpg", 80);
                $fh = fopen($config->site_root.DS.'administrator'.DS.'files'.DS.$tmpfolder.DS."full.jpg", "rb");
                $full = fread($fh, filesize($config->site_root.DS.'administrator'.DS.'files'.DS.$tmpfolder.DS."full.jpg"));
                $f['coverart_full'] = base64_encode($full);
          
                $thumb_h = 36;
                $thumb_w = $original_w / $original_h * $thumb_h;
                $thumb = imagecreatetruecolor($thumb_w, $thumb_h);
                imagecopyresampled($thumb, $original, 0, 0, 0, 0, $thumb_w, $thumb_h, $original_w, $original_h);
                imagejpeg($thumb, $config->site_root.DS.'administrator'.DS.'files'.DS.$tmpfolder.DS."thumb.jpg", 80);
                $fh = fopen($config->site_root.DS.'administrator'.DS.'files'.DS.$tmpfolder.DS."thumb.jpg", "rb");
                $thumb= fread($fh, filesize($config->site_root.DS.'administrator'.DS.'files'.DS.$tmpfolder.DS."thumb.jpg"));
                $f['coverart_thumb'] = base64_encode($thumb);                
              }
              
              $response['files'][] = $f;
            }
          }
        }      
      } else {  
        $response['errormessage'] = 'Could not extract files';
      }
    } else {
      $response['errormessage'] = 'Could not create temp directory';
    }
  }
  
  print json_encode($response);
?>
