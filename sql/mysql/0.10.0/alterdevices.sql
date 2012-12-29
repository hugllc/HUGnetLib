ALTER TABLE `devices` CHANGE `channels` `dataChannels` LONGTEXT NOT NULL;
ALTER TABLE `devices` ADD `controlChannels` LONGTEXT NOT NULL AFTER `dataChannels`;
