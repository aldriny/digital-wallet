<?php

namespace App\Repositories;

use App\Models\Bank;
use App\Models\Client;
use App\Models\RawTransaction;

class TransactionRepository
{
    public function saveRawTransactions($transactions, $bankId)
    {
        RawTransaction::create([
            "payload" => json_encode([
                "data" => $transactions,
                "bank_id" => $bankId,
            ]),
        ]);
    }

    public function getBank($bankName)
    {
        return Bank::where("name", $bankName)->first();
    }

    public function getClientId($receiverAccountNumber)
    {
        return Client::where("bank_account_number", $receiverAccountNumber)->first()?->id;
    }
}
