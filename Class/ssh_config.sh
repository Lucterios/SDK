#!/bin/sh

ssh_rsa=$HOME"/.ssh/id_rsa"
ssh_pub=$ssh_rsa".pub"

if [ ! -f "$ssh_pub" ]
then
    ssh-keygen -f $ssh_rsa -N ''
fi

if [ -f "$ssh_pub" ]
then
    cat $ssh_pub
fi
