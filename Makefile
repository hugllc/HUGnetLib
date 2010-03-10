PHPUNIT=`which phpunit`
PHPDOC=`which phpdoc`
DOXYGEN=`which doxygen`
PHPCS=`which phpcs`
BASE_DIR=${PWD}/
GIT=`which git`

test: clean Documentation/test
	${PHPUNIT} --coverage-html ${BASE_DIR}Documentation/test/codecoverage/ \
                --log-junit ${BASE_DIR}Documentation/test/log.xml \
                --testdox-html ${BASE_DIR}Documentation/test/testdox.html \
                test/ | tee ${BASE_DIR}Documentation/test/testoutput.txt

Documentation/test:
	mkdir -p ${BASE_DIR}Documentation/test

doc:
	rm -Rf ${BASE_DIR}Documentation
	mkdir -p ${BASE_DIR}Documentation
	${PHPDOC} -c phpdoc.ini  | tee ${BASE_DIR}Documentation/build.txt

clean:
	rm -Rf ${BASE_DIR}Documentation/test


style:
	mkdir -p ${BASE_DIR}Documentation
	${PHPCS} --standard=PHPCS --report=full --standard=Pear --ignore="Documentation/,JoomlaMock/,tmpl/" .

