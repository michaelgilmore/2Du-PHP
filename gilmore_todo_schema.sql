-- --------------------------------------------------------

-- 
-- Table structure for table 'tudu_deleted'
-- 

CREATE TABLE IF NOT EXISTS tudu_deleted (
  id bigint(20) NOT NULL,
  `text` varchar(200) NOT NULL,
  created_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  updated_date timestamp NULL DEFAULT NULL,
  category varchar(32) DEFAULT NULL,
  due_date datetime DEFAULT NULL,
  original_due_date datetime DEFAULT NULL,
  label varchar(32) DEFAULT NULL,
  completed_date datetime DEFAULT NULL,
  user_id int(11) NOT NULL,
  details text,
  deleted_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table 'tudu_list_access'
-- 

CREATE TABLE IF NOT EXISTS tudu_list_access (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  list_id bigint(20) NOT NULL,
  user_id bigint(20) NOT NULL,
  updated_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  access_level enum('read_only','read_write') NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table 'tudu_lists'
-- 

CREATE TABLE IF NOT EXISTS tudu_lists (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  title varchar(128) NOT NULL,
  created_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table 'tudu_log'
-- 

CREATE TABLE IF NOT EXISTS tudu_log (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  msg varchar(500) NOT NULL,
  `level` enum('debug','info','warn','error') NOT NULL,
  user_id int(11) NOT NULL,
  log_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page` varchar(100) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table 'tudu_moved'
-- 

CREATE TABLE IF NOT EXISTS tudu_moved (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `from` datetime NOT NULL,
  `to` datetime NOT NULL,
  created_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table 'tudu_users'
-- 

CREATE TABLE IF NOT EXISTS tudu_users (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  p varbinary(64) NOT NULL,
  created_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  state enum('active','admin','retired') NOT NULL DEFAULT 'active',
  p_change_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Triggered on p field change',
  state_change_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Triggered on state field change',
  tier smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- Table structure for table 'tudus'
-- 

CREATE TABLE IF NOT EXISTS tudus (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  `text` varchar(200) NOT NULL,
  created_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  category varchar(32) DEFAULT NULL,
  due_date datetime DEFAULT NULL,
  original_due_date datetime DEFAULT NULL,
  label varchar(32) DEFAULT NULL,
  completed_date datetime DEFAULT NULL,
  user_id int(11) NOT NULL,
  details text,
  list_id bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM AUTO_INCREMENT=869 DEFAULT CHARSET=latin1 AUTO_INCREMENT=869 ;
