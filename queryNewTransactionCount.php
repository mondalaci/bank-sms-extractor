<?php
require __DIR__ . '/transactions.php';

$last_utime = $_GET['last_utime'];
$transactions = get_transactions();

$new_transaction_count = 0;
foreach ($transactions as $transaction) {
    if ($transaction['date'] > $last_utime) {
        $new_transaction_count++;
    }
}

print $new_transaction_count;
