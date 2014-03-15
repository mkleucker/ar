-- phpMyAdmin SQL Dump
-- version 3.3.7deb6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 15, 2011 at 09:58 AM
-- Server version: 5.1.48
-- PHP Version: 5.3.3-7+squeeze3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


-- --------------------------------------------------------

--
-- Table structure for table `Layer`
--

CREATE TABLE IF NOT EXISTS `Layer` (
  `layer` varchar(255) NOT NULL,
  `refreshInterval` int(10) DEFAULT '300',
  `refreshDistance` int(10) DEFAULT '100',
  `fullRefresh` tinyint(1) DEFAULT '1',
  `showMessage` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `biwStyle` enum('classic','collapsed') DEFAULT 'classic',
  PRIMARY KEY (`id`),
  UNIQUE KEY `layer` (`layer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `Layer`
--
INSERT INTO `Layer` (`layer`, `refreshInterval`, `refreshDistance`, `fullRefresh`, `showMessage`, `id`, `biwStyle`) VALUES
('layername', 300, 100, 1, NULL, 1, 'classic');
-- --------------------------------------------------------

--
-- Table structure for table `POI`
--

CREATE TABLE IF NOT EXISTS `POI` (
  `id` varchar(255) NOT NULL,
  `footnote` varchar(150) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `lat` decimal(13,10) NOT NULL,
  `lon` decimal(13,10) NOT NULL,
  `imageURL` varchar(255) DEFAULT NULL,
  `description` varchar(150) DEFAULT NULL,
  `biwStyle` enum('classic','collapsed') DEFAULT 'classic',
  `alt` int(10) DEFAULT NULL,
  `doNotIndex` tinyint(1) DEFAULT '0',
  `showSmallBiw` tinyint(1) DEFAULT '1',
  `showBiwOnClick` tinyint(1) DEFAULT '1',
  `poiType` enum('geo','vision') NOT NULL DEFAULT 'geo',
  `layerID` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `layerID` (`layerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `POI`
--

INSERT INTO `POI` (`id`, `footnote`, `title`, `lat`, `lon`, `imageURL`, `description`, `biwStyle`, `alt`, `doNotIndex`, `showSmallBiw`, `showBiwOnClick`, `poiType`, `layerID`) VALUES
('geo_1', 'powered by Layar', 'The Layar Office', '52.3741180000', '4.9342500000', 'http://custom.layar.nl/Layar_banner_icon.png', 'The Location of the Layar Office', 'classic', NULL, 0, 1, 1, 'geo', 1);

--
-- Constraints for dumped tables
--
--
-- Constraints for table `POI`
--
ALTER TABLE `POI`
  ADD CONSTRAINT `POI_ibfk_8` FOREIGN KEY (`layerID`) REFERENCES `Layer` (`id`);
