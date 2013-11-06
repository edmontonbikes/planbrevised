-- phpMyAdmin SQL Dump
-- version 2.11.9.4
-- http://www.phpmyadmin.net
--
-- Host: mysql.ybdb.austinyellowbike.org
-- Generation Time: Jan 07, 2010 at 06:55 AM
-- Server version: 5.0.67
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ybdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE IF NOT EXISTS `contacts` (
  `contact_id` int(10) unsigned NOT NULL auto_increment,
  `first_name` varchar(20) NOT NULL default '',
  `middle_initial` char(2) NOT NULL default '',
  `last_name` varchar(20) NOT NULL default '',
  `email` varchar(70) NOT NULL default '',
  `phone` varchar(45) NOT NULL default '',
  `address1` varchar(70) NOT NULL default '',
  `address2` varchar(70) NOT NULL default '',
  `city` varchar(25) NOT NULL default '',
  `state` char(2) NOT NULL default '',
  `country` varchar(25) NOT NULL default '',
  `receive_newsletter` tinyint(1) NOT NULL default '1',
  `date_created` datetime default NULL,
  `invited_newsletter` tinyint(1) NOT NULL default '0',
  `DOB` date NOT NULL default '0000-00-00',
  `pass` varbinary(30) NOT NULL default '',
  `zip` varchar(5) NOT NULL default '',
  `hidden` tinyint(1) NOT NULL default '0',
  `location_name` varchar(45) NOT NULL default '',
  `location_type` varchar(45) default NULL,
  PRIMARY KEY  (`contact_id`),
  KEY `location_type` (`location_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 5120 kB' AUTO_INCREMENT=7758 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `project_id` varchar(50) NOT NULL default '',
  `date_established` date NOT NULL default '0000-00-00',
  `active` tinyint(1) NOT NULL default '1',
  `public` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sale_log`
--

CREATE TABLE IF NOT EXISTS `sale_log` (
  `transaction_id` int(10) unsigned NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `sale_type` varchar(45) NOT NULL default '',
  `description` varchar(200) NOT NULL default '',
  `amount` float NOT NULL default '0',
  `sold_by` varchar(45) NOT NULL default '',
  `sold_to` varchar(45) NOT NULL default '',
  PRIMARY KEY  (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE IF NOT EXISTS `shops` (
  `shop_id` int(10) unsigned NOT NULL auto_increment,
  `date` date default NULL,
  `shop_location` varchar(45) NOT NULL default '',
  `shop_type` varchar(45) NOT NULL default '',
  `ip_address` varchar(45) NOT NULL default '0',
  PRIMARY KEY  (`shop_id`),
  KEY `shop_type` (`shop_type`),
  KEY `shop_location` (`shop_location`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1066 ;

-- --------------------------------------------------------

--
-- Table structure for table `shop_hours`
--

CREATE TABLE IF NOT EXISTS `shop_hours` (
  `shop_visit_id` int(10) unsigned NOT NULL auto_increment,
  `contact_id` int(10) unsigned NOT NULL default '0',
  `shop_id` int(10) unsigned NOT NULL default '0',
  `shop_user_role` varchar(45) NOT NULL default '',
  `project_id` varchar(45) default NULL,
  `time_in` datetime NOT NULL default '0000-00-00 00:00:00',
  `time_out` datetime NOT NULL default '0000-00-00 00:00:00',
  `comment` tinytext,
  PRIMARY KEY  (`shop_visit_id`),
  KEY `contact_id` (`contact_id`),
  KEY `shop_user_role` (`shop_user_role`),
  KEY `project_id` (`project_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 4096 kB; (`contact_id`) REFER `nwilkes_ybdb/con' AUTO_INCREMENT=16305 ;

-- --------------------------------------------------------

--
-- Table structure for table `shop_locations`
--

CREATE TABLE IF NOT EXISTS `shop_locations` (
  `shop_location_id` varchar(30) NOT NULL default '',
  `date_established` date NOT NULL default '0000-00-00',
  `active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`shop_location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shop_types`
--

CREATE TABLE IF NOT EXISTS `shop_types` (
  `shop_type_id` varchar(30) NOT NULL default '',
  `list_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`shop_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shop_user_roles`
--

CREATE TABLE IF NOT EXISTS `shop_user_roles` (
  `shop_user_role_id` varchar(20) NOT NULL default '',
  `hours_rank` int(10) unsigned NOT NULL default '0',
  `volunteer` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`shop_user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_log`
--

CREATE TABLE IF NOT EXISTS `transaction_log` (
  `transaction_id` int(10) unsigned NOT NULL auto_increment,
  `date_startstorage` datetime default NULL,
  `date` datetime default NULL,
  `transaction_type` varchar(45) NOT NULL default '',
  `amount` float default '0',
  `description` varchar(200) default NULL,
  `sold_to` int(10) unsigned default NULL,
  `sold_by` int(10) unsigned default NULL,
  `quantity` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`transaction_id`),
  KEY `transaction_type` (`transaction_type`),
  KEY `sold_to` (`sold_to`),
  KEY `sold_by` (`sold_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2198 ;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_types`
--

CREATE TABLE IF NOT EXISTS `transaction_types` (
  `transaction_type_id` varchar(45) NOT NULL default '',
  `rank` varchar(45) NOT NULL default '1',
  `active` tinyint(1) NOT NULL default '1',
  `community_bike` tinyint(1) NOT NULL default '0',
  `show_transaction_id` tinyint(1) NOT NULL default '0',
  `show_type` tinyint(1) NOT NULL default '0',
  `show_startdate` tinyint(1) NOT NULL default '0',
  `show_amount` tinyint(1) NOT NULL default '0',
  `show_description` tinyint(1) NOT NULL default '0',
  `show_soldto` tinyint(1) NOT NULL default '0',
  `show_soldby` tinyint(1) NOT NULL default '0',
  `fieldname_date` varchar(25) NOT NULL default '',
  `fieldname_soldby` varchar(25) NOT NULL default '',
  `message_transaction_id` varchar(100) NOT NULL default '',
  `fieldname_soldto` varchar(45) NOT NULL default '',
  `show_soldto_location` tinyint(1) NOT NULL default '0',
  `fieldname_description` varchar(45) NOT NULL,
  `accounting_group` varchar(45) NOT NULL,
  PRIMARY KEY  (`transaction_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `location_type` FOREIGN KEY (`location_type`) REFERENCES `transaction_types` (`transaction_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `shops`
--
ALTER TABLE `shops`
  ADD CONSTRAINT `shop_location` FOREIGN KEY (`shop_location`) REFERENCES `shop_locations` (`shop_location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `shop_type` FOREIGN KEY (`shop_type`) REFERENCES `shop_types` (`shop_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `shop_hours`
--
ALTER TABLE `shop_hours`
  ADD CONSTRAINT `contact_id` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`contact_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_id` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `shop_id` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `shop_user_role` FOREIGN KEY (`shop_user_role`) REFERENCES `shop_user_roles` (`shop_user_role_id`) ON UPDATE CASCADE;

--
-- Constraints for table `transaction_log`
--
ALTER TABLE `transaction_log`
  ADD CONSTRAINT `sold_by` FOREIGN KEY (`sold_by`) REFERENCES `contacts` (`contact_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sold_to` FOREIGN KEY (`sold_to`) REFERENCES `contacts` (`contact_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_type` FOREIGN KEY (`transaction_type`) REFERENCES `transaction_types` (`transaction_type_id`) ON UPDATE CASCADE;
