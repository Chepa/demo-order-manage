<?php

namespace App\Application\Services\Order\Order;

use App\Domain\Order\Entities\Order as DomainOrder;
use App\Domain\Order\Mappers\OrderMapper;
use App\DTOs\CreateOrderData;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

readonly class CreateOrderHandler
{
    public function __construct(
        private OrderMapper $orderMapper,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(CreateOrderData $data): DomainOrder
    {
        $eloquentOrder = DB::transaction(function () use ($data) {
            $order = Order::create([
                'customer_id' => $data->customerId,
                'status' => Order::STATUS_NEW,
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            foreach ($data->items as $itemData) {
                $product = Product::where('id', $itemData->productId)->lockForUpdate()->first();

                if (! $product) {
                    throw ValidationException::withMessages([
                        'items' => ["Product with id {$itemData->productId} not found."],
                    ]);
                }

                if ($product->stock_quantity < $itemData->quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for product {$product->sku}. Available: {$product->stock_quantity}."],
                    ]);
                }

                $unitPrice = $product->price;
                $totalPrice = $unitPrice * $itemData->quantity;
                $totalAmount += $totalPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $itemData->quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);

                $product->decrement('stock_quantity', $itemData->quantity);
            }

            $order->update(['total_amount' => $totalAmount]);

            return $order->load(['customer', 'items.product']);
        });

        return $this->orderMapper->toDomain($eloquentOrder);
    }
}

