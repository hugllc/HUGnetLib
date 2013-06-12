PHPUNIT=`which phpunit`
PHPDOC=`which phpdoc`
DOXYGEN=`which doxygen`
PHPCS=`which phpcs`
BASE_DIR=${PWD}/
GIT=`which git`

all:

setup:
	sudo apt-get install php-pear npm phantomjs php5-sqlite php5-mysql
	#sudo npm install jshint -g
	sudo pear update-channels
	sudo pear config-set auto_discover 1
	sudo pear install pear.phpqatools.org/phpqatools pear.netpirates.net/phpDox
	sudo pear install phpunit/DbUnit

build-setup: node_modules/jasmine-node node_modules/jsdom node_modules/jquery node_modules/backbone node_modules/underscore


node_modules/underscore:
	npm install underscore

node_modules/jsdom:
	npm install jsdom

node_modules/jquery:
	npm install jquery

node_modules/backbone:
	npm install backbone

node_modules/jasmine-node:
	npm install jasmine-node

clean:
	find . -iname "*~" -exec rm -f {} \;
