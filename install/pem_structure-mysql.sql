--
-- Table structure for table `pem_authors`
--

CREATE TABLE IF NOT EXISTS `pem_authors` (
  `idx_extension` int(11) NOT NULL,
  `idx_user` int(11) NOT NULL
) ENGINE=MyISAM;

--
-- Table structure for table `pem_categories`
--

CREATE TABLE IF NOT EXISTS `pem_categories` (
  `id_category` int(11) NOT NULL AUTO_INCREMENT,
  `idx_parent` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `idx_language` int(11) NOT NULL,
  PRIMARY KEY (`id_category`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_categories_translations`
--

CREATE TABLE IF NOT EXISTS `pem_categories_translations` (
  `idx_category` int(11) NOT NULL,
  `idx_language` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`idx_category`,`idx_language`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_download_log`
--

CREATE TABLE IF NOT EXISTS `pem_download_log` (
  `IP` varchar(15) NOT NULL DEFAULT '',
  `year` smallint(4) NOT NULL DEFAULT 0,
  `month` tinyint(2) DEFAULT NULL,
  `day` tinyint(2) DEFAULT NULL,
  `idx_revision` int(11) NOT NULL DEFAULT 0,
  KEY `download_log_i1` (`year`),
  KEY `download_log_i2` (`month`),
  KEY `download_log_i3` (`day`),
  KEY `download_log_i4` (`idx_revision`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_extensions`
--

CREATE TABLE IF NOT EXISTS `pem_extensions` (
  `id_extension` int(11) NOT NULL AUTO_INCREMENT,
  `idx_user` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `idx_language` int(11) NOT NULL,
  `svn_url` varchar(255) DEFAULT NULL,
  `git_url` varchar(255) DEFAULT NULL,
  `archive_root_dir` varchar(255) DEFAULT NULL,
  `archive_name` varchar(255) DEFAULT NULL,
  `rating_score` float(5,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_extension`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_extensions_categories`
--

CREATE TABLE IF NOT EXISTS `pem_extensions_categories` (
  `idx_category` int(11) NOT NULL DEFAULT 0,
  `idx_extension` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idx_category`,`idx_extension`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_extensions_tags`
--

CREATE TABLE IF NOT EXISTS `pem_extensions_tags` (
  `idx_extension` int(11) NOT NULL DEFAULT 0,
  `idx_tag` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`idx_extension`,`idx_tag`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_extensions_translations`
--

CREATE TABLE IF NOT EXISTS `pem_extensions_translations` (
  `idx_extension` int(11) NOT NULL,
  `idx_language` int(11) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`idx_extension`,`idx_language`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_hosting_details`
--

CREATE TABLE IF NOT EXISTS `pem_hosting_details` (
  `id_hosting_details` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(32) NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  `os` varchar(20) DEFAULT NULL,
  `pwgversion` varchar(10) DEFAULT NULL,
  `phpversion` varchar(50) DEFAULT NULL,
  `dbengine` varchar(10) DEFAULT NULL,
  `dbversion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_hosting_details`),
  KEY `uuid` (`uuid`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_languages`
--

CREATE TABLE IF NOT EXISTS `pem_languages` (
  `id_language` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `interface` enum('true','false') NOT NULL DEFAULT 'false',
  `extensions` enum('true','false') NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id_language`),
  KEY `languages_i2` (`interface`),
  KEY `languages_i3` (`extensions`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_links`
--

CREATE TABLE IF NOT EXISTS `pem_links` (
  `id_link` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `rank` int(10) unsigned NOT NULL DEFAULT 0,
  `idx_extension` int(10) unsigned NOT NULL DEFAULT 0,
  `idx_language` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_link`),
  KEY `idx_extension` (`idx_extension`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_rates`
--

CREATE TABLE IF NOT EXISTS `pem_rates` (
  `idx_user` smallint(5) NOT NULL DEFAULT 0,
  `idx_extension` int(11) NOT NULL,
  `anonymous_id` varchar(45) NOT NULL,
  `rate` float(5,2) NOT NULL DEFAULT 0.00,
  `date` datetime NOT NULL,
  PRIMARY KEY (`idx_user`,`idx_extension`,`anonymous_id`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_reviews`
--

CREATE TABLE IF NOT EXISTS `pem_reviews` (
  `id_review` int(11) NOT NULL AUTO_INCREMENT,
  `idx_user` smallint(5) unsigned NOT NULL,
  `anonymous_id` varchar(45) NOT NULL,
  `idx_extension` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `author` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `rate` float(5,2) NOT NULL,
  `validated` enum('true','false') NOT NULL DEFAULT 'false',
  `idx_language` varchar(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_review`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_revisions`
--

CREATE TABLE IF NOT EXISTS `pem_revisions` (
  `id_revision` int(11) NOT NULL AUTO_INCREMENT,
  `idx_extension` int(11) NOT NULL DEFAULT 0,
  `date` int(11) NOT NULL DEFAULT 0,
  `url` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `idx_language` int(11) NOT NULL,
  `version` varchar(25) NOT NULL DEFAULT '',
  `accept_agreement` enum('true','false') DEFAULT NULL,
  `author` int(11) DEFAULT NULL,
  `nb_downloads` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_revision`),
  KEY `revisions_i1` (`idx_extension`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_revisions_compatibilities`
--

CREATE TABLE IF NOT EXISTS `pem_revisions_compatibilities` (
  `idx_revision` int(11) NOT NULL DEFAULT 0,
  `idx_version` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idx_revision`,`idx_version`),
  KEY `idx_version_only` (`idx_version`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_revisions_languages`
--

CREATE TABLE IF NOT EXISTS `pem_revisions_languages` (
  `idx_revision` int(11) NOT NULL DEFAULT 0,
  `idx_language` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idx_revision`,`idx_language`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_revisions_translations`
--

CREATE TABLE IF NOT EXISTS `pem_revisions_translations` (
  `idx_revision` int(11) NOT NULL,
  `idx_language` int(11) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`idx_revision`,`idx_language`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_tags`
--

CREATE TABLE IF NOT EXISTS `pem_tags` (
  `id_tag` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `idx_language` int(11) NOT NULL,
  PRIMARY KEY (`id_tag`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_tags_translations`
--

CREATE TABLE IF NOT EXISTS `pem_tags_translations` (
  `idx_tag` int(11) NOT NULL,
  `idx_language` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`idx_tag`,`idx_language`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_user_infos`
--

CREATE TABLE IF NOT EXISTS `pem_user_infos` (
  `idx_user` smallint(5) NOT NULL DEFAULT 0,
  `language` varchar(50) NOT NULL DEFAULT '',
  `registration_date` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `remind_every` enum('day','week','month') NOT NULL DEFAULT 'week',
  `last_reminder` datetime DEFAULT NULL,
  UNIQUE KEY `user_infos_ui1` (`idx_user`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_users`
--

CREATE TABLE IF NOT EXISTS `pem_users` (
  `id_user` smallint(5) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `users_ui1` (`username`)
) ENGINE=MyISAM;

--
-- Table structure for table `pem_versions`
--

CREATE TABLE IF NOT EXISTS `pem_versions` (
  `id_version` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_version`),
  KEY `version_only` (`version`)
) ENGINE=MyISAM;