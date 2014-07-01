#!/bin/sh

SED=`which sed`

for file in `find . -iname "*.php"|grep -v contrib`; do
    ${SED} -i'' "s/@copyright  [ A-Za-z0-9:.$\-]* Hunt Utilities Group, LLC/@copyright  ${1} Hunt Utilities Group, LLC/g" ${file}
    ${SED} -i'' "s/Copyright (C) [ A-Za-z0-9:.$\-]* Hunt Utilities Group, LLC/Copyright (C) ${1} Hunt Utilities Group, LLC/g" ${file}
done

for file in `find . -iname "*.js"|grep -v contrib`; do
    ${SED} -i'' "s/@copyright  [ A-Za-z0-9:.$\-]* Hunt Utilities Group, LLC/@copyright  ${1} Hunt Utilities Group, LLC/g" ${file}
    ${SED} -i'' "s/Copyright (C) [ A-Za-z0-9:.$\-]* Hunt Utilities Group, LLC/Copyright (C) ${1} Hunt Utilities Group, LLC/g" ${file}
done
