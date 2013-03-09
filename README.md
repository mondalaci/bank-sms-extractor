bank-sms-extractor
==================

A script to extract and display transaction information out of SMS messages of Hungarian banks.  Currently only OTP bank is supported.

1) Install the [SMS Backup & Restore](https://play.google.com/store/apps/details?id=com.riteshsahu.SMSBackupRestore) Android app along with its [addon](https://play.google.com/store/apps/details?id=com.riteshsahu.SMSBackupRestoreNetworkAddon) then make it back up your SMS-es to your Dropbox account on a regular basis.

2) Create your config.sh out of config.sh.sample and include update-latest-sms.sh to your crontab.  (This is needed because currently the archive mode of the app doesn't work reliable so we've got to grab the latest archive into a webserer-visible directory.

3) Hit list-transactions.php from your web browser.
