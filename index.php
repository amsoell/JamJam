<?php
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'configuration.php');
  require('include'.DS.'framework.php');  

  $ui = new UI();
  
  if (($_REQUEST['view']=='iphone') || (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')>0) || (strpos($_SERVER['HTTP_USER_AGENT'], 'iPod')>0)) {
    $q = new Queue();
    $c = new Catalog();    
    switch (strtolower($_REQUEST['panel'])) {
      case 'artist':
?>
    <ul id="albums" title="Albums">
<?php
        $albums = $c->getAlbumsByArtist($_REQUEST['id'], true, false);
        $prev = '';
          
        foreach ($albums as $album) {
?>
        <li><a href="<?php print $_SERVER['PHP_SELF']?>?panel=album&id=<?php print $album->getId(); ?>"><?php print $album->getName();?></a></li>
<?php
        }
?>
    </ul>
<?php
        break;
      case "album":
?>
    <ul id="tracks" title="Tracks">
<?php
        $tracks = $c->getTracksByAlbum($_REQUEST['id'], true, false);
        $prev = '';
          
        foreach ($tracks as $track) {
?>
        <li><a href="<?php print $_SERVER['PHP_SELF']?>?panel=track&id=<?php print $track->getId(); ?>"><?php print $track->getName();?></a></li>
<?php
        }
?>
    </ul>
<?php

        break;
      case "track":
?>
    <ul id="trackdetail" title="Details">
<?php
        $track = new Track($_REQUEST['id']);
?>
      <li class="name" style="text-align: center">
        <img src="data:image/jpg;base64,<?php print $track->album->coverart_full;?>">
      </li>    
      <li class="name">Track: <span class="secondary"><?php print $track->getName(); ?></span></li>
      <li class="artist">Artist: <span class="secondary"><?php print $track->artist[0]->getName(); ?></span></li>
      <li><a href="<?php print $_SERVER['PHP_SELF']?>?panel=main&action=queue&id=<?php print $track->getId(); ?>" class="blueButton">Request</a></li>
    </ul>
<?php

        break;
      case "main":
?>
    <ul id="home" title="Radio" selected="true" class="panel">
<?php
        switch (strtolower($_REQUEST['action'])) {
          case "queue":
            $t = new Track($_REQUEST['id']);
            $r = new Request($t);
            $q = new Queue();   
            if ($q->enqueue($r, true)) {
?>
      <fieldset>
        <div class="row">
          <label>Request</label> <span class="value">Successful</span>
        </div>
      </fieldset>
<?php
            }
          
            break;
        }    

        $nowplaying = $q->getNowPlaying();
        $upcoming = $q->getUpcoming();
        if ($nowplaying instanceof Request) {
?>
      <h2>Now Playing</h2>
      <fieldset>
        <div class="row" style="text-align: center;padding-top:4px;">
          <embed target="myself" type="audio/mpeg" loop="true" src="/images/cover_full.php?id=<?php print $nowplaying->track->album->getId();?>" href="/i/play.php" autoplay="true" width="200" height="200"></embed>
        </div>
        <div class="row">
          <label>Track</label> <span class="value"><?php print $nowplaying->track->getName(); ?></span>
        </div>
        <div class="row">
          <label>Artist</label> <span class="value"><?php print $nowplaying->track->artist[0]->getName(); ?></span>
        </div>
      </fieldset>
<?php
        }
?>
      <h2>Coming Up</h2>
      <fieldset>
<?php
        foreach ($upcoming as $request) {
?>
        <div class="row">
          <label><?php print $request->track->getName(); ?></label><span class="value"><?php print $request->track->artist[0]->getName(); ?></span>
        </div>
<?php
        }
?>
      </fieldset>
      </ul>
<?php
        break;
      default:
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  <head>
    <title>radio</title>
    <meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
    <link rel="apple-touch-icon" href="/favicon.iphone.png">
    <link rel="stylesheet" type="text/css" href="/css/iui.css" />
    <script type="text/javascript" src="/js/iui.js"></script>
  </head>
  <body>
    <div class="toolbar">
        <h1 id="pageTitle"></h1>
        <a id="backButton" class="button" href="#"></a>
        <a class="button" href="#request">Request</a>
    </div>
    <ul id="home" title="Radio" selected="true" class="panel">
<?php
        $nowplaying = $q->getNowPlaying();
        $upcoming = $q->getUpcoming();
        if ($nowplaying instanceof Request) {
?>
      <h2>Now Playing</h2>
      <fieldset>
        <div class="row" style="text-align: center;padding-top:4px;">
          <embed target="myself" type="audio/mpeg" loop="true" src="/images/cover_full.php?id=<?php print $nowplaying->track->album->getId();?>" href="/i/play.php" autoplay="true" width="200" height="200"></embed>
        </div>
        <div class="row">
          <label>Track</label> <span class="value"><?php print $nowplaying->track->getName(); ?></span>
        </div>
        <div class="row">
          <label>Artist</label> <span class="value"><?php print $nowplaying->track->artist[0]->getName(); ?></span>
        </div>
      </fieldset>
      <h2>Coming Up</h2>
      <fieldset>
<?php
        foreach ($upcoming as $request) {
?>
        <div class="row">
          <label><?php print $request->track->getName(); ?></label><span class="value"><?php print $request->track->artist[0]->getName(); ?></span>
        </div>
<?php
        }
?>
      </fieldset>
      </ul>
<?php
        }
?>  
    </ul>  
    
    <ul id="request" title="Add Request">
<?php
        $artists = $c->getArtists();
        $prev = '';
          
        foreach ($artists as $artist) {
          if ($prev != substr(strtolower($artist->sortname),0,1)) {
            $prev = substr(strtolower($artist->sortname),0,1);
?>
        <li class="group"><?php print strtoupper($prev); ?></li>
<?php
          }
?>
        <li><a href="<?php print $_SERVER['PHP_SELF']?>?panel=artist&id=<?php print $artist->getId(); ?>"><?php print $artist->getName();?></a></li>
<?php
        }
?>
    </ul>    
  </body>
</html>
<?php
    }
  } else { 
?>
<html>
  <head>
    <title>radio</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">    
    <link rel="stylesheet" type="text/css" href="/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="/css/master.css" />
    <link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" /> 
    <link rel="stylesheet" type="text/css" href="/css/jScrollPane.css" />       
    <link rel="stylesheet" type="text/css" href="/css/jquery.thickbox.css" />         
    <link rel="stylesheet" type="text/css" href="/css/scrollIndex.php" />    
    <script type="text/javascript" src="/js/jquery.js"></script>
    <script type="text/javascript" src="/js/jquery.mousewheel.js"></script>      
    <script type="text/javascript" src="/js/jquery.bgiframe.js"></script>    
    <script type="text/javascript" src="/js/jquery.autocomplete.js"></script>          
    <script type="text/javascript" src="/js/jquery.ui.base.js"></script>
    <script type="text/javascript" src="/js/jquery.ui.draggable.js"></script>
    <script type="text/javascript" src="/js/jquery.ui.droppable.js"></script>
    <script type="text/javascript" src="/js/jquery.jScrollPane.js"></script>    
    <script type="text/javascript" src="/js/jquery.thickbox.js"></script>    
    <script type="text/javascript" src="/js/jquery.flash.js"></script> 
    <script type="text/javascript">
    <!--
      $(document).ready(function() {
      
          $(window).resize(function() {
              if (typeof(resizeTimer)!='undefined') clearTimeout(resizeTimer);
              resizeTimer = setTimeout(resizeContent, 100);
            });            
      
          // Handle artist/album/track browsing
          // Includes 'draggable' code for tracks
          $('#musictree ul.artist li.name').click(function() {
              expandArtist($(this).parent().attr('id').substring(7), true);
            });
            
          // Handle dropping of tracks on the queue
          $("#queue").droppable({
              
            	accept: ".track",
	            activeClass: 'droppable-active',
	            hoverClass: 'droppable-hover',
	            drop: function(ev, ui) {
  	              if (enqueue(ui.draggable.attr('id').substring(6))) {
                    $('#'+$(ui.draggable).attr('id')).addClass('queueable');
  	              }
	              }
            });  
            
          $('#searchField').autocomplete('/ajax/doSearch.php', {
              width: 320,
              max: 10,
              highlight: false,
              scroll: true,
              scrollHeight: 400,
              formatItem: function(data, i, n, value) {
                  var track = eval('('+value+')');
                  $result = $("<div>").addClass('searchResult').addClass('track').attr('id', 'track_'+track.id).html('<span class="coverart"><img src="'+generateCoverartSrc(track.album.coverart_thumb, track.album.id)+'"></span><span class="track"><span class="name">'+track.name+'</span><span class="details"><span class="artist">'+track.artist[0].name+'</span> <span class="album">('+track.album.name+')</span></span>').draggable({
                      helper: function() {
                          return createRequestNode(track.id, track.name, track.artist[0].name, generateCoverartSrc(track.album.coverart_thumb, track.album.id));
                        }
                    }).click(function() {
                        expandArtist(track.artist[0].id, true, track.album.id, track.id)
                      });
                  return $result;
                },
              formatResult: function(data, value) {
                  var track = eval('('+value+')');
                  return track.name+'; '+track.artist[0].name+'; '+track.album.name;
                }
            });
            
          $('form#login').submit(function() {
              theform = $(this);
              
              $.ajax({
                  url: '/ajax/doAuthenticate.php',
                  data:  $('input').serialize()+'&web_version='+$.browser.version+'&web_browserclass='+($.browser.msie?'msie':($.browser.mozilla?'mozilla':($.browser.safari?'safari':($.browser.opera?'opera':'other')))), 
                  async: false,
                  dataType: 'json',
                  success: function(data) {
                      if (data.success) {
                        serverMessage($('#loginForm .serverMessage'), 'Successful login');
                        $('#loginForm form').fadeOut('slow');
                      } else {
                        serverMessage($('#loginForm .serverMessage'), 'Invalid Username/Password')
                      }
                    },
                  error: function(data) {
                      $('#loginForm .serverMessage').html('Communications Error');
                    },
                  complete: function() {  
                      setTimeout(refreshQueue, <?php echo ($config->queue_refresh*1000); ?>);
                    }
                });
              
              return false;
            });             
          
          function expandArtist(artistid, scrollTo, albumid, trackid) {
            if ((typeof(artistid)!='undefined') && (artistid!='')) {
              artistNode = $('#artist_'+artistid);
              if ((typeof scrollTo != 'undefined') && (scrollTo == true)) {
                scrollBy = $('#artist_'+artistid).offset().top - 200;
                $.each($('.albuminfo:visible'), function() {
                    if ($('#artist_'+artistid).offset().top > $(this).offset().top) {
                      scrollBy -= $('.albuminfo:visible').innerHeight();
                    }
                  });
                if (scrollBy<-100 || scrollBy>300) $('#musictree')[0].scrollBy(scrollBy);                              
              }
  
              $('.albuminfo:visible').slideUp('slow', function() {
                  $(this).empty();
                });
              $.ajax({
                  url: '/ajax/getAlbumsByArtist.php?artist='+artistid,
                  async: false,
                  dataType: 'json',
                  success: function(data) {
                              $.each(data, function(i, album) {
                                  newNode = $('#albumtemplate').clone().attr('id', 'album_'+album.id);
                                  $(newNode).click(function() {
                                      expandAlbum(album.id);
                                    });
                                  $(newNode).children('.art').attr('src', generateCoverartSrc(album.coverart_thumb, album.id)).siblings('.detail').children('.name').html(album.name).siblings('.year').html(album.year);
  
                                  $(newNode).appendTo($(artistNode).children('.albuminfo'));                                  
                                });
                            },
                  complete: function() {
                    }
                });
             
              $(artistNode).children('.albuminfo').slideDown('slow', function() {
                  if ((typeof albumid != 'undefined')) expandAlbum(albumid, trackid);            
                });
            }
          }
          
          function expandAlbum(albumid, trackid) {
            if (! $('#album_'+albumid).hasClass('active')) {
              albumNode = $('#album_'+albumid);
              
              $.ajax({
                  url: '/ajax/getAlbumDetail.php?album='+albumid,
                  async: false,
                  dataType: 'json',
                  success: function(data) {
                      $('.tracks.active').removeClass('active').slideUp('fast').empty();
                      $('.album.active').removeClass('active');
                      $(albumNode).addClass('active');
                      $('.album .art.active').removeClass('active').animate({ width: 45, height: 45 }, 500);
                      $.each(data.tracks, function(i, track) {
                          $(albumNode).children('.detail').children('.tracks').append($('<li>').addClass('track').addClass(track.queueable?'queueable':'unqueued').attr('value', track.tracknumber).attr('id', 'track_'+track.id).html(track.name)
                          .draggable({
                              helper: function() {
                                 return createRequestNode(track.id, track.name, track.artist[0].name, $(albumNode).children('.art').attr('coverart_thumb'));
                                }
                            })
                          .dblclick(function() { 
                              if (enqueue($(this).attr('id').substr(6))) { 
                                $(this).addClass('queueable'); 
                              }
                            })
                          .click(function() {
                              getRequestDetail($(this).attr('id').substr(6), 'Details');
                            })
                          );
                        });
                      $(albumNode).children('.art').addClass('active').attr('coverart_thumb', $(albumNode).children('.art').attr('src')).attr('src', generateCoverartSrc(data.detail.coverart_full, albumid, 'full')).animate({ width: 200, height: 200 }, 500, null, function() {
                          $(albumNode).children('.detail').children('.tracks').addClass('active').fadeIn('normal', function() {
                              if (typeof(trackid) != 'undefined') {
                                $('#track_'+trackid).css('background-color', '#FFFF00');
                              }
                            });
                        });                                                                                          
                    },
                  complete: function() {
                    }
                });
            }          
          }
          
          function createRequestNode(trackid, name, artist, coverart_src) {
            theNode = $('<div>').addClass('request').addClass('middrag')
              .append($('<img>').addClass('art').attr('src', coverart_src))
              .append($('<div>').addClass('name').html(name))
              .append($('<div>').addClass('artist').html(artist))
            $('body').append($(theNode));
              
            return theNode;
          }
            
          function enqueue(track) {
            if (isAuthenticated()) {
              return $.ajax({
                  url: '/ajax/updateQueue.php?action=enqueue&track='+track,
                  async: false,
                  dataType: 'json',
                  success: function(data) {
                      rebuildQueue(data.queue);
                      return data.success;
                    },
                  error: function() {
                      return false;
                    },
                  complete: function() {
                    }
                });
            } else {
              loginPrompt();
              return false;
            }
          }

          function dequeue(request) {
            if (isAuthenticated()) {
              return $.ajax({
                  url: '/ajax/updateQueue.php?action=dequeue&request='+request,
                  async: false,
                  dataType: 'json',
                  success: function(data) {
                      rebuildQueue(data.queue);
                      return data.success;
                    },
                  error: function() {
                      return false;
                    },
                  complete: function() {
                    }
                });
            } else {
              loginPrompt();
              return false;
            }
          }

          
          function loginPrompt() {
            alert('You must login to do that');
          }
          
          function inspectorIcon() {
            icon = $('<img>').addClass('inspect').attr('src','/images/icons/search.png');
            
            return $(icon);
          }
          
          function rebuildQueue(data) {
            getRequestDetail(data.nowplaying.track.id, 'Now Playing', data.nowplaying);
          
            $('#queue').empty().append($('<li>').attr('id', 'request_'+data.nowplaying.id).addClass('nowPlaying').addClass('request').attr('rel', '/ajax/getRequestDetail.php?request='+data.nowplaying.id).attr('title', data.nowplaying.track.name).append($('<img>').attr('src', generateCoverartSrc(data.nowplaying.track.album.coverart_thumb, data.nowplaying.track.album.id)).addClass('art')).append($('<div>').addClass('name').html(data.nowplaying.track.name)).append($('<div>').addClass('artist').html(data.nowplaying.track.artist[0].name)).click(function() {
                  getRequestDetail(data.nowplaying.track.id, 'Now Playing', data.nowplaying);
                }));
            document.title = 'radio ['+data.nowplaying.track.name.toLowerCase()+' - '+data.nowplaying.track.artist[0].name.toLowerCase()+']';                
            $.each(data.upcoming, function(i, request) {
                $('#queue').append($('<li>').attr('id', 'request_'+request.id).addClass('request').attr('rel', '/ajax/getRequestDetail.php?request='+request.id).attr('title', request.track.name).append($('<img>').attr('src', generateCoverartSrc(request.track.album.coverart_thumb, request.track.album.id)).addClass('art')).append($('<div>').addClass('name').html(request.track.name)).append($('<div>').addClass('artist').html(request.track.artist[0].name)).click(function() {
                  getRequestDetail(request.track.id, 'Details', request);
                }).draggable({
                    axis: 'y', 
                    distance: 5,
                    revert: true,
                    stop: function(e, ui) {
                      }
                  }).droppable({
                    accept: '.request', 
                    activeClass: 'droppable-active', 
                    hoverClass: 'droppable-hover', 
                    drop: function(ev, ui) { 
                        $.ajax({
                            url: '/ajax/updateQueue.php?action=move&orig='+$(ui.draggable).attr('id').substring(8)+'&dest='+$(this).attr('id').substring(8),
                            async: false,
                            dataType: 'json',
                            success: function(data) {
                                rebuildQueue(data.queue);
                              },
                            complete: function() {
                              }
                          });
                      }
                  }));
              });
          }      
          
          function refreshQueue() {
            $.ajax({
                url: '/ajax/getQueue.php',
                async: false,
                dataType: 'json',
                success: function(data) {
                    rebuildQueue(data);
                  },
                complete: function() {  
                    setTimeout(refreshQueue, <?php echo ($config->queue_refresh*1000); ?>);
                  }
              });
          }  
                        
          function getRequestDetail(track, caption, request) {
            $.ajax({
                url: '/ajax/getTrackDetail.php',
                data:  'track='+track+'&expanded=1',
                async: false,
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                    
                      req = $('<div>').attr('id', 'request').append($('<div>').addClass('caption').html(caption))
                        .append($('<div>').addClass('coverart').html($('<img>').attr('src', generateCoverartSrc(data.track.album.coverart_full, data.track.album.id, 'full'))))
                        .append($('<div>').addClass('name').html(data.track.name))
                        .append($('<div>').addClass('artist').html(data.track.artist[0].name).click(function() {
                            expandArtist(data.track.artist[0].id, true);
                          }))
                        .append($('<div>').addClass('album').html(data.track.album.name).click(function() {
                            expandArtist(data.track.artist[0].id, true, data.track.album.id);
                          }))
                        .append($('<div>').addClass('year').html(data.track.album.year))
                        .append($('<div>').addClass('length').html(data.track.displayLength))
                        .append($('<div>').addClass('tags').html(getTagObject(data.track.tag, data.track.id)))
                        .append($('<div>').addClass('preview').html('Preview').click(function() {
                          $(this).unbind('click').flash({ src: '/images/player.swf?autoplay=true&song_title='+data.track.name+'&player_title=Preview&song_url=/preview.php?track='+data.track.id, height: '15px', width: '200px' });
                          }));
                      if (typeof(request) != 'undefined') {
                        req.append($('<div>').addClass('dequeue').html('Remove from queue').click(function() {
                          $.ajax({
                              url: '/ajax/updateQueue.php?action=dequeue&request='+request.id,
                              async: false,
                              dataType: 'json',
                              success: function(data) {
                                  rebuildQueue(data.queue);
                                },
                              error: function() {
                                },
                              complete: function() {
                                }
                            });
                          }));
                      }
                    
                      $('#detail').html(req);
                    } else {
                      serverMessage($('#loginForm .serverMessage'), 'Invalid Username/Password')
                    }
                  },
                error: function(data) {
                    $('#loginForm .serverMessage').html('Communications Error');
                  }
              });
          
          }
          
          function getTagObject(tags, trackid) {
            tagObject = $('<ul>');
            
            $.each(tags, function(i, tag) {
                $(tagObject).append($('<li>').addClass('tag').html(tag.name));                                                                                                                                                                                                                                                                          
              });
              
            if (isAuthenticated()) {
              $(tagObject).append($('<li>').addClass('add').html('Add Tag').click(function() {
                  $(this).unbind('click');
                  addLink = $(this);
                  $.ajax({
                      url: '/ajax/getTags.php',
                      async: false,
                      dataType: 'json',
                      success: function(data) {
                        selectObj = $('<select>').attr('name','tag').append($('<option>').val(''));
                        $.each(data, function(i, tag) {
                            $(selectObj).append($('<option>').val(tag.id).html(tag.name));
                          })
                        $(selectObj).append($('<option>').val('--new--').html('New Tag...')).change(function() {
                            if ($(this).children('option:selected').val()=='--new--') {
                              $(this).replaceWith($('<input>').attr('name','tag'));
                            }
                          });
                        
                        $(addLink).empty().append($('<form>').append(selectObj).append($('<input type="button">').click(function() {
                          newtag = $(this).parent().children('[name=tag]:input').val();
                          $.ajax({
                              url: '/ajax/addTag.php',
                              data: {
                                  'track': trackid,
                                  'tag': newtag
                                },
                              async: false,
                              dataType: 'json',
                              success: function(data) {
                                  $(tagObject).replaceWith(getTagObject(data.tags, trackid));
                                }
                              });
                            })));
                        }
                    })            
                }));
              }
            
            return tagObject;
          }
          
          function serverMessage(target, message) {
              $(target).html(message).animate({ opacity: 0.3 }, function() { $(this).animate({ opacity: 1.0 }); });            
          }        
          
          function isAuthenticated() {
            if ((typeof($_UID)=='undefined') || ($_UID==null)) {
              $.ajax({
                  url: '/ajax/getAuthenticatedUser.php',
                  async: false,
                  dataType: 'json',
                  success: function(data) {
                      if (data.authenticated==true) {
                        $_UID = data.id;
                        return true;
                      } else {
                        return false;
                      }
                    },
                  error: function() {
                      return false;
                    }
                });
            } else {
              return true;
            }
          }  
          
          function generateCoverartSrc(src, albumid, size) {
            if (typeof(size)=='undefined') size = 'thumb';
            
            return 'data:image/jpg;base64,' + src;
          }
          
          function resizeContent(){
            $('#content').css('height', $('body').innerHeight()-50+'px');           
            $('#content #musictree').css('height', $('body').innerHeight()-50+'px');  
            
            $('#reference').css('height', $('body').innerHeight()-51+'px');
            $('#reference #queue').css('height', ($('body').innerHeight()-50-$('#reference #detail').innerHeight())+'px');
            $('#reference #detail').css('top', $('#reference').innerHeight()-$('#reference #detail').innerHeight());
                                 
            $('#musictree').jScrollPane({
                animateTo: true,
                animateInterval: 20,
                scrollbarWidth: 20
              });              
           $('.jScrollPaneContainer .jScrollPaneTrack').
            append($('<div>').addClass('scrollLink').attr('id','a').html('A')).
            append($('<div>').addClass('scrollLink').attr('id','b').html('B')).
            append($('<div>').addClass('scrollLink').attr('id','c').html('C')).
            append($('<div>').addClass('scrollLink').attr('id','d').html('D')).
            append($('<div>').addClass('scrollLink').attr('id','e').html('E')).
            append($('<div>').addClass('scrollLink').attr('id','f').html('F')).
            append($('<div>').addClass('scrollLink').attr('id','g').html('G')).
            append($('<div>').addClass('scrollLink').attr('id','h').html('H')).
            append($('<div>').addClass('scrollLink').attr('id','i').html('I')).
            append($('<div>').addClass('scrollLink').attr('id','j').html('J')).
            append($('<div>').addClass('scrollLink').attr('id','k').html('K')).
            append($('<div>').addClass('scrollLink').attr('id','l').html('L')).
            append($('<div>').addClass('scrollLink').attr('id','m').html('M')).
            append($('<div>').addClass('scrollLink').attr('id','n').html('N')).
            append($('<div>').addClass('scrollLink').attr('id','o').html('O')).
            append($('<div>').addClass('scrollLink').attr('id','p').html('P')).
            append($('<div>').addClass('scrollLink').attr('id','q').html('Q')).
            append($('<div>').addClass('scrollLink').attr('id','r').html('R')).
            append($('<div>').addClass('scrollLink').attr('id','s').html('S')).
            append($('<div>').addClass('scrollLink').attr('id','t').html('T')).
            append($('<div>').addClass('scrollLink').attr('id','u').html('U')).
            append($('<div>').addClass('scrollLink').attr('id','v').html('V')).
            append($('<div>').addClass('scrollLink').attr('id','w').html('W')).
            append($('<div>').addClass('scrollLink').attr('id','x').html('X')).
            append($('<div>').addClass('scrollLink').attr('id','y').html('Y')).
            append($('<div>').addClass('scrollLink').attr('id','z').html('Z'));
                         
          }
         
          refreshQueue();        
          resizeContent();
          expandArtist('<?php print $_REQUEST['artistid']; ?>', true, '<?php print $_REQUEST['albumid']; ?>', '<?php print $_REQUEST['track']; ?>');
        });    
    //-->
    </script>
  </head>
  <body>
    <div id="header">
      <?php $ui->displayLogin(); ?>
      <?php $ui->displaySearch(); ?>
    </div>
    <div id="reference">
      <?php $ui->displayQueue(); ?>
    </div>
    <div id="content">
      <?php $ui->displayMusicTree(); ?>
    </div>
    <script type="text/javascript">
    <!--
      $(document).ready(function() {
<?php $ui->displayJquery(); ?>
        });
    //-->
    </script>
  </body>
</html>
<?php
  }
?>