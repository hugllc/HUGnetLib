# _HUGnetLib_

This is a library of PHP code that does all of the functions of the HUGnet system.  The
main part of any PHP code for the project resides here.  This way this code can be
used for any user interface.

## Project Setup

### Directory Structure
This project is broken up into the following directories:

- The test/ directory contains the unit tests
- The build/ directory contains build scripts, and other things useful for building the project
- The deb/ directory contains the base files for the debs
- The src/ This contains the source code

### Requirements
- phpunit (http://www.phpunit.de) with database test extension
- php-mysql extensions
- php-sqlite extensions


### Setup

To function properly, the following must be in place:

1. ./src/php should be installed in the php_include path as ./HUGnetLib/
2. ./src/webapi/html should be installed in the web root as ./HUGnetLib/

On Linux systems, this can be accomplished by symbolic links when working on the code.
For example, in Ubuntu, you could create the following symbolic links:

- /usr/share/php/HUGnetLib -> /your/code/path/HUGnetLib.git/src/php
- /var/www/HUGnetLib -> /your/code/path/HUGnetLib.git/src/webapi/html

## Testing
### Unit Testing
This project uses phpunit (http://phpunit.de) to run unit testing.  There are currently
thousands of tests that run in about 2 minutes.  All of the tests reside in the
test/suite/ directory.  Every file in that directory should stand on its own.
They can all be called separately with phpunit.

#### Running all of the tests
Calling phpunit with no arguments in the root directory of the project will cause all of
the tests to be run.

$ phpunit

#### Running one or more sets of tests
Any directory under test/suite/ can be referenced to only run those tests.  The following
would only test the database code:

$ phpunit test/suite/db


## Deploying

### Ubuntu
Currently there are only build scripts for building .deb files for Ubuntu.  They are
created by running 'ant deb'.  The debs will be in the ./rel directory.


## Troubleshooting & Useful Tools

The unit tests are the best bet for troubleshooting.

Currently E_NOTICE must be set to OFF in report_errors in the php.ini file.

## Contributing changes

_Any contributions need to be tested in the unit testing_

_All unit tests MUST pass for contributions to be even considered_

Changes can be contributed by either:

1. Using git to create patches and emailing them to patches@hugllc.com
2. Creating another github repository to make your changes to and submitting pull requests.

## Filing Bug Reports
The bug tracker for this project is at http://dev.hugllc.com/bugs/ .  If you want an
account on that site, please email prices@hugllc.com.

## License
This is released under the GNU GPL V3.  You can find the complete text in the
LICENSE file, or at http://opensource.org/licenses/gpl-3.0.html