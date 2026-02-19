<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reference' => 'required|string',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            'sender_account_number' => 'required|string',
            'receiver_bank_code' => 'required|string',
            'receiver_account_number' => 'required|string',
            'receiver_name' => 'required|string',
            'notes' => 'nullable|array',
            'notes.*' => 'string',
            'payment_type' => 'nullable|integer',
            'charge_details' => 'nullable|string',
        ];
    }
}
