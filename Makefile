PHPUNIT=`which phpunit`
PHPDOC=`which phpdoc`
DOXYGEN=`which doxygen`
PHPCS=`which phpcs`
BASE_DIR=${PWD}/
GIT=`which git`

test: clean Documentation/test
	cd test; ${PHPUNIT} --report ${BASE_DIR}Documentation/test/codecoverage/ \
                --log-xml ${BASE_DIR}Documentation/test/log.xml \
                --testdox-html ${BASE_DIR}Documentation/test/testdox.html \
                --log-pmd ${BASE_DIR}Documentation/test/pmd.xml \
                --log-metrics ${BASE_DIR}Documentation/test/metrics.xml \
                HUGnetLibTests | tee ${BASE_DIR}Documentation/test/testoutput.txt

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

