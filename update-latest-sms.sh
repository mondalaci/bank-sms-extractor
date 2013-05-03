#!/bin/bash

# Copy the most up-to-date SMS backup as $destination_sms_backup
# to a directory where your web server can access it.
# Delete the rest of the SMS backups which are obsoleted.

# The SMS backup with the largest file size is considered to be the most
# up-to-date which should be safer than taking the latest SMS backup.

# This is because upon setting up a new phone with no SMSes the latest
# SMS backup will be empty in Dropbox and the earlier valid backups
# would get lost when every backups but the latest one would be deleted.

. `dirname $0`/config.sh  # defines the $destination_sms_backups variable

cd ~/Dropbox/Apps/SMSBackupRestore
largest_to_smallest_sms_backups=`ls -S sms-????-??-??_??-??-??.xml`
is_first=yes
for sms_backup in $largest_to_smallest_sms_backups; do
    if [ $is_first == 'yes' ]; then
        last_sms_backup="$sms_backup"
        is_first=no
    else
        rm "$sms_backup"
    fi
done

cp "$last_sms_backup" "$destination_sms_backup"
