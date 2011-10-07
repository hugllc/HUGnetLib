#!/bin/bash

ZIP=`which zip`
TAR=`which tar`

cd ${ROOT_DIR}/rel/${COM_NAME}
cp -Rvp LICENSE* src/
cp -Rvp README* src/
mv src HUGnetLib-${COM_VERSION}
${ZIP} -r ${ROOT_DIR}/rel/${COM_NAME}-${COM_VERSION}.zip HUGnetLib-${COM_VERSION}
${TAR} -cvzf ${ROOT_DIR}/rel/${COM_NAME}-${COM_VERSION}.tar.gz HUGnetLib-${COM_VERSION}
${TAR} -cvjf ${ROOT_DIR}/rel/${COM_NAME}-${COM_VERSION}.tar.bz2 HUGnetLib-${COM_VERSION}
mv HUGnetLib-${COM_VERSION} src
