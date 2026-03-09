<?php

namespace App\UserInterface\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'search' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function toFilters(): array
    {
        $data = $this->validated();

        return [
            'category_id' => $data['category_id'] ?? null,
            'search' => $data['search'] ?? null,
            'per_page' => (int) ($data['per_page'] ?? 15),
            'page' => (int) ($data['page'] ?? 1),
        ];
    }
}

