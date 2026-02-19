<?php

namespace App\Services\BankParsers;

use App\DTOs\TransacionDTO;
use App\Services\Interfaces\TransactionParserInterface;
use Illuminate\Support\Facades\Log;

class AcmeBankParser implements TransactionParserInterface
{
    public function parseTransactions($transactions)
    {
        $transactions = explode("\n", $transactions);
        $result = [];
        foreach ($transactions as $transaction) {
            [$amount, $reference_number, $date, $receiver_account_number] = explode("//", $transaction);

            $result[] = new TransacionDTO(
                date: $date,
                amount: $amount,
                reference_number: $reference_number,
                receiver_account_number: $receiver_account_number,
                metadata: []
            );
        }
        return $result;
    }
}
