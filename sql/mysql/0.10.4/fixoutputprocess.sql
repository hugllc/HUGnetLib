ALTER TABLE `deviceProcesses` ADD `location` VARCHAR( 128 ) NOT NULL AFTER `type`;
ALTER TABLE `deviceOutputs` ADD `location` VARCHAR( 128 ) NOT NULL AFTER `type`;
