-- phpMyAdmin SQL Dump
-- version 2.6.1
-- http://www.phpmyadmin.net
-- 
-- Host: www.yourdomain.com
-- Generation Time: Dec 02, 2005 at 08:56 AM
-- Server version: 3.23.58
-- PHP Version: 4.3.2
-- 
-- Database: `Request`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `Authorization`
-- 

CREATE TABLE `Authorization` (
  `id` int(10) NOT NULL auto_increment,
  `type` enum('CER','PO') NOT NULL default 'CER',
  `type_id` varchar(10) NOT NULL default '',
  `issuer` varchar(5) default NULL,
  `issuerDate` datetime default NULL,
  `app1` varchar(5) default NULL,
  `app1yn` enum('yes','no','hold') default NULL,
  `app1Com` varchar(100) default NULL,
  `app1Date` datetime default NULL,
  `app2` varchar(5) default NULL,
  `app2yn` enum('yes','no','hold') default NULL,
  `app2Com` varchar(100) default NULL,
  `app2Date` datetime default NULL,
  `app3` varchar(5) default NULL,
  `app3yn` enum('yes','no','hold') default NULL,
  `app3Com` varchar(100) default NULL,
  `app3Date` datetime default NULL,
  `app4` varchar(5) default NULL,
  `app4yn` enum('yes','no','hold') default NULL,
  `app4Com` varchar(100) default NULL,
  `app4Date` datetime default NULL,
  `app5` varchar(5) default NULL,
  `app5yn` enum('yes','no','hold') default NULL,
  `app5Com` varchar(100) default NULL,
  `app5Date` datetime default NULL,
  `app6` varchar(5) default NULL,
  `app6yn` enum('yes','no','hold') default NULL,
  `app6Com` varchar(100) default NULL,
  `app6Date` datetime default NULL,
  `app7` varchar(5) default NULL,
  `app7yn` enum('yes','no','hold') default NULL,
  `app7Com` varchar(100) default NULL,
  `app7Date` datetime default NULL,
  `app8` varchar(5) default NULL,
  `app8yn` enum('yes','no','hold') default NULL,
  `app8Com` varchar(100) default NULL,
  `app8Date` datetime default NULL,
  `app9` varchar(5) default NULL,
  `app9yn` enum('yes','no','hold') default NULL,
  `app9Com` varchar(100) default NULL,
  `app9Date` datetime default NULL,
  `app10` varchar(5) default NULL,
  `app10yn` enum('yes','no','hold') default NULL,
  `app10Com` varchar(100) default NULL,
  `app10Date` datetime default NULL,
  `app11` varchar(5) default NULL,
  `app11yn` enum('yes','no','hold') default NULL,
  `app11Com` varchar(100) default NULL,
  `app11Date` datetime default NULL,
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=2644 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `CER`
-- 

CREATE TABLE `CER` (
  `id` int(10) NOT NULL auto_increment,
  `purpose` varchar(100) NOT NULL default '',
  `cer` varchar(10) default NULL,
  `reqDate` date NOT NULL default '0000-00-00',
  `date1` date default NULL,
  `date2` date default NULL,
  `req` varchar(5) default NULL,
  `location` varchar(5) default NULL,
  `company` varchar(5) NOT NULL default '1',
  `projClass` varchar(5) default NULL,
  `capBudget` varchar(5) default NULL,
  `amtBudget` varchar(15) default NULL,
  `budgetTrans` varchar(15) default NULL,
  `summary` text,
  `file_name` varchar(50) default NULL,
  `file_type` varchar(25) default NULL,
  `file_ext` varchar(10) default NULL,
  `file_size` int(25) default NULL,
  `assetCost` varchar(15) default NULL,
  `accCost` varchar(15) default NULL,
  `frtInstall` varchar(15) default NULL,
  `otherCost` varchar(15) default NULL,
  `totalCost` varchar(15) default NULL,
  `netValue` varchar(15) default NULL,
  `rateOfReturn` varchar(5) default NULL,
  `netAsset` varchar(5) default NULL,
  `payback` varchar(5) default NULL,
  `assetLife` varchar(5) default NULL,
  `firstYr` varchar(4) default NULL,
  `secYr` varchar(4) default NULL,
  `thirdYr` varchar(4) default NULL,
  `forthYr` varchar(4) default NULL,
  `totalExp` varchar(15) default NULL,
  `firstExp` varchar(15) default NULL,
  `secExp` varchar(15) default NULL,
  `thirdExp` varchar(15) default NULL,
  `forthExp` varchar(15) default NULL,
  `status` enum('N','A','X','C') NOT NULL default 'N',
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `Comments`
-- 

CREATE TABLE `Comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `submitted` date NOT NULL default '0000-00-00',
  `eid` varchar(5) NOT NULL default '',
  `type` varchar(5) NOT NULL default '',
  `comment` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `Items`
-- 

CREATE TABLE `Items` (
  `id` int(11) NOT NULL auto_increment,
  `type_id` int(10) default '0',
  `qty` varchar(4) default NULL,
  `descr` varchar(100) default NULL,
  `price` decimal(12,2) default NULL,
  `cat` varchar(25) default NULL,
  `unit` varchar(5) default NULL,
  `part` varchar(20) default NULL,
  `vt` varchar(10) default NULL,
  `plant` varchar(5) default NULL,
  `rec` enum('Y','N') default 'N',
  PRIMARY KEY  (`id`),
  KEY `type_id` (`type_id`)
) TYPE=MyISAM AUTO_INCREMENT=8854 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `Message`
-- 

CREATE TABLE `Message` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `posted` date NOT NULL default '0000-00-00',
  `message` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `PO`
-- 

CREATE TABLE `PO` (
  `id` int(10) NOT NULL auto_increment,
  `po` varchar(7) default NULL,
  `req` varchar(5) default NULL,
  `reqDate` datetime default NULL,
  `ship` int(5) default NULL,
  `sup` int(5) default NULL,
  `fob` varchar(15) default NULL,
  `terms` varchar(15) default NULL,
  `job` varchar(15) default NULL,
  `via` varchar(15) default NULL,
  `company` int(5) default '0',
  `department` int(5) default NULL,
  `purpose` varchar(100) default NULL,
  `file_name` varchar(50) default NULL,
  `file_type` varchar(25) default NULL,
  `file_ext` varchar(10) default NULL,
  `file_size` int(25) default NULL,
  `file2` enum('0','1') NOT NULL default '0',
  `total` varchar(50) default NULL,
  `cer` int(10) default NULL,
  `status` enum('N','A','O','R','X','C') default 'N',
  `dueDate` date default NULL,
  `orderDate` datetime default NULL,
  `recDate` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=3115 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `Settings`
-- 

CREATE TABLE `Settings` (
  `id` int(5) NOT NULL auto_increment,
  `company` int(5) NOT NULL default '0',
  `variable` varchar(50) NOT NULL default '',
  `value` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=60 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `Summary`
-- 

CREATE TABLE `Summary` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `module` varchar(50) NOT NULL default '',
  `eid` varchar(5) NOT NULL default '',
  `access` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=256 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `Supplier`
-- 

CREATE TABLE `Supplier` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(35) default NULL,
  `address` varchar(35) default NULL,
  `city` varchar(35) default NULL,
  `state` char(2) default NULL,
  `state0` varchar(50) default NULL,
  `zip5` varchar(5) default NULL,
  `zip4` varchar(4) default NULL,
  `contact` varchar(35) default NULL,
  `phone` varchar(15) default NULL,
  `ext` varchar(5) default NULL,
  `fax` varchar(15) default NULL,
  `email` varchar(50) default NULL,
  `web` varchar(50) default NULL,
  `country` char(2) NOT NULL default 'US',
  `status` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=368 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `Users`
-- 

CREATE TABLE `Users` (
  `eid` varchar(5) NOT NULL default '',
  `access` int(11) NOT NULL default '0',
  `requester` enum('0','1') NOT NULL default '1',
  `one` enum('0','1') NOT NULL default '0',
  `two` enum('0','1') NOT NULL default '0',
  `three` enum('0','1') NOT NULL default '0',
  `issuer` enum('0','1') NOT NULL default '0',
  `cer` tinyint(2) NOT NULL default '0',
  `online` enum('0','1') NOT NULL default '0',
  `vacation` varchar(5) NOT NULL default '0',
  `aprint` enum('0','1') NOT NULL default '0',
  `status` enum('0','1') NOT NULL default '0',
  `disabledBy` varchar(5) default NULL,
  `disableDate` date default NULL,
  PRIMARY KEY  (`eid`)
) TYPE=MyISAM;
