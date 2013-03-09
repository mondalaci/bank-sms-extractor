bank-sms-extractor
==================

A script to extract and display your transaction records out of your SMS messages sent by Hungarian banks.  Currently only OTP bank is supported.

1. Install the [SMS Backup & Restore](https://play.google.com/store/apps/details?id=com.riteshsahu.SMSBackupRestore) Android app along with its [addon](https://play.google.com/store/apps/details?id=com.riteshsahu.SMSBackupRestoreNetworkAddon) then make it back up your SMS-es to your Dropbox account on a regular basis.
2. Customize `config.sh.sample` as `config.sh` and include `update-latest-sms.sh` into your crontab.  (This is needed because currently the archive mode of the app doesn't work reliably so we've got to grab the latest archive and put it into a webserver-visible directory as a fixed filename.
3. Hit `list-transactions.php` from your web browser.
