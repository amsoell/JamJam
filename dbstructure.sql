CREATE TABLE `radio_album` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci default NULL,
  `year` int(11) default NULL,
  `description` mediumtext collate utf8_unicode_ci,
  `coverart_thumb` text collate utf8_unicode_ci,
  `coverart_full` text collate utf8_unicode_ci,
  `creation` datetime default NULL,
  `creation_user` int(11) default NULL,
  `creation_remoteaddr` varchar(15) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2549 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `radio_artist` (
  `id` int(11) NOT NULL auto_increment,
  `sortname` varchar(60) collate utf8_unicode_ci default NULL,
  `name` varchar(60) collate utf8_unicode_ci default NULL,
  `creator` int(11) default NULL,
  `creation` datetime default NULL,
  `remoteaddr` varchar(15) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1382 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `radio_artist_album` (
  `artist` int(11) NOT NULL default '0',
  `album` int(11) NOT NULL default '0',
  PRIMARY KEY  (`artist`,`album`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `radio_artist_track` (
  `artist` int(11) NOT NULL default '0',
  `track` int(11) NOT NULL default '0',
  PRIMARY KEY  (`artist`,`track`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `radio_filter` (
  `id` int(11) NOT NULL auto_increment,
  `inclusive` tinyint(4) NOT NULL default '0',
  `targetfield` varchar(30) default NULL,
  `_operation` varchar(10) default NULL,
  `subquery` int(11) default NULL,
  `maximum` int(11) NOT NULL default '1',
  `applicationmask` int(4) NOT NULL default '0',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=ascii;

CREATE TABLE `radio_filter_param` (
  `id` int(11) NOT NULL auto_increment,
  `filter` int(11) default NULL,
  `param` varchar(40) default NULL,
  `value` varchar(40) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=ascii;

CREATE TABLE `radio_queue` (
  `id` int(11) NOT NULL auto_increment,
  `track` int(11) default NULL,
  `user` int(11) NOT NULL default '0',
  `queued` datetime default NULL,
  `remoteaddr` varchar(15) collate utf8_unicode_ci default NULL,
  `status` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=42336 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `radio_rating` (
  `id` int(11) NOT NULL,
  `name` varchar(30) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=ascii;

CREATE TABLE `radio_sighting` (
  `id` int(11) NOT NULL auto_increment,
  `user` int(11) default NULL,
  `seen` datetime default NULL,
  `stream_remoteaddr` varchar(15) default NULL,
  `stream_useragent` varchar(250) default NULL,
  `web_remoteaddr` varchar(15) default NULL,
  `web_useragent` varchar(250) default NULL,
  `web_version` varchar(15) default NULL,
  `web_browserclass` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=ascii;

CREATE TABLE `radio_subquery` (
  `id` int(11) NOT NULL auto_increment,
  `query` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=ascii;

CREATE TABLE `radio_subquery_param` (
  `id` int(11) NOT NULL auto_increment,
  `subquery` int(11) default NULL,
  `param` varchar(40) default NULL,
  `type` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=ascii;

CREATE TABLE `radio_tag` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(40) default NULL,
  `user` int(11) default NULL,
  `remoteaddr` varchar(15) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=ascii;


CREATE TABLE `radio_track` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci default NULL,
  `album` int(11) default NULL,
  `tracknumber` int(11) default NULL,
  `path` mediumtext collate utf8_unicode_ci,
  `q` tinyint(4) NOT NULL default '0',
  `lastqueue` datetime default NULL,
  `filtermask` int(11) NOT NULL default '0',
  `queueCount` int(11) NOT NULL default '0',
  `playCount` int(11) NOT NULL default '0',
  `length` int(11) default NULL,
  `bitrate` float default NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=25334 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `radio_track_tag` (
  `track` int(11) NOT NULL default '0',
  `tag` int(11) NOT NULL default '0',
  `user` int(11) default NULL,
  `tagged` datetime default NULL,
  `remoteaddr` varchar(15) default NULL,
  PRIMARY KEY  (`track`,`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

CREATE TABLE `radio_user` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(40) NOT NULL,
  `name` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL default '',
  `remoteaddr` varchar(15) default NULL,
  `forcechange` tinyint(4) NOT NULL default '0',
  `lastseen` datetime default NULL,
  `creationdate` datetime default NULL,
  `creationremoteaddr` varchar(15) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=ascii;


CREATE TABLE `radio_user_track_rating` (
  `user` int(11) NOT NULL default '0',
  `track` int(11) NOT NULL default '0',
  `rating` int(11) default NULL,
  PRIMARY KEY  (`user`,`track`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii;