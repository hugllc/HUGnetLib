RENAME TABLE `sensors` TO `deviceInputs` ;
ALTER TABLE `deviceInputs` CHANGE `sensor` `input` INT( 11 ) NOT NULL;
ALTER TABLE `deviceInputs` DROP `dataType`, DROP `units`, DROP `decimals`;
ALTER TABLE `deviceInputs` ADD `calibration` TEXT NOT NULL AFTER `driver`;
