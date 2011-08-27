DROP TABLE IF EXISTS `fresh`.`checkin`;
CREATE TABLE  `fresh`.`checkin` (
  `checkin_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fresh_place_id` varchar(60) NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `datetime_checkin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`checkin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `fresh`.`person`;
CREATE TABLE  `fresh`.`person` (
  `person_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `last_name` varchar(20) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `fb_user_id` varchar(45) DEFAULT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(45) DEFAULT NULL,
  `pic` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `fresh`.`place_detail`;
CREATE TABLE  `fresh`.`place_detail` (
  `fresh_place_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `place_id` varchar(45) DEFAULT NULL,
  `source` char(1) NOT NULL,
  `place_name` varchar(50) NOT NULL,
  `lat` float NOT NULL,
  `lng` float NOT NULL,
  `place_ref` varchar(255) DEFAULT NULL,
  `vicinity` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  PRIMARY KEY (`fresh_place_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `fresh`.`places`;
CREATE TABLE  `fresh`.`places` (
  `fresh_place_id` int(10) unsigned NOT NULL DEFAULT '0',
  `place_id` varchar(45) NOT NULL,
  `source` char(1) NOT NULL,
  `place_name` varchar(50) NOT NULL,
  `lat` float NOT NULL,
  `lng` float NOT NULL,
  `place_ref` varchar(255) NOT NULL,
  PRIMARY KEY (`place_id`),
  KEY `Index_1` (`place_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;