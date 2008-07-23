#!/bin/sh

svn checkout http://projets.lucterios.org/svn/Clients/SDK/ . --username=user --password=user123

rm -f *.zip
rm -f *.tar.gz
rm -f *.tar
rm -f *.lpk
rm -f Output.txt

VersionMaj=0
VersionMin=15
VersionRev=2
current_date_sec=`date +%s`
VersionBuild=$(( (current_date_sec-1214866800)/7200 ))

clear

echo "*** Compilation $VersionMaj.$VersionMin.$VersionRev.$VersionBuild ***"
echo $VersionMaj $VersionMin $VersionRev $VersionBuild > version.txt

rm -f setup.inc.php
cp template_setup.inc.php setup.tmp
sed -e "s/\$version_max=X;/\$version_max=$VersionMaj;/" setup.tmp > setup.inc.php
cp setup.inc.php setup.tmp
sed -e "s/\$version_min=X;/\$version_min=$VersionMin;/" setup.tmp > setup.inc.php
cp setup.inc.php setup.tmp
sed -e "s/\$version_release=X;/\$version_release=$VersionRev;/" setup.tmp > setup.inc.php
cp setup.inc.php setup.tmp
sed -e "s/\$version_build=X;/\$version_build=$VersionBuild;/" setup.tmp > setup.inc.php

ArcFileName=JDK_$VersionMaj-$VersionMin-$VersionRev-$VersionBuild

tar --exclude=.svn -cf $ArcFileName.tar Actions Backup/readme Class CNX/readme Help images Main tutorials ConnectionSDK.inc.php coreIndex.php DeleteModule.php FunctionTool.inc.php GNU_General_Public_License.txt Help.php index.php PathInitial.inc.php ReloadModule.php setup.inc.php
if [ $? -ne 0 ]
then
	echo "+++ Error lpk +++"
	exit 2
fi
cp $ArcFileName.tar JDK
gzip -f -S .lpk JDK
if [ $? -ne 0 ]
then
	echo "+++ Error lpk +++"
	exit 2
fi

echo "--- Compilation $VersionMaj.$VersionMin.$VersionRev.$VersionBuild Success ---"

cp antBuilderOutput.log Output.txt