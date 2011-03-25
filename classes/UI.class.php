<?php
  class UI {
    private $jbuffer;
  
    public function __construct() {
    }
    
    public function displayMusicTree() {
      $c = new Catalog();
      $artists = $c->getArtists();
      
      print "<ul id=\"musictree\">\n";
      foreach ($artists as $artist) {
        print "  <ul class=\"artist\" id=\"artist_".$artist->id."\">\n";
        print "    <li class=\"name\">".$artist->name."</li>\n";
        print "    <li class=\"albuminfo\"></li>\n";
        print "  </ul>\n";
      }
      print "</ul>\n";    
      print "<div id=\"albumtemplate\" class=\"album\">\n";
      print "  <img src=\"\" class=\"art\">\n";
      print "  <div class=\"detail\">\n";
      print "    <div class=\"name\"></div>\n";
      print "    <div class=\"year\"></div>\n";
      print "    <div class=\"description\"></div>\n";
      print "    <ul class=\"tracks\"></ul>\n";
      print "  </div>\n";
      print "  <br class=\"clear\">\n";
      print "</div>\n";
    }
    
    public function displayQueue() {
      print "<ul id=\"queue\"></ul>\n";
      print "<div id=\"detail\"></div>\n";
    }
    
    public function displaySearch() {
      print "<div id=\"search\"><input id=\"searchField\"></div>\n";
    }
    
    public function displayLogin() {
      global $_USER;
      
      print "<div id=\"loginForm\">\n";
        print "<div class=\"serverMessage\"></div>\n";
      if (is_object($_USER) && ($_USER instanceof User) && $_USER->isAuthenticated()) {
        print "<a href=\"$_SERVER[PHP_SELF]?action=logout\">Log Out</a>";
      } else {
        print "<form id=\"login\" action=\"".$_SERVER['SCRIPT_NAME']."\">\n";
        print "  <fieldset>\n";
        print "    <label for=\"login_username\">Username</label> <input id=\"login_username\" name=\"username\" value=\"asoell@gmail.com\" />\n";
        print "    <label for=\"login_password\">Password</label> <input id=\"login_password\" name=\"password\" type=\"password\" value=\"everyoneelse\" />\n";
        print "  </fieldset>\n";
        print "  <input type=\"submit\" name=\"submit\" value=\"login\"> <input type=\"button\" alt=\"/ajax/getRegistrationForm.php?\" title=\"Register\" class=\"thickbox\" value=\"Register\" />\n";
        print "</form>\n";
      }
      print "</div>\n";
    }

    public function displayJquery() {
      print $this->jbuffer;
    }
    
    private function addJquery($str) {
      $this->jbuffer .= "\n$str\n";
    }
  }
?>