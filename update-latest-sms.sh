#!/bin/bash

# Copy the most up-to-date SMS backup as $destination_sms
# to a directory where your webserver can access it.
. `dirname $0`/config.sh  # defines the $destination_sms variable

cd ~/Dropbox/Apps/SMSBackupRestore
last_sms=`ls -1 sms-????-??-??_??-??-??.xml | tail --lines=1`
cp "$last_sms" "$destination_sms"
