PHPUNIT=`which phpunit`
PHPDOC=`which phpdoc`
DOXYGEN=`which doxygen`
PHPCS=`which phpcs`
BASE_DIR=${PWD}/
GIT=`which git`

all:

setup:
	sudo apt-get install php-pear npm nodejs
	sudo pear update
	sudo pear config-set auto_discover 1
	sudo pear install pear.phpqatools.org/phpqatools pear.netpirates.net/phpDox
	sudo npm install jshint -g

build-setup:
	npm install jasmine-node
	npm install jsdom
	npm install jquery
	npm install backbone
