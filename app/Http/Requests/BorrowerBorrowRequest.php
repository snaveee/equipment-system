<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BorrowerBorrowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBorrower() === true;
    }

    public function rules(): array
    {
        return [
            'equipment_id' => ['required', 'exists:equipment,id'],
            'purpose' => ['required', 'string', 'max:500'],
            'borrow_date' => ['required', 'date'],
            'expected_return_date' => ['required', 'date', 'after_or_equal:borrow_date'],
        ];
    }
}
