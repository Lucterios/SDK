#!/bin/bash

TARGET_DIR=$1
APPLI_NAME=$2
if [ "$APPLI_NAME"=="" ]
then
  APPLI_NAME='ASSO'
fi
DEPLOYED_DIR=$PWD/depoyed

function extract {
    extname=$1
    target=$2
    cd $target
    ext_name=`ls $DEPLOYED_DIR/*.lpk | grep $extname`
    if [ -f "$ext_name" ]
    then
	echo 
	echo "++[Extract $ext_name in $PWD]++"
	tar -xzpf $ext_name
    else
	echo 
	echo "### No found $extname! ###"
    fi
    cd -
}


if [ -d "$TARGET_DIR" -a -d "$DEPLOYED_DIR" ]
then
    echo "--- Deployed $DEPLOYED_DIR to $TARGET_DIR ---"
    cd $TARGET_DIR
    extract "CORE" $PWD
    
    ext=`ls extensions`
    for extension in $ext
    do
      if [ "$extension" == "applis" ]
      then
	  extract "$APPLI_NAME" "extensions/$extension"
      else
	  extract "$extension" "extensions/$extension"
      fi
    done
    cd -
else
    echo "Bad parameters!!"
fi

