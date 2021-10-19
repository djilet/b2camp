CREATE TABLE IF NOT EXISTS `crm_staff_phone` (
  `PhoneID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `StaffID` int(10) unsigned NOT NULL,
  `Type` enum('mobile','work','home') NOT NULL,
  `Prefix` varchar(10) NOT NULL,
  `Number` varchar(10) NOT NULL,
  PRIMARY KEY (`PhoneID`),
  KEY `StaffID` (`StaffID`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;