ALTER TABLE `user` 
ADD COLUMN `InManagerStat` TINYINT(2) NOT NULL DEFAULT 1 COMMENT '' AFTER `LastIP`;
