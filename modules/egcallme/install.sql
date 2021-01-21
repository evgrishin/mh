
CREATE TABLE IF NOT EXISTS `PREFIX_DB1NAME` (
  `id_DB1NAME` int(11) NOT NULL AUTO_INCREMENT,
  `id_shop` INT( 11 ) NULL DEFAULT NULL,
  `host` varchar(30) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `date_call` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `phone` varchar(30) NOT NULL,
  `fname` varchar(20) DEFAULT NULL,
  `lname` varchar(20) DEFAULT NULL,
  `message` varchar(200) DEFAULT NULL,
  `status` varchar(20)  NOT NULL DEFAULT 'new', 
  PRIMARY KEY (`id_DB1NAME`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
