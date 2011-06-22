#!/bin/sh
# phpdoc_generator.sh
# PHPdoc G��ateur pour Lucterios API

rm -rf Help/*

TITLE="Lucterios"
PACKAGES="Lucterios"
FILE_PROJECT=$1/CORE/DB*.inc.php,$1/CORE/xfer*.inc.php,$1/CORE/dbcnx.inc.php
PATH_PROJECT=./tutorials/
PATH_DOCS=./Help
RIC=./GNU_General_Public_License.txt,./LisezMoi

phpdoc -f $FILE_PROJECT -d $PATH_PROJECT -t $PATH_DOCS -ti $TITLE -dn $PACKAGES -o HTML:frames:phpedit -ric $RIC 

#phpdoc -f $FILE_PROJECT -d $PATH_PROJECT -t $PATH_DOCS -ti $TITLE -dn $PACKAGES -o PDF:default:default -p on -ric $RIC 



