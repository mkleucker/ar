-- SQL to set up the database according to
-- https://www.layar.com/documentation/browser/tutorials-tools/create-simple-geo-location-layer/#prepare-the-database

CREATE TABLE IF NOT EXISTS `poi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `footnote` varchar(150) NOT NULL,
  `title` varchar(150) NOT NULL,
  `lat` decimal(13,10) NOT NULL,
  `lon` decimal(13,10) NOT NULL,
  `imageURL` varchar(255) NOT NULL,
  `description` varchar(150) NOT NULL,
  `biwstyle` enum('classic','collapsed') NOT NULL,
  `alt` int(10) NOT NULL,
  `doNotIndex` tinyint(1) NOT NULL,
  `showSmallBiw` tinyint(1) NOT NULL,
  `showBiwOnClick` tinyint(1) NOT NULL,
  `poiType` enum('geo','vision') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;