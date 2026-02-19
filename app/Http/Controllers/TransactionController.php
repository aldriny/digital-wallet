<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendTransactionRequest;
use App\Services\TransactionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    private $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    public function getTransactions(Request $request)
    {
        try {
            // get request headers
            $bankName = $request->header("bank-name");
            $receivedAuthToken = $request->header("auth-token");

            $bank = $this->service->getBank($bankName);
            $this->service->validateAuthToken($receivedAuthToken, $bank->auth_token);
            $this->service->handleTransactions($request->transactions, $bank);
            return response()->json([
                'status' => 'success',
                "message" => 'payload saved successfuly',
            ], 201);
        } catch (Exception $e) {
            Log::channel("transactions")->error($e->getMessage());
            return response()->json([
                'status' => 'failed',
                "message" => $e->getMessage(),
            ], $e->getCode());
        }
    }
    
    public function sendTransaction(SendTransactionRequest $request)
    {
        try {
            $data = $request->validated();
            $xml = $this->service->generateXML($data);
            return response($xml, 200)->header("Content-Type", "application/xml");
        } catch (Exception $e) {
            Log::channel("transactions")->error($e->getMessage());
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }
}
