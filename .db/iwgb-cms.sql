-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 10, 2019 at 11:57 PM
-- Server version: 5.6.35
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `iwgb-cms`
--
CREATE DATABASE IF NOT EXISTS `iwgb-cms` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `iwgb-cms`;

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
                       `name` varchar(20) NOT NULL,
                       `admin` varchar(100) NOT NULL,
                       `friendly_name` varchar(100) NOT NULL,
                       `friendly_singular` varchar(100) NOT NULL,
                       `type` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
                        `id` varchar(13) NOT NULL,
                        `type` varchar(50) NOT NULL,
                        `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `who` varchar(200) DEFAULT NULL,
                        `data` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
                         `id` varchar(13) NOT NULL,
                         `branchdata` text NOT NULL,
                         `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         `verified` tinyint(1) NOT NULL DEFAULT '0',
                         `confirmed` tinyint(1) NOT NULL DEFAULT '0',
                         `branch` varchar(30) NOT NULL,
                         `membership` varchar(50) NOT NULL,
                         `firstname` varchar(100) DEFAULT NULL,
                         `surname` varchar(100) DEFAULT NULL,
                         `dob` date DEFAULT NULL,
                         `gender` varchar(100) DEFAULT NULL,
                         `mobile` varchar(14) DEFAULT NULL,
                         `email` varchar(200) DEFAULT NULL,
                         `address` varchar(500) DEFAULT NULL,
                         `postcode` varchar(20) DEFAULT NULL,
                         `recentSecret` varchar(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
                       `id` varchar(13) NOT NULL,
                       `author` varchar(100) NOT NULL,
                       `blog` varchar(20) NOT NULL,
                       `content` mediumtext NOT NULL,
                       `language` varchar(2) NOT NULL DEFAULT 'en',
                       `shortlink` varchar(30) NOT NULL,
                       `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                       `posted_by` varchar(100) NOT NULL,
                       `title` varchar(200) NOT NULL,
                       `header_img` varchar(200) DEFAULT NULL,
                       `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shortlinks`
--

CREATE TABLE `shortlinks` (
                            `id` varchar(15) NOT NULL,
                            `creator` varchar(100) NOT NULL,
                            `url` varchar(200) NOT NULL,
                            `enabled` tinyint(1) DEFAULT '1',
                            `protected` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
                       `email` varchar(100) NOT NULL,
                       `pass` varchar(255) NOT NULL,
                       `permissions` varchar(20) NOT NULL,
                       `organisation` varchar(20) NOT NULL,
                       `name` varchar(100) NOT NULL,
                       `photo_id` varchar(13) NOT NULL,
                       `firstname` varchar(100) DEFAULT NULL,
                       `surname` varchar(100) DEFAULT NULL,
                       `membership_admin` tinyint(1) DEFAULT '0',
                       `contributor` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `verify`
--

CREATE TABLE `verify` (
                        `id` varchar(13) NOT NULL,
                        `token` varchar(36) DEFAULT NULL,
                        `secret` varchar(36) DEFAULT NULL,
                        `keystr` varchar(23) NOT NULL,
                        `callback` varchar(200) DEFAULT NULL,
                        `type` varchar(10) NOT NULL,
                        `contact` varchar(100) NOT NULL,
                        `verified` tinyint(1) NOT NULL DEFAULT '0',
                        `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`name`),
  ADD KEY `admin` (`admin`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posted_by` (`posted_by`),
  ADD KEY `blog` (`blog`),
  ADD KEY `author` (`author`);

--
-- Indexes for table `shortlinks`
--
ALTER TABLE `shortlinks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ShortlinkUser` (`creator`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `verify`
--
ALTER TABLE `verify`
  ADD PRIMARY KEY (`id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `fk_BlogUser` FOREIGN KEY (`admin`) REFERENCES `users` (`email`) ON UPDATE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_AuthorUser` FOREIGN KEY (`author`) REFERENCES `users` (`email`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_PostBlog` FOREIGN KEY (`blog`) REFERENCES `blogs` (`name`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_PosterUser` FOREIGN KEY (`posted_by`) REFERENCES `users` (`email`) ON UPDATE CASCADE;

--
-- Constraints for table `shortlinks`
--
ALTER TABLE `shortlinks`
  ADD CONSTRAINT `fk_ShortlinkUser` FOREIGN KEY (`creator`) REFERENCES `users` (`email`) ON UPDATE CASCADE;
