ALTER TABLE `crm_child` 
ADD COLUMN `CreateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '' AFTER `ManagerID`;

ALTER TABLE `crm_school` 
ADD COLUMN `CreateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '' AFTER `ManagerID`;

ALTER TABLE `crm_staff` 
ADD COLUMN `CreateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '' AFTER `ManagerID`;

ALTER TABLE `crm_legal` 
ADD COLUMN `CreateDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '' AFTER `LegalOffice`;

CREATE TABLE `crm_bookkeeping` 
	(
  `BookkeepingID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  
	`Date` DATE NULL DEFAULT NULL,
  
	`DocumentNumber` VARCHAR(45) NULL DEFAULT NULL,
  
	`Check` INT UNSIGNED NOT NULL,
  
	`ArticleType` INT UNSIGNED NOT NULL,
  
	`ArticleID` INT UNSIGNED NOT NULL,
 
 	`ManagerID` INT UNSIGNED NOT NULL,
  
	`Contractor` VARCHAR(45) NULL DEFAULT NULL,
  
	`Firstname` VARCHAR(45) NULL DEFAULT NULL,
  
	`Secondname` VARCHAR(45) NULL DEFAULT NULL,
  
	`Lastname` VARCHAR(45) NULL DEFAULT NULL,
  
	`Comment` TEXT NULL DEFAULT NULL,
  
	`Amount` FLOAT UNSIGNED NOT NULL,
  
	PRIMARY KEY (`BookkeepingID`))

	ENGINE = InnoDB
DEFAULT 
	CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE `crm_parent_payback` (
  `PaybackID` INT NOT NULL AUTO_INCREMENT,
  `Created` DATETIME NOT NULL,
  `ManagerID` INT NOT NULL,
  `ContractID` INT NOT NULL,
  `Amount` INT NOT NULL,
  PRIMARY KEY (`PaybackID`));

CREATE TABLE `crm_staff_payback` (
  `PaybackID` INT NOT NULL AUTO_INCREMENT,
  `Created` DATETIME NOT NULL,
  `ManagerID` INT NOT NULL,
  `ContractID` INT NOT NULL,
  `Amount` INT NOT NULL,
  PRIMARY KEY (`PaybackID`));

CREATE TABLE `crm_school_contact_payback` (
  `PaybackID` INT NOT NULL AUTO_INCREMENT,
  `Created` DATETIME NOT NULL,
  `ManagerID` INT NOT NULL,
  `ContractID` INT NOT NULL,
  `Amount` INT NOT NULL,
  PRIMARY KEY (`PaybackID`));

CREATE TABLE `crm_legal_payback` (
  `PaybackID` INT NOT NULL AUTO_INCREMENT,
  `Created` DATETIME NOT NULL,
  `ManagerID` INT NOT NULL,
  `ContractID` INT NOT NULL,
  `Amount` INT NOT NULL,
  PRIMARY KEY (`PaybackID`));

ALTER TABLE `crm_parent_payback` 
ADD INDEX `ContractID` (`ContractID` ASC);

ALTER TABLE `crm_staff_payback` 
ADD INDEX `ContractID` (`ContractID` ASC);

ALTER TABLE `crm_school_contact_payback` 
ADD INDEX `ContractID` (`ContractID` ASC);

ALTER TABLE `crm_legal_payback` 
ADD INDEX `ContractID` (`ContractID` ASC);

ALTER TABLE `crm_bookkeeping` 
ADD COLUMN `RealComment` TEXT NULL DEFAULT NULL AFTER `Amount`;

ALTER TABLE `crm_bookkeeping` 
CHANGE COLUMN `Comment` `Base` TEXT NULL DEFAULT NULL ;

ALTER TABLE `crm_bookkeeping` 
CHANGE COLUMN `DocumentNumber` `DocumentNumber` INT(11) NULL DEFAULT NULL ;

