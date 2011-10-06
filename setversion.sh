#!/bin/sh

SED=`which sed`

for file in `find . -iname "*.php"`; do
    ${SED} --in-place "s/@version    [ A-Za-z0-9:.$]*/@version    Release: ${1}/g" ${file}
done
