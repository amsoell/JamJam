<?php
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'..'.DS.'configuration.php');
  require('..'.DS.'include'.DS.'framework.php');  

  $ui = new UI();
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">    
    <link rel="stylesheet" type="text/css" href="/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="/css/master.css" />
    <link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />  
    <link rel="stylesheet" type="text/css" href="style.css" />        
    <script type="text/javascript" src="/js/jquery.js"></script>
    <script type="text/javascript" src="/js/jquery.flash.js"></script>    
    <script type="text/javascript" src="/js/jquery.jqUploader.js"></script>          
    <script type="text/javascript">
    <!--
      $(document).ready(function() {
          $('#addalbum').jqUploader({
             background:'FFFFFF',
             barColor:'990000',
             allowedExt:'*.zip;',
             allowedExtDescr: 'Music Archive (*.zip)',
             validFileMessage: '',
             endMessage: 'Thanks',
             hideSubmit: true,
             success: function() {
              $.ajax({
                  url: '/administrator/ajax/processZip.php',
                  data:  $('input').serialize(), 
                  async: false,
                  dataType: 'json',
                  type: 'post',
                  success: function(data) {
                      if (data.success) {
                        $('#addalbum_form').empty().append($('<input type="hidden">').attr('name','tmpdir').val(data.tmpfolder))
                                .append($('<div>').addClass('file')
                                .append($('<div>').addClass('header').addClass('path').html('File'))
                                .append($('<div>').addClass('header').addClass('name').html('Name'))
                                .append($('<div>').addClass('header').addClass('track_number').html('Track #'))
                                .append($('<div>').addClass('header').addClass('bitrate').html('Bitrate'))
                                .append($('<div>').addClass('header').addClass('playtime_seconds').html('Length')))
                                .prepend($('<input type="hidden">').attr('name','coverart_thumb').val(data.files[0].coverart_thumb))
                                .prepend($('<img>').attr('src','files/'+data.tmpfolder+'/thumb.jpg'))                           
                                .prepend($('<label>').attr('for','album_coverart_thumb').html('Album Art - Thumb'))                    
                                .prepend($('<input type="hidden">').attr('name','coverart_full').val(data.files[0].coverart_full))
                                .prepend($('<img>').attr('src','files/'+data.tmpfolder+'/full.jpg'))                           
                                .prepend($('<label>').attr('for','album_coverart_full').html('Album Art - Full'))                    
                                .prepend($('<textarea>').attr('name','albumdescription').attr('id','album_description').val(data.files[0].comments))                           
                                .prepend($('<label>').attr('for','album_description').html('Description'))                    
                                .prepend($('<input>').attr('name','year').attr('id','album_year').val(data.files[0].year))                           
                                .prepend($('<label>').attr('for','album_year').html('Year'))                    
                                .prepend($('<input>').attr('name','albumname').attr('id','album_name').val(data.files[0].album))
                                .prepend($('<label>').attr('for','album_name').html('Album name'))               
                                .prepend($('<select>').attr('name','artistid').attr('id', 'album_artist'))
                                .prepend($('<label>').attr('for','album_artist').html('Artist'));                        
                        $.each(data.files, function(i, file) {
                              $('#addalbum_form').append($('<div>').addClass('file')
                                .append($('<div>').addClass('path').html(file.path).append($('<input type="hidden">').attr('name','track['+i+'][path]').val(file.path)))
                                .append($('<div>').addClass('name').append($('<input>').attr('name','track['+i+'][name]').val(file.name)))
                                .append($('<div>').addClass('track_number').append($('<input>').attr('name','track['+i+'][tracknumber]').val(parseInt(file.track_number))))
                                .append($('<div>').addClass('bitrate').html(file.bitrate+'kbps').append($('<input type="hidden">').attr('name','track['+i+'][bitrate]').val(file.bitrate)))
                                .append($('<div>').addClass('playtime_seconds').html(file.playtime_seconds+' sec').append($('<input type="hidden">').attr('name','track['+i+'][length]').val(parseInt(file.playtime_seconds))))
                              );
                          });

                        artistGuess = 0;
                        $.ajax({
                            url: '/ajax/getArtist.php',
                            data: 'query='+data.files[0].artist,
                            async: false,
                            dataType: 'json',
                            type: 'post',
                            success: function(data) {
                                artistGuess = data.id;
                              },
                            error: function(data) {
                              },
                            complete: function() {  
                              }
                          });  
                          
                        $.ajax({
                            url: '/ajax/getArtist.php',
                            async: false,
                            dataType: 'json',
                            type: 'post',   
                            success: function(data) {
                                $.each(data, function(i, artist) {
                                    newOption = $('<option>').val(artist.id).html(artist.name);
                                    if (artist.id == artistGuess) $(newOption).attr('selected', 'selected');
                                    $('#addalbum_form select[@name=artistid]').append($(newOption));
                                  });
                                
                              },
                            error: function(data) {
                              },
                            complete: function() {  
                              }
                          });   
                          
                          $('#addalbum_form').append($('<input type="submit">').val('Confirm Detail')).submit(function() {
                            $.ajax({
                                url: '/administrator/ajax/processNewAlbum.php',
                                data: $('#addalbum_form').serialize(),
                                async: false,
                                dataType: 'json',
                                type: 'post',
                                success: function(data) {
                                   
                                  },
                                error: function(data) {
                                  },
                                complete: function() {  
                                  }
                              });   
                              
                              return false;
                            });                       
                      } else {
                        serverMessage($('#loginForm .serverMessage'), 'Invalid Username/Password')
                      }
                    },
                  error: function(data) {
                    },
                  complete: function() {  
                    }
                });
                
              }
           });
        });
    </script>
  </head>
  <body>
  </body>
    <form id="addalbum_form" enctype="multipart/form-data" action="/administrator/flash_upload.php" method="POST" class="a_form">
      <div id="addalbum"
        <label for="addalbum_field">Choose a file to upload:</label>
        <input name="albumarchive" id="addalbum_field"  type="file" />
      </div>
      <input type="submit"  name="submit" value="Upload File" />
    </form>    
  </body>
</html>
