#!/bin/bash
GIT_SERVER=git://git.hugllc.com

VERSION=$1

if [ x${VERSION} == "x" ]; then
    echo "Usage:  ${0} <release version>"
    exit;
fi

git commit -a
echo Tagging the version
#svn -m "Release $VERSION" copy ./$COM_NAME ${SVN_SERVER}/HUGnet/tags/HUGnetLib/${VERSION}
git tag -m "Release ${VERSION}" -s ${VERSION}
git push
