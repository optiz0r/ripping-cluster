-- phpMyAdmin SQL Dump
-- version 3.3.0
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jan 11, 2012 at 12:27 AM
-- Server version: 5.1.53
-- PHP Version: 5.3.6-pl1-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `ripping-cluster`
--

-- --------------------------------------------------------

--
-- Table structure for table `client_log`
--

DROP TABLE IF EXISTS `client_log`;
CREATE TABLE IF NOT EXISTS `client_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(10) unsigned DEFAULT NULL,
  `level` varchar(32) NOT NULL,
  `category` varchar(32) NOT NULL,
  `ctime` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `hostname` varchar(32) NOT NULL,
  `progname` varchar(64) NOT NULL,
  `file` text NOT NULL,
  `line` int(11) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `source_plugin` varchar(255) NOT NULL COMMENT 'Partial classname of the plugin used to read the source',
  `rip_plugin` varchar(255) NOT NULL COMMENT 'Partial classname of the plugin used to perform the rip',
  `source` text NOT NULL,
  `destination` text NOT NULL,
  `title` varchar(64) NOT NULL,
  `format` varchar(4) NOT NULL,
  `video_codec` varchar(8) NOT NULL,
  `video_width` int(11) DEFAULT NULL,
  `video_height` int(11) DEFAULT NULL,
  `quantizer` float NOT NULL,
  `deinterlace` double NOT NULL,
  `audio_tracks` varchar(64) NOT NULL,
  `audio_codecs` varchar(64) NOT NULL,
  `audio_names` varchar(255) NOT NULL,
  `subtitle_tracks` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `job_status`
--

DROP TABLE IF EXISTS `job_status`;
CREATE TABLE IF NOT EXISTS `job_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(10) unsigned NOT NULL,
  `status` int(10) unsigned NOT NULL,
  `ctime` int(10) unsigned NOT NULL,
  `mtime` int(10) unsigned NOT NULL,
  `rip_progress` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `job_status_current`
--

DROP TABLE IF EXISTS `job_status_current`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `handbrake_cluster`.`job_status_current` AS select `js`.`id` AS `id`,`js`.`job_id` AS `job_id`,`js`.`status` AS `status`,`js`.`ctime` AS `ctime`,`js`.`mtime` AS `mtime`,`js`.`rip_progress` AS `rip_progress` from (`handbrake_cluster`.`job_status` `js` join `handbrake_cluster`.`job_status_current_int` `js2`) where ((`js2`.`job_id` = `js`.`job_id`) and (`js`.`id` = `js2`.`latest`));

-- --------------------------------------------------------

--
-- Table structure for table `job_status_current_int`
--

DROP TABLE IF EXISTS `job_status_current_int`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `handbrake_cluster`.`job_status_current_int` AS (select `handbrake_cluster`.`job_status`.`job_id` AS `job_id`,max(`handbrake_cluster`.`job_status`.`id`) AS `latest` from `handbrake_cluster`.`job_status` group by `handbrake_cluster`.`job_status`.`job_id`);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `type` enum('bool','int','float','string','array(string)','hash') DEFAULT 'string',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`name`, `value`, `type`) VALUES
('debug.display_exceptions', '1', 'bool'),
('rips.nice', '15', 'int'),
('cache.base_dir', '/dev/shm/hbc/', 'string'),
('rips.cache_ttl', '86400', 'int'),
('rips.job_servers', 'localhost:7003', 'string'),
('rips.context', 'dev', 'string'),
('rips.default.output_directory', '', 'string'),
('rips.handbrake_binary', '/usr/bin/HandBrakeCLI', 'string'),
('rips.nice_binary', '/usr/bin/nice', 'string'),
('source.handbrake.dir', '', 'array(string)'),
('source.mkvinfo.dir', '', 'array(string)'),
('source.bluray.dir', '', 'array(string)'),
('logging.plugins', 'Database\nFlatFile\nConsole', 'array(string)'),
('logging.Console', 'stdout', 'array(string)'),
('logging.Console.stdout.format', '%ctime% %hostname%:%pid% %progname%:%file%[%line%] %message%', 'string'),
('logging.Console.stdout.severity', 'debug\ninfo\nwarning\nerror', 'array(string)'),
('logging.Console.stdout.category', 'client\nworker', 'array(string)'),
('logging.Database', 'webui\nworker', 'array(string)'),
('logging.Database.webui.table', 'client_log', 'string'),
('logging.Database.webui.severity', 'debug\ninfo\nwarning\ndebug', 'array(string)'),
('logging.Database.webui.category', 'batch\nclient\ndefault', 'array(string)'),
('logging.Database.worker.table', 'worker_log', 'string'),
('logging.Database.worker.severity', 'debug\ninfo\nwarning\nerror', 'array(string)'),
('logging.Database.worker.category', 'worker', 'array(string)'),
('logging.FlatFile', 'stderr\nvarlog_worker', 'array(string)'),
('logging.FlatFile.stderr.filename', 'php://stderr', 'string'),
('logging.FlatFile.stderr.format', '%timestamp% %hostname%:%pid% %progname%:%file%[%line%] %message%', 'string'),
('logging.FlatFile.stderr.severity', 'warning\nerror', 'array(string)'),
('logging.FlatFile.stderr.category', 'batch\nclient\ndefault\nworker', 'array(string)'),
('logging.FlatFile.varlog_worker.filename', '/var/log/ripping-cluster/worker.log', 'string'),
('logging.FlatFile.varlog_worker.format', '%timestamp% %hostname%:%pid% %progname%:%file%[%line%] %message%', 'string'),
('logging.FlatFile.varlog_worker.severity', 'debug\ninfo\nwarning\nerror', 'array(string)'),
('logging.FlatFile.varlog_worker.category', 'worker', 'array(string)'),
('logging.Syslog', 'local0', 'array(string)'),
('logging.Syslog.local0.facility', '128', 'int'),
('logging.Syslog.local0.severity', 'debug\ninfo\nwarning\nerror', 'array(string)'),
('logging.Syslog.local0.category', 'batch\nclient\ndefault\nworker', 'array(string)'),
('logging.Syslog.local0.format', '%file%[%line%] %message%', 'string'),
('templates.tmp_path', '/var/tmp/ripping-cluster', 'string'),
('rips.temp_dir', '/tmp', 'string'),
('job.logs.default_display_count', '30', 'int'),
('job.logs.default_order', 'DESC', 'string'),
('rips.output_directories.default', '', 'hash'),
('rips.output_directories.recent', '', 'array(string)'),
('rips.output_directories.recent_limit', '10', 'int'),
('auth', 'Config', 'string'),
('auth.admin.username', 'admin', 'string'),
('auth.admin_password', '489152af89501a7dc72f6e589123b8c337c01623', 'string');

-- --------------------------------------------------------

--
-- Table structure for table `worker_log`
--

DROP TABLE IF EXISTS `worker_log`;
CREATE TABLE IF NOT EXISTS `worker_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `job_id` int(10) unsigned DEFAULT NULL,
  `level` varchar(32) NOT NULL,
  `category` varchar(32) NOT NULL,
  `ctime` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `hostname` varchar(32) NOT NULL,
  `progname` varchar(64) NOT NULL,
  `file` text NOT NULL,
  `line` int(11) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `client_log`
--
ALTER TABLE `client_log`
  ADD CONSTRAINT `client_log_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `job_status`
--
ALTER TABLE `job_status`
  ADD CONSTRAINT `job_status_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
