PHPUNIT=`which phpunit`
PHPDOC=`which phpdoc`
DOXYGEN=`which doxygen`
PHPCS=`which phpcs`
BASE_DIR=${PWD}/
GIT=`which git`

all: test style

test: clean doc-clean Documentation/test
	cd test; ${PHPUNIT} --coverage-html ${BASE_DIR}Documentation/test/codecoverage/ \
                --log-junit ${BASE_DIR}Documentation/test/log.xml --syntax-check\
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
	${PHPCS} --standard=PHPCS --report=full --standard=Pear --ignore="Documentation/,JoomlaMock/,tmpl/,old/,contrib/" .


setup:
	-sudo pear channel-discover pear.pdepend.org
	-sudo pear channel-discover pear.phpmd.org
	-sudo pear channel-discover pear.phpunit.de
	-sudo pear channel-discover components.ez.no
	-sudo pear channel-discover pear.symfony-project.com

	-sudo pear install pdepend/PHP_Depend
	-sudo pear install phpmd/PHP_PMD
	-sudo pear install phpunit/phpcpd
	-sudo pear install phpunit/phploc
	-sudo pear install PHPDocumentor
	-sudo pear install PHP_CodeSniffer
	-sudo pear install --alldeps phpunit/PHP_CodeBrowser
	-sudo pear install --alldeps phpunit/PHPUnit
