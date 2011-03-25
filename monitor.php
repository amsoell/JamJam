<?php
  define('DS', DIRECTORY_SEPARATOR);
  require('..'.DS.'configuration.php');
  require('include'.DS.'framework.php');  

  $ui = new UI();
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">    
    <link rel="stylesheet" type="text/css" href="/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="/css/monitor.css" />
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
                  $result = $("<div>").addClass('searchResult').addClass('track').attr('id', 'track_'+track.id).html('<span class="coverart"><img src="data:image/jpg;base64,'+track.album.coverart_thumb+'"></span><span class="track"><span class="name">'+track.name+'</span><span class="details"><span class="artist">'+track.artist[0].name+'</span> <span class="album">('+track.album.name+')</span></span>').draggable({
                      helper: function() {
                          return $(this).clone().appendTo('body');
                        }
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
          
          function expandArtist(artistid, scrollTo, albumid) {
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
                                $(newNode).children('.art').attr('src', (album.coverart_thumb!=null)?(jQuery.browser.msie?'/coverart.php?album='+album.id+'&size=thumb':'data:image/jpg;base64,'+album.coverart_thumb):'/images/noart_thumb.jpg').siblings('.detail').children('.name').html(album.name).siblings('.year').html(album.year);

                                $(newNode).appendTo($(artistNode).children('.albuminfo'));                                  
                              });
                          },
                complete: function() {
                  }
              });
           
            $(artistNode).children('.albuminfo').slideDown('slow', function() {
                if ((typeof albumid != 'undefined')) expandAlbum(albumid);            
              });
          }
          
          function expandAlbum(albumid) {
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
                          $(albumNode).children('.detail').children('.tracks').append($('<li>').addClass('track').addClass(track.queueable?'queueable':'unqueued').attr('value', track.tracknumber).attr('id', 'track_'+track.id).html(track.name).draggable({
                              helper: function() {
                                 return createRequestNode(track.id, track.name, $(albumNode).children('.art').attr('coverart_thumb'));
                                }
                            }).dblclick(function() { if (enqueue($(this).attr('id').substr(6))) $(this).addClass('queueable'); }));
                        });
                      $(albumNode).children('.art').addClass('active').attr('coverart_thumb', $(albumNode).children('.art').attr('src')).attr('src', (data.detail.coverart_full!=null?(jQuery.browser.msie?'/coverart.php?album='+album.id+'&size=full':'data:image/jpg;base64,'+data.detail.coverart_full):'/images/noart_full.jpg')).animate({ width: 200, height: 200 }, 500, null, function() {
                          $(albumNode).children('.detail').children('.tracks').addClass('active').fadeIn();
                        });                                                                                          
                    },
                  complete: function() {
                    }
                });
            }          
          }
          
          function createRequestNode(trackid, name, coverart_src) {
            theNode = $('<div>').addClass('request').addClass('middrag')
              .append($('<img>').attr('src', coverart_src))
              .append($('<div>').addClass('name').html(name))
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
          
          function rebuildQueue(data) {
            $('#detail').html(getRequestDetail(data.nowplaying));
          
            $('#queue').empty().append($('<li>').attr('id', 'request_'+data.nowplaying.id).attr('rel', '/ajax/getRequestDetail.php?request='+data.nowplaying.id).attr('title', data.nowplaying.track.name).addClass('nowplaying').html('<span class="detail"><span class="name">'+data.nowplaying.track.name+'</span> - <span class="artist">'+data.nowplaying.track.artist[0].name+'</span></span>').mouseover(function() { 
                $('#detail').html(getRequestDetail(data.nowplaying));
              }).prepend('<img src="data:image/jpg;base64,'+data.nowplaying.track.album.coverart_thumb+'">'));
            $.each(data.upcoming, function(i, request) {
                $('#queue').append($('<li>').attr('id', 'request_'+request.id).addClass('request').attr('rel', '/ajax/getRequestDetail.php?request='+request.id).attr('title', request.track.name).html('<span class="name">'+request.track.name+'</span><span name="artist">'+request.track.artist[0].name+'</span>').mouseover(function() {
                  $('#detail').html(getRequestDetail(request));
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
                  }).prepend('<img src="data:image/jpg;base64,'+request.track.album.coverart_thumb+'">'));
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
                        
          function getRequestDetail(request) {
            req = $('<div>').attr('id', 'request')
              .append($('<div>').addClass('coverart').html($('<img>').attr('src', 'data:image/jpg;base64,'+request.track.album.coverart_full)))
              .append($('<div>').addClass('name').html(request.track.name))
              .append($('<div>').addClass('artist').html(request.track.artist[0].name).click(function() {
                  expandArtist(request.track.artist[0].id, true);
                }))
              .append($('<div>').addClass('album').html(request.track.album.name).click(function() {
                  expandArtist(request.track.artist[0].id, true, request.track.album.id);
                }))
              .append($('<div>').addClass('year').html(request.track.album.year))
              .append($('<div>').addClass('length').html(request.track.displayLength))
              .append($('<div>').addClass('tags').html(getTagObject(request.track.tag, request.track.id)))
              .append($('<div>').addClass('dequeue').html('Remove from queue').click(function() {
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
              
            return req;
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
          
          function resizeContent(){
            $('#content').css('height', $('body').innerHeight()-50+'px');           
            $('#content #musictree').css('height', $('body').innerHeight()-50+'px');  
            
            $('#reference').css('height', $('body').innerHeight()-50+'px');
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
        });    
    //-->
    </script>
  </head>
  <body>
<OBJECT ID=MediaPlayer
CLASSID=CLSID:6BF52A52-394A-11D3-B153-00C04F79FAA6
standby=Loading
TYPE=application/x-oleobject width=190 height=45>
<PARAM NAME=url VALUE=http://home.amsoell.com:8000>
<PARAM NAME=AutoStart VALUE=true>
<PARAM NAME=ShowControls VALUE=1>
<PARAM NAME=uiMode VALUE=mini>
</OBJECT>  
    <div id="reference">
      <?php $ui->displayQueue(); ?>
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
