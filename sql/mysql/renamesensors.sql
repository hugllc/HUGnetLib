RENAME TABLE `HUGnet`.`sensors` TO `HUGnet`.`deviceInputs` ;
ALTER TABLE `deviceInputs` CHANGE `sensor` `input` INT( 11 ) NOT NULL;
ALTER TABLE `deviceInputs` DROP `dataType`, DROP `units`, DROP `decimals`;
