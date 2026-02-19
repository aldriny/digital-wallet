<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\Transaction;
use App\Factories\BankParserFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessTransactionsJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    private $transactions;
    private $bank;
    private $bankParser;

    /**
     * Create a new job instance.
     */
    public function __construct($transactions, $bank)
    {
        $this->transactions = $transactions;
        $this->bank = $bank;
    }

    /**
     * Execute the job.
     */
    public function handle(BankParserFactory $factory): void
    {
        $bankParser = $factory->getBankParser($this->bank->name);
        $transactions = $bankParser->parseTransactions($this->transactions);

        $transactionsData = [];
        foreach ($transactions as $transaction) {

            $client = Client::where("bank_account_number", $transaction["receiver_account_number"])->first()?->id;
            $transactionData = [
                "bank_id" => $this->bank->id,
                "reference_number" => $transaction["reference_number"],
                "date" => $transaction["date"],
                "amount" => $transaction["amount"],
                "metadata" => isset($transaction["metadata"]) ? json_encode($transaction["metadata"]) : null,
                "created_at" => now(),
                "updated_at" => now()
            ];

            if (!$client) {
                Log::channel("transactions")->error("client not found for the transaction: ", [
                    "reference number" => $transaction["reference_number"],
                    "receiver_account_number" => $transaction["receiver_account_number"],
                ]);
                $transactionData["client_id"] = null;
                $transactionData["status"] = "failed";
            } else {
                $transactionData["client_id"] = $client;
                $transactionData["status"] = "success";
            }

            $transactionsData[] = $transactionData;
        }

        Transaction::insertOrIgnore($transactionsData);
    }
}
