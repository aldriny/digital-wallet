<?php

namespace App\Services\BankParsers;

use App\DTOs\TransacionDTO;
use App\Services\Interfaces\TransactionParserInterface;

class PayTechBankParser implements TransactionParserInterface
{
    public function parseTransactions($transactions)
    {
        $transactions = explode("\n", $transactions);
        $result = [];
        foreach ($transactions as $transaction) {
            [$dateamount, $reference, $receiver_account_number, $keyValuePart] = explode("#", $transaction);

            $date = substr($dateamount, 0, 8);
            $amount = substr($dateamount, 8);

            $metadata = [];
            $pairs = explode("/", $keyValuePart);
            for ($i = 0; $i < count($pairs); $i += 2) {
                $key = $pairs[$i];
                $value = $pairs[$i + 1];
                $metadata[$key] = $value;
            }

            $result[] = new TransacionDTO(
                date: $date,
                amount: $amount,
                reference_number: $reference,
                receiver_account_number: $receiver_account_number,
                metadata: $metadata
            );
        }
        return $result;
    }
}
