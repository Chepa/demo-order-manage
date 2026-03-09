<?php

namespace App\UserInterface\Requests;

use App\DTOs\CreateOrderData;
use App\DTOs\OrderItemData;
use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function toDto(): CreateOrderData
    {
        $items = array_map(
            fn (array $item) => new OrderItemData(
                productId: (int) $item['product_id'],
                quantity: (int) $item['quantity'],
            ),
            $this->validated('items')
        );

        return new CreateOrderData(
            customerId: (int) $this->validated('customer_id'),
            items: $items,
        );
    }
}
