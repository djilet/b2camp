CREATE TABLE `crm_status2task` ( `HistoryStatusID` INT(11) UNSIGNED NOT NULL , `TaskID` INT(11) UNSIGNED NOT NULL , INDEX (`HistoryStatusID`), INDEX (`TaskID`)) ENGINE = InnoDB;
ALTER TABLE `crm_status2task` ADD `entity` ENUM('child','school','legal') NOT NULL AFTER `TaskID`;

CREATE TABLE `crm_child_document` (
  `DocumentID` int(11) UNSIGNED NOT NULL,
  `ChildID` int(11) NOT NULL,
  `Type` enum('passport','international','birth','') NOT NULL,
  `Main` tinyint(4) NOT NULL DEFAULT '0',
  `Number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `crm_child_document`
  ADD PRIMARY KEY (`DocumentID`),
  ADD KEY `ChildID` (`ChildID`);

ALTER TABLE `crm_child_document`
  MODIFY `DocumentID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `crm_child_status` ADD `TransferThere` ENUM('Y','N') NULL AFTER `Updated`, ADD `TransferThereNote` VARCHAR(255) NULL AFTER `TransferThere`, ADD `TransferBack` ENUM('Y','N') NULL AFTER `TransferThereNote`, ADD `TransferBackNote` VARCHAR(255) NULL AFTER `TransferBack`;
ALTER TABLE `crm_season` ADD `TransferThereConditions` TEXT NULL DEFAULT NULL AFTER `Comment`, ADD `TransferBackConditions` TEXT NULL DEFAULT NULL AFTER `TransferThereConditions`;

CREATE TABLE `crm_questionnaire` (
  `ChildID` int(11) UNSIGNED NOT NULL,
  `qMedChronical` varchar(255) DEFAULT NULL,
  `qMedTrauma` varchar(255) DEFAULT NULL,
  `qMedAllergy` varchar(255) DEFAULT NULL,
  `qMedSea` varchar(64) DEFAULT NULL,
  `qMedDrugs` varchar(255) DEFAULT NULL,
  `qMedSun` varchar(255) DEFAULT NULL,
  `qPhysSport` varchar(255) DEFAULT NULL,
  `qPhysSweam` varchar(255) DEFAULT NULL,
  `qPhysEyes` varchar(255) DEFAULT NULL,
  `qPersonalPhobia` varchar(255) DEFAULT NULL,
  `qPersonalCharacter` varchar(255) DEFAULT NULL,
  `qPersonalIndependence` varchar(128) DEFAULT NULL,
  `qAnthropoHeight` varchar(24) DEFAULT NULL,
  `qAnthropoWeight` varchar(24) DEFAULT NULL,
  `qAnthropoSize` varchar(24) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `crm_questionnaire`
  ADD KEY `ChildID` (`ChildID`);
ALTER TABLE `crm_questionnaire` ADD UNIQUE(`ChildID`);
ALTER TABLE `crm_questionnaire` ADD `Created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `qAnthropoSize`, ADD `Modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `Created`;
