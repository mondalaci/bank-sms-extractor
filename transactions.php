<?php
@include __DIR__ . '/card-numbers-to-owners.php';

function get_transactions()
{
    global $card_number_to_owner;

    $results = array();
    $smses = simplexml_load_file('sms.xml');

    foreach ($smses as $sms) {
        if (@$_GET['dump'] == 'true') {
            print '<pre>';
            var_dump($sms);
            print '</pre>';
        }

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
                              'Kàrtyaszàm: ...([0-9]{4})(.*) - OTPdirekt/U',
                              $body, $matches))
        {
            $is_transaction_successful = true;
            list($body, $timestamp, $subject, $amount, $partner, $card_number, $balance) = $matches;
            $type = 'credit card transfer';

            $recipient_string = '; Elfogadò: ';
            if (strstr($partner, $recipient_string) !== false) {
                $partner = str_replace($recipient_string, ' [', $partner) . ']';
            }

            foreach (array('Egyenleg', 'Egy.') as $balance_string) {
                $balance = str_replace("; $balance_string: ", "", $balance);
            }
        } else if (preg_match('/OTPdirekt - .* Jòvàhagyàs [0-9]{2}:[0-9]{2}-ig./', $body, $matches)) {
            $type = 'confirmation';
        } else {
            $type = 'unknown';
        }

        if (!$is_transaction_successful) {
            continue;
        }

        $is_transaction_successful = $subject != 'SIKERTELEN Kàrtyàs vàsàrlàs/zàrolàs';
        $card_owner = @array_key_exists($card_number, $card_number_to_owner)
                          ? $card_number_to_owner[$card_number]
                          : sprintf('Anonymous [%s]', $card_number);
        $extended_comment = $subject . ($comment ? ": <i>$comment</i>" : "");

        $results[] = array(
            'body' => $sms['body'],
            'date' => $sms['date'],
            'is_transaction_successful' => $is_transaction_successful,
            'datestamp' => strftime('%F %T', $sms['date']/1000),
            'card_number' => $card_number,
            'card_owner' => $card_owner,
            'partner' => $partner,
            'subject' => $subject,
            'comment' => $comment,
            'extended_comment' => $extended_comment,
            'amount' => $amount,
            'balance' => $balance,
        );
    }

    return $results;
}
