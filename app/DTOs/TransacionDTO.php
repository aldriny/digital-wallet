<?php

namespace App\DTOs;

class TransacionDTO
{
    public string $date;
    public string $amount;
    public string $reference_number;
    public string $receiver_account_number;
    public array $metadata;

    public function __construct($date, $amount, $reference_number, $receiver_account_number, $metadata)
    {
        $this->date = $date;
        $this->amount = $amount;
        $this->reference_number = $reference_number;
        $this->receiver_account_number = $receiver_account_number;
        $this->metadata = $metadata;
    }
}