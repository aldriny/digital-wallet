<?php

namespace App\Services;

use App\Jobs\ProcessTransactionsJob;
use App\Repositories\TransactionRepository;
use Exception;
use SimpleXMLElement;

class TransactionService
{
    private $repository;
    public function __construct(TransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getBank($bankName)
    {
        $bank = $this->repository->getBank($bankName);
        if (!$bank) {
            throw new Exception("bank not found", 404);
        }
        return $bank;
    }

    public function validateAuthToken($receivedAuthToken, $bankAuthToken)
    {
        if (!isset($receivedAuthToken) || !hash_equals($bankAuthToken, $receivedAuthToken)) {
            throw new Exception("authentication failed", 401);
        }
    }

    public function handleTransactions($transactions, $bank)
    {
        $this->repository->saveRawTransactions($transactions, $bank->id);
        ProcessTransactionsJob::dispatch($transactions, $bank);
    }

    public function generateXML(array $transaction)
    {
        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><PaymentRequestMessage></PaymentRequestMessage>");

        // TransferInfo
        $transfer = $xml->addChild('TransferInfo');
        $transfer->addChild('Reference', $transaction['reference']);
        $transfer->addChild('Date', $transaction['date']);
        $transfer->addChild('Amount', number_format($transaction['amount'], 2, '.', ''));
        $transfer->addChild('Currency', strtoupper($transaction['currency']));

        // SenderInfo
        $sender = $xml->addChild('SenderInfo');
        $sender->addChild('AccountNumber', $transaction['sender_account_number']);

        // ReceiverInfo
        $receiver = $xml->addChild('ReceiverInfo');
        $receiver->addChild('BankCode', $transaction['receiver_bank_code']);
        $receiver->addChild('AccountNumber', $transaction['receiver_account_number']);
        $receiver->addChild('BeneficiaryName', $transaction['receiver_name']);

        // Notes
        if (!empty($transaction['notes'])) {
            $notes = $xml->addChild('Notes');
            foreach ($transaction['notes'] as $note) {
                $notes->addChild('Note', $note);
            }
        }

        // PaymentType
        if (isset($transaction['payment_type']) && $transaction['payment_type'] != 99) {
            $xml->addChild('PaymentType', $transaction['payment_type']);
        }

        // ChargeDetails
        if (isset($transaction['charge_details']) && strtoupper($transaction['charge_details']) != 'SHA') {
            $xml->addChild('ChargeDetails', $transaction['charge_details']);
        }

        return $xml->asXML();
    }
}
