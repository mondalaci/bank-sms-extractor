<meta charset='utf-8'>
<table border=1>
<?php
$smses = simplexml_load_file('sms.xml');

foreach ($smses as $sms) {
    if ($sms['contact_name'] != 'OTP Bank') {
        continue;
    }

    $body = (string)$sms['body'];
    $matches = array($body);

    $is_transaction_successful = false;
    $balance = $comment = '';
    $partner = 'OTP';

    if (preg_match('/\.{3}([0-9]{4}) Szàmla \(([0-9]{6})\) (.+):(.+); (.+); OTPdirekt/U',
                   $body, $matches))
    {
        $is_transaction_successful = true;
        $type = 'bank account transfer';
        list($body, $card_number, $day, $subject, $amount, $payload) = $matches;

        $unknown_values = array();

        $fields = explode('; ', $payload);
        foreach ($fields as $field) {
            $key_and_value = explode(':', $field, 2);

            if (count($key_and_value) == 1) {
                $comment .= '; ' . $key_and_value[0];
            } else {
                list($key, $value) = $key_and_value;

                switch ($key) {
                case 'Egy':
                case 'Egyenleg':
                    $balance = $value;
                    break;
                case 'Partner':
                    $partner = $value;
                    break;
                case 'Közl':
                    $comment .= $value;
                    break;
                default:
                    $comment .= "; $value";
                    break;
                }
            }
        }
    } else if (preg_match('/([0-9]{6} [0-9]{2}:[0-9]{2}) (.+): (.*); (.*); ' .
                          'Kàrtyaszàm: ...([0-9]{4})(?:; Egyenleg:(.*)) - OTPdirekt/U',
                          $body, $matches))
    {
        $is_transaction_successful = true;
        list($body, $timestamp, $subject, $amount, $partner, $card_number, $balance) = $matches;
        $type = 'credit card transfer';
    } else if (preg_match('/OTPdirekt - .* Jòvàhagyàs [0-9]{2}:[0-9]{2}-ig./', $body, $matches)) {
        $type = 'confirmation';
    } else {
        $type = 'unknown';
    }

    if (!$is_transaction_successful) {
        continue;
    }

    print '<tr><td title="'.htmlspecialchars($body).'">' . join('</td><td>',
          array($sms['readable_date'], $card_number, $partner, $subject, $amount, $comment, $balance)) .
          '</td></tr>';
}

?>
</table>