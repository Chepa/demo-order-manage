<?php

namespace App\Domain\Order\Entities;

use App\Models\Order as EloquentOrder;
use App\Models\OrderItem as EloquentOrderItem;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Order
{
    private EloquentOrder $order;

    /**
     * Оборачивает Eloquent-модель доменным объектом.
     */
    public function setEloquentModel(EloquentOrder $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Возвращает исходную Eloquent-модель (для инфраструктурного слоя).
     */
    public function getEloquentModel(): EloquentOrder
    {
        return $this->order;
    }

    /**
     * Проверяет, может ли заказ перейти в новый статус.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $currentStatus = $this->order->status;
        $allowed = EloquentOrder::VALID_TRANSITIONS[$currentStatus] ?? [];

        return in_array($newStatus, $allowed, true);
    }

    /**
     * Меняет статус заказа с проверкой допустимости перехода.
     */
    public function changeStatus(string $newStatus): void
    {
        if (! $this->canTransitionTo($newStatus)) {
            throw new InvalidArgumentException("Transition from {$this->order->status} to $newStatus is not allowed.");
        }

        $this->order->status = $newStatus;

        if ($newStatus === EloquentOrder::STATUS_CONFIRMED && is_null($this->order->confirmed_at)) {
            $this->order->confirmed_at = now();
        }

        if ($newStatus === EloquentOrder::STATUS_SHIPPED && is_null($this->order->shipped_at)) {
            $this->order->shipped_at = now();
        }
    }

    /**
     * Добавляет позицию в заказ и перерасчитывает сумму.
     */
    public function addItem(int $productId, float $unitPrice, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be greater than zero.');
        }

        $totalPrice = $unitPrice * $quantity;

        $this->order->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
        ]);

        $this->recalculateTotal();
    }

    /**
     * Пересчитывает общую сумму заказа на основе позиций.
     */
    public function recalculateTotal(): void
    {
        /** @var Collection<EloquentOrderItem> $items */
        $items = $this->order->items;

        $totalAmount = $items->sum('total_price');

        $this->order->total_amount = $totalAmount;
    }
}

