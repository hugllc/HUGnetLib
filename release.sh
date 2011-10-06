#!/bin/bash
GIT_SERVER=git://git.hugllc.com

VERSION=$1

if [ "x${VERSION}" == "x" ]; then
    echo "Usage:  ${0} <release version>"
    exit;
fi

source setversion.sh ${VERSION}

git commit -a -m "Release ${VERSION}"

echo Tagging the version
git tag -m "Release ${VERSION}" -s v${VERSION} HEAD
git push --tags
