ALTER TABLE `deviceInputs` ADD `tableEntry` TEXT NOT NULL AFTER `calibration`;
ALTER TABLE `deviceOutputs` ADD `tableEntry` TEXT NOT NULL AFTER `driver`;
ALTER TABLE `deviceProcesses` ADD `tableEntry` TEXT NOT NULL AFTER `driver`;
UPDATE inputTable` SET `arch`= "0039-37" WHERE `arch` = "ADuC";
ALTER TABLE `devices` ADD `Publish` TINYINT NOT NULL DEFAULT '1' AFTER `Active`;