PHPUNIT=`which phpunit`
PHPDOC=`which phpdoc`
DOXYGEN=`which doxygen`
PHPCS=`which phpcs`
BASE_DIR=${PWD}/
GIT=`which git`

test: clean doc-clean Documentation/test
	cd test; ${PHPUNIT} --coverage-html ${BASE_DIR}Documentation/test/codecoverage/ \
                --log-junit ${BASE_DIR}Documentation/test/log.xml \
                --testdox-html ${BASE_DIR}Documentation/test/testdox.html \
                .| tee ${BASE_DIR}Documentation/test/testoutput.txt

Documentation/test:
	mkdir -p ${BASE_DIR}Documentation/test

doc:
	rm -Rf ${BASE_DIR}Documentation
	mkdir -p ${BASE_DIR}Documentation
	${PHPDOC} -c phpdoc.ini  | tee ${BASE_DIR}Documentation/build.txt

doc-clean:
	rm -Rf ${BASE_DIR}Documentation/test

clean: 
	rm -Rf *~ */*~ */*/*~ */*/*/*~


style:
	mkdir -p ${BASE_DIR}Documentation
	${PHPCS} --standard=PHPCS --report=full --standard=Pear --ignore="Documentation/,JoomlaMock/,tmpl/" .


setup:
	sudo apt-get install php-pear php5-xdebug
	if [ "${PHPUNIT}x" = "x" ]; then \
		sudo pear channel-discover pear.phpunit.de; \
		sudo pear channel-discover pear.symfony-project.com; \
		sudo pear install --alldeps phpunit/PHPUnit; \
	fi;
	if [ "${PHPCS}x" = "x" ]; then \
		sudo pear install --alldeps PHP_CodeSniffer \
	fi;
