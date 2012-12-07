#!/bin/sh

SED=`which sed`



for file in `find . -iname "*.php"|grep -v contrib`; do
    ${SED} -i'' "s/${1}/${2}/g" ${file}
done
