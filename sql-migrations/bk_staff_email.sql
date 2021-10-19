CREATE TABLE IF NOT EXISTS `crm_staff_email` (
  `EmailID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `StaffID` int(11) NOT NULL,
  `Email` varchar(45) NOT NULL,
  PRIMARY KEY (`EmailID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;