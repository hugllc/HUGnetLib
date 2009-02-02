#!/bin/bash
GIT_SERVER=git://git.hugllc.com

VERSION=$1

if [ "x${VERSION}" == "x" ]; then
    echo "Usage:  ${0} <release version>"
    exit;
fi

git commit -a
echo Tagging the version
git tag -m "Release ${VERSION}" -s v${VERSION} HEAD
git push --tags
