ALTER TABLE `crm_parent_contract` ADD `IsNeedStamp` TINYINT(1) NULL DEFAULT NULL;

ALTER TABLE `crm_questionnaire` ADD `qPersonalHobbies` VARCHAR(255) NULL DEFAULT NULL AFTER `qPersonalIndependence`;

ALTER TABLE `crm_legal` ADD `Commission` INT NULL DEFAULT NULL;

CREATE TABLE `crm_child2mailing` ( `ChildID` INT NOT NULL , `onSending` ENUM('Y','N') NULL DEFAULT NULL , `onSMS` ENUM('Y','N') NULL DEFAULT NULL , `onPhoto` ENUM('Y','N') NULL DEFAULT NULL ) ENGINE = InnoDB;
ALTER TABLE `crm_child` DROP `Mailing`;
ALTER TABLE `crm_child2mailing` ADD PRIMARY KEY( `ChildID`);

ALTER TABLE `crm_legal_contract` ADD `ChildIDs` TEXT NULL DEFAULT NULL;

ALTER TABLE `crm_child` ADD `Source` INT NULL AFTER `ManagerID`, ADD `Contact` VARCHAR(256) NULL AFTER `Source`;

ALTER TABLE `crm_school_contact_contract` ADD `ChildIDs` TEXT NULL DEFAULT NULL;
ALTER TABLE `crm_school_contact_contract` ADD `SchoolID` INT NOT NULL;

ALTER TABLE `crm_child_status` ADD `UserID` INT NULL DEFAULT NULL;
ALTER TABLE `crm_legal_status` ADD `UserID` INT NULL DEFAULT NULL;
ALTER TABLE `crm_school_status` ADD `UserID` INT NULL DEFAULT NULL;
CREATE TABLE `crm_child_status_history` ( `ChangesID` INT NOT NULL AUTO_INCREMENT , `HistoryStatusID` INT NULL DEFAULT NULL , `EventTime` DATETIME NULL DEFAULT NULL , `UserID` INT NULL DEFAULT NULL , `EventText` TINYTEXT NULL DEFAULT NULL , PRIMARY KEY (`ChangesID`)) ENGINE = InnoDB;
ALTER TABLE `crm_child` ADD `Source` INT(11) NULL DEFAULT NULL;
ALTER TABLE `crm_child` ADD `Contact` VARCHAR(256) NULL DEFAULT NULL;

INSERT INTO `crm_status` (`StatusID`, `Title`) VALUES (7, 'Предоплата');

ALTER TABLE `crm_child2mailing` ADD INDEX `ChildID` (`ChildID`);

ALTER TABLE `crm_school_status` ADD `TransferThere` ENUM('Y','N') NULL DEFAULT NULL, ADD `TransferThereNote` MEDIUMTEXT NULL DEFAULT NULL, ADD `TransferBack` ENUM('Y','N') NULL DEFAULT NULL, ADD `TransferBackNote` MEDIUMTEXT NULL DEFAULT NULL;