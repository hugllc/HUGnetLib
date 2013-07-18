ALTER TABLE `deviceInputs` ADD `tableEntry` TEXT NOT NULL AFTER `calibration`;
ALTER TABLE `deviceOutputs` ADD `tableEntry` TEXT NOT NULL AFTER `driver`;
ALTER TABLE `deviceProcesses` ADD `tableEntry` TEXT NOT NULL AFTER `driver`;
