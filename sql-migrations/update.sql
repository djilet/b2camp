ALTER TABLE `crm_staff` ADD `UserID` INT(10) UNSIGNED NOT NULL AFTER `Archive`, ADD INDEX (`UserID`);

CREATE TABLE `crm_status2task` ( `HistoryStatusID` INT(11) UNSIGNED NOT NULL , `TaskID` INT(11) UNSIGNED NOT NULL , INDEX (`HistoryStatusID`), INDEX (`TaskID`)) ENGINE = InnoDB;

ALTER TABLE `crm_status2task` ADD `entity` ENUM('child','school','legal') NOT NULL AFTER `TaskID`;

ALTER TABLE `user` CHANGE `Role` `Role` ENUM('integrator','administrator','manager','guide','servicestaff') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'guide';