#!/bin/sh
SVN_SERVER=https://svn.hugllc.com

VERSION=$1

if [ x$VERSION == "x" ]; then
    echo "Usage:  $0 <release version>"
    exit;
fi

svn commit .
echo Tagging the version
svn -m "Release $VERSION" copy ./$COM_NAME ${SVN_SERVER}/0039/tags/24/${VERSION}
