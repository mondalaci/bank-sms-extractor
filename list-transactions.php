<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<link rel="stylesheet" href="transactions.css">
</head>
<body>
<table>
<?php
@include __DIR__ . '/transactions.php';

$column_count = 7;

$transactions = get_transactions();

foreach ($transactions as $transaction) {

    $second_to_last_cell_content = $transaction['type'] == 'unknown'
            ? sprintf("<td colspan='%d'>%s</td>",
                      $column_count, $transaction['body']
              )
            : sprintf("<td title='%s' class='no-wrap'>%s</td>" .
                      "<td title='%s' class='no-wrap'>%s</td>" .
                      "<td>%s</td>" .
                      "<td>%s</td>" .
                      "<td class='no-wrap numeric'>%s</td>" .
                      "<td class='no-wrap numeric gray'>%s</td>",
                      htmlspecialchars($transaction['body']), $transaction['datestamp'],  // body title and date stamp content
                      $transaction['card_number'], $transaction['card_owner'],
                      $transaction['partner'],
                      $transaction['extended_comment'],
                      $transaction['amount'],
                      $transaction['balance']
              );

    printf("<tr>" .
           "<td><input type='checkbox' data-utime='%s' data-success='%s'></td>" .
           "%s" .
           "</tr>\n",
           $transaction['date'], $transaction['is_transaction_successful'] ? 'true' : 'false',  // utime in microseconds
           $second_to_last_cell_content
    );
}

?>
</table>
<input type='button' id='reload' value='Reload'>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="list-transactions.js"></script>
</body>
</html>
