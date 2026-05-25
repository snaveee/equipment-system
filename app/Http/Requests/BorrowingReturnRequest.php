<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BorrowingReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'actual_return_date' => ['required', 'date'],
            'return_condition' => ['required', Rule::in(['new', 'good', 'fair', 'damaged'])],
            'damage_remarks' => ['nullable', 'string', 'max:1000'],
            'follow_up_actions' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
