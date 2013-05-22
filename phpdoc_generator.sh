#!/bin/sh
# phpdoc_generator.sh
# PHPdoc Générateur pour Lucterios API

rm -rf Help/*

TITLE="Lucterios"
PACKAGES="Lucterios"
FILE_PROJECT=$1/CORE/dbcnx.*.php,$1/CORE/XMLparse.*.php,$1/CORE/extensionManager.*.php,$1/CORE/log.*.php,$1/CORE/DB*.inc.php,$1/CORE/xfer*.inc.php

PATH_PROJECT=./tutorials/
PATH_DOCS=./Help
RIC=./GNU_General_Public_License.txt,./LisezMoi

phpdoc -f $FILE_PROJECT -d $PATH_PROJECT -t $PATH_DOCS -ti $TITLE -dn $PACKAGES -o HTML:Smarty:PHP -ric $RIC 

#phpdoc -f $FILE_PROJECT -d $PATH_PROJECT -t $PATH_DOCS -ti $TITLE -dn $PACKAGES -o PDF:default:default -p on -ric $RIC 
