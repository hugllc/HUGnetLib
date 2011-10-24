PHPUNIT=`which phpunit`
PHPDOC=`which phpdoc`
DOXYGEN=`which doxygen`
PHPCS=`which phpcs`
BASE_DIR=${PWD}/
GIT=`which git`

all:

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
