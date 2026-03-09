<?php

namespace App\UserInterface\Requests;

use App\DTOs\OrderStatusUpdateData;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class OrderStatusUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:' . implode(',', Order::STATUSES)],
        ];
    }

    public function toDto(): OrderStatusUpdateData
    {
        return new OrderStatusUpdateData(
            status: $this->validated('status'),
        );
    }
}
