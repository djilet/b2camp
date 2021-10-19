DROP TABLE IF EXISTS `crm_child_email`;
CREATE TABLE `crm_child_email` (
  `EmailID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ChildID` int(11) NOT NULL,
  `Email` varchar(45) NOT NULL,
  PRIMARY KEY (`EmailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `crm_group`;
CREATE TABLE `crm_group` (
  `GroupID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(100) NOT NULL,
  PRIMARY KEY (`GroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `crm_legal` 
CHANGE COLUMN `ManagerID` `ManagerID` INT(10) NULL DEFAULT NULL,
ADD COLUMN `INN` varchar(45) DEFAULT NULL,
ADD COLUMN `KPP` varchar(45) DEFAULT NULL,
ADD COLUMN `BankName` varchar(45) DEFAULT NULL,
ADD COLUMN `PC` varchar(45) DEFAULT NULL,
ADD COLUMN `KC` varchar(45) DEFAULT NULL,
ADD COLUMN `BIK` varchar(45) DEFAULT NULL,
ADD COLUMN `LegalPostIndex` varchar(45) DEFAULT NULL,
ADD COLUMN `LegalCountry` varchar(45) DEFAULT NULL,
ADD COLUMN `LegalCity` varchar(45) DEFAULT NULL,
ADD COLUMN `LegalStreet` varchar(45) DEFAULT NULL,
ADD COLUMN `LegalHome` varchar(45) DEFAULT NULL,
ADD COLUMN `LegalBuilding` varchar(45) DEFAULT NULL,
ADD COLUMN `LegalOffice` varchar(45) DEFAULT NULL;

DROP TABLE IF EXISTS `crm_legal_contact_email`;
CREATE TABLE `crm_legal_contact_email` (
  `EmailID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ContactID` int(11) NOT NULL,
  `Email` varchar(45) NOT NULL,
  PRIMARY KEY (`EmailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `crm_legal_contract` 
ADD COLUMN `ContactID` int(11) NOT NULL,
ADD COLUMN `TourPrice` int(11) NOT NULL,
ADD COLUMN `TourCount` int(11) NOT NULL,
ADD COLUMN `CoursePrice` int(11) NOT NULL,
ADD COLUMN `CourseCount` int(11) NOT NULL,
ADD COLUMN `LastDayForPay` date NOT NULL,
ADD COLUMN `IsNeedStamp` tinyint(4) NOT NULL;

DROP TABLE IF EXISTS `crm_legal_email`;
CREATE TABLE `crm_legal_email` (
  `EmailID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `LegalID` int(11) NOT NULL,
  `Email` varchar(45) NOT NULL,
  PRIMARY KEY (`EmailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `crm_parent_email`;
CREATE TABLE `crm_parent_email` (
  `EmailID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ParentID` int(11) NOT NULL,
  `Email` varchar(45) NOT NULL,
  PRIMARY KEY (`EmailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `crm_school_contact` 
ADD COLUMN `Passport` varchar(255) DEFAULT NULL;

ALTER TABLE `crm_school_contact_contract` 
ADD COLUMN `TourPrice` int(11) NOT NULL,
ADD COLUMN `TourCount` int(11) NOT NULL,
ADD COLUMN `CoursePrice` int(11) NOT NULL,
ADD COLUMN `CourseCount` int(11) NOT NULL,
ADD COLUMN `LastDayForPay` date NOT NULL,
ADD COLUMN `IsNeedStamp` tinyint(4) NOT NULL;

DROP TABLE IF EXISTS `crm_school_contact_email`;
CREATE TABLE `crm_school_contact_email` (
  `EmailID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ContactID` int(11) NOT NULL,
  `Email` varchar(45) NOT NULL,
  PRIMARY KEY (`EmailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `crm_school_email`;
CREATE TABLE `crm_school_email` (
  `EmailID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SchoolID` int(11) NOT NULL,
  `Email` varchar(45) NOT NULL,
  PRIMARY KEY (`EmailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `crm_season` 
ADD COLUMN `TypeID` int(10) unsigned NOT NULL;

DROP TABLE IF EXISTS `crm_season_type`;
CREATE TABLE `crm_season_type` (
  `TypeID` int(10) unsigned NOT NULL,
  `Title` varchar(45) NOT NULL,
  PRIMARY KEY (`TypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `crm_staff` 
ADD COLUMN `GroupID` int(11) NOT NULL;

DROP TABLE IF EXISTS `crm_staff_email`;
CREATE TABLE `crm_staff_email` (
  `EmailID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `StaffID` int(11) NOT NULL,
  `Email` varchar(45) NOT NULL,
  PRIMARY KEY (`EmailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `user` 
CHANGE COLUMN `Role` `Role` ENUM('integrator','administrator','manager', 'guide') NOT NULL DEFAULT 'manager' ;


INSERT INTO `crm_season_type` VALUES (1,'Лагерь'),(2,'Городская площадка'),(3,'Праздники');
INSERT INTO `crm_group` VALUES (1,'Вожатый'),(2,'Сотрудник офиса'),(3,'Сотрудник лагеря'),(4,'Аниматор');
INSERT INTO `crm_status` VALUES (7,'Не звонили');
INSERT INTO `user` VALUES (NULL, 'developer', '5e8edd851d2fdfbd7415232c67367cc3', 'Разработчик', '', 'Сайта', NULL, NULL, NULL, 'M', '', '', '', '', '', '', 'integrator', NULL, '2016-03-21 22:25:49', '127.0.0.1');
INSERT INTO `user` VALUES (NULL, 'guide', 'a0c391dc49c440fc9962168353cedde3', 'Вожат', 'Вожатович', 'Вожатый', NULL, '[]', '2016-03-07', 'M', '+7-(989)-898-98-98', '', '', '', '', '', 'guide', '2016-03-07 00:23:31', '2016-03-18 18:02:05', '127.0.0.1');


