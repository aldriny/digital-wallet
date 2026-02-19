<?php

namespace App\Factories;

use App\Services\BankParsers\AcmeBankParser;
use App\Services\BankParsers\PayTechBankParser;
use RuntimeException;

class BankParserFactory
{
    public function getBankParser($bankName)
    {
        $result = match ($bankName) {
            "Acme Bank" => new AcmeBankParser(),
            "PayTech Bank" => new PayTechBankParser(),
            default => throw new RuntimeException("bank not found", 404),
        };
        return $result;
    }
}
