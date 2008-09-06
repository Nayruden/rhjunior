-- phpMyAdmin SQL Dump
-- version 2.8.2.4
-- http://www.phpmyadmin.net
-- 
-- Host: mysql.meg-tech.com
-- Generation Time: Sep 06, 2008 at 06:16 AM
-- Server version: 5.0.67
-- PHP Version: 4.4.7
-- 
-- Database: `rhjunior`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `comics`
-- 

CREATE TABLE `comics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `series` char(10) NOT NULL,
  `file` char(9) NOT NULL,
  `views` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=266 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `config`
-- 

CREATE TABLE `config` (
  `key` varchar(32) NOT NULL,
  `value` varchar(64) NOT NULL,
  PRIMARY KEY  (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `series`
-- 

CREATE TABLE `series` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `series` char(10) NOT NULL,
  `remote` char(64) NOT NULL,
  `folder` char(10) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `series` (`series`),
  UNIQUE KEY `location` (`folder`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
