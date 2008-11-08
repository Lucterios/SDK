#!/bin/sh

svn checkout http://projets.lucterios.org/svn/Clients/SDK/ . --username=user --password=user123
if [ $? -ne 0 ]
then
	echo "+++ Error checkout +++"
	exit 1
fi

rm -f *.zip
rm -f *.tar.gz
rm -f *.tar
rm -f *.lpk
rm -f Output.txt

./build_all.sh

cp antBuilderOutput.log bin/Output.txt