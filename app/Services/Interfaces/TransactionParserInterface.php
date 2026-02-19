<?php

namespace App\Services\Interfaces;

interface TransactionParserInterface
{
    public function parseTransactions($transactions);
}
