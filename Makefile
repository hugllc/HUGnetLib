PHPUNIT=`which phpunit`
PHPDOC=`which phpdoc`
DOXYGEN=`which doxygen`
PHPCS=`which phpcs`
BASE_DIR=${PWD}/
GIT=`which git`

all:

setup-ubuntu: 
	sudo apt-get install php-pear php-sqlite3 php-mysql php-curl npm nodejs php-xsl php-curl
	sudo apt-get install nodejs-legacy
	sudo apt-get install mongodb php-mongo

setup: bin bin/composer build-setup
	./bin/composer update
	./bin/composer install
	
	
build-setup: bin/phantomjs bin/jsdoc bin/jslint bin/jasmine-node node_modules/jsdom node_modules/jquery node_modules/backbone node_modules/underscore bin/jshint

bin:
	mkdir -p $(BASE_DIR)bin

bin/phpunit:
	wget https://phar.phpunit.de/phpunit.phar
	chmod +x phpunit.phar
	mv phpunit.phar $(BASE_DIR)bin/phpunit

bin/phploc:
	wget https://phar.phpunit.de/phploc.phar
	chmod +x phploc.phar
	mv phploc.phar $(BASE_DIR)bin/phploc

bin/phpcs:
	wget https://github.com/squizlabs/PHP_CodeSniffer/releases/download/2.0.0a2/phpcs.phar
	chmod +x phpcs.phar
	mv phpcs.phar $(BASE_DIR)bin/phpcs
	
bin/hhvm-wrapper:
	wget https://phar.phpunit.de/hhvm-wrapper.phar
	chmod +x hhvm-wrapper.phar
	mv hhvm-wrapper.phar $(BASE_DIR)bin/hhvm-wrapper

bin/phpcpd:
	wget https://phar.phpunit.de/phpcpd.phar
	chmod +x phpcpd.phar
	mv phpcpd.phar $(BASE_DIR)bin/phpcpd

bin/phpdox:
	wget http://phpdox.de/releases/phpdox.phar
	chmod +x phpdox.phar
	mv phpdox.phar $(BASE_DIR)bin/phpdox

bin/behat:
	wget https://github.com/downloads/Behat/Behat/behat.phar
	chmod +x behat.phar
	mv behat.phar $(BASE_DIR)bin/behat
	
bin/phpdcd:
	wget https://phar.phpunit.de/phpdcd.phar
	chmod +x phpdcd.phar
	mv phpdcd.phar $(BASE_DIR)bin/phpdcd
	
bin/composer:
	curl -sS https://getcomposer.org/installer | php -- --install-dir=bin
	mv $(BASE_DIR)bin/composer.phar $(BASE_DIR)bin/composer
	
node_modules/underscore: bin
	npm install underscore

node_modules/jsdom: bin
	npm install jsdom

node_modules/jquery: bin
	npm install jquery

node_modules/backbone: bin
	npm install backbone

bin/jasmine-node: bin
	rm -f bin/jasmine-node
	npm install jasmine-node
	ln -s ../node_modules/jasmine-node/bin/jasmine-node ./bin/jasmine-node
	
bin/jshint:
	rm -f bin/jshint
	npm install jshint
	ln -s ../node_modules/jshint/bin/jshint ./bin/jshint

bin/jsdoc:
	rm -f bin/jsdoc
	npm install jsdoc
	ln -s ../node_modules/jsdoc/jsdoc.js ./bin/jsdoc

bin/jslint:
	rm -f bin/jslint
	npm install jslint
	ln -s ../node_modules/jslint/bin/jslint.js ./bin/jslint

bin/phantomjs:
	rm -f bin/phantomjs
	npm install phantomjs
	ln -s ../node_modules/phantomjs/bin/phantomjs ./bin/phantomjs

clean:
	find . -iname "*~" -exec rm -f {} \;
	
dist-clean: clean
	rm -Rf composer bin composer.lock autoload.php pear tmp
