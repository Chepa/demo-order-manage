<?php

namespace App\Domain\Product\Entities;

use App\Models\Product as EloquentProduct;

class Product
{
    private EloquentProduct $product;

    public function setEloquentModel(EloquentProduct $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getEloquentModel(): EloquentProduct
    {
        return $this->product;
    }
}

