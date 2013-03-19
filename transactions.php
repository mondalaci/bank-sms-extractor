<?php
@include __DIR__ . '/card-numbers-to-owners.php';

function get_transactions()
{
    global $card_number_to_owner;

    $otpdirekt_phone_number = '06709400700';

    $results = array();
    $smses = simplexml_load_file('sms.xml');

    foreach ($smses as $sms) {
        if (@$_GET['dump'] == 'true') {
            print '<pre>';
            var_dump($sms);
            print '</pre>';
        }

        if ($sms['address'] != $otpdirekt_phone_number) {
            continue;
        }

        $body = (string)$sms['body'];
        $matches = array($body);

        $balance = $comment = '';
        $partner = 'OTP';

        if (preg_match('/\.{3}([0-9]{4}) Szàmla \(([0-9]{6})\) (.+):(.+); (.+); OTPdirekt/U',
                       $body, $matches))
        {
            $type = 'bank account transfer';
            list($body, $card_number, $day, $subject, $amount, $payload) = $matches;

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

        if ($type == 'confirmation') {
            continue;
        }

        $is_transaction_successful = @$subject != 'SIKERTELEN Kàrtyàs vàsàrlàs/zàrolàs' && $type != 'unknown';
        $card_owner = @array_key_exists($card_number, $card_number_to_owner)
                          ? $card_number_to_owner[@$card_number]
                          : sprintf('Anonymous [%s]', @$card_number);

        $results[] = array(
            'type' => $type,
            'body' => $sms['body'],
            'date' => $sms['date'],
            'is_transaction_successful' => $is_transaction_successful,
            'card_number' => @$card_number,
            'card_owner' => $card_owner,
            'partner' => $partner,
            'subject' => @$subject,
            'comment' => $comment,
            'amount' => @$amount,
            'balance' => $balance,
        );
    }

    return $results;
}
