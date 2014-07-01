#!/bin/sh

SED=`which sed`

for file in `find . -iname "*.php"|grep -v contrib`; do
    ${SED} -i'' "s/@version    [ A-Za-z0-9:.$]*/@version    Release: ${1}/g" ${file}
done

for file in `find . -iname "*.js"|grep -v contrib`; do
    ${SED} -i'' "s/@version    [ A-Za-z0-9:.$]*/@version    Release: ${1}/g" ${file}
done
