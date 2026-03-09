<?php

namespace App\UserInterface\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toFilters(): array
    {
        $data = $this->validated();

        return [
            'status' => $data['status'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'date_from' => $data['date_from'] ?? null,
            'date_to' => $data['date_to'] ?? null,
            'per_page' => (int) ($data['per_page'] ?? 15),
        ];
    }
}

