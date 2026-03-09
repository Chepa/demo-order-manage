<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('stock_quantity');
            $table->index('category_id');
        });

        // Базовое заполнение category_id на основе существующего текстового поля category
        Product::query()
            ->select('id', 'category')
            ->chunkById(100, function ($products) {
                foreach ($products as $product) {
                    if (! $product->category) {
                        continue;
                    }

                    $category = Category::firstOrCreate(
                        ['name' => $product->category],
                        ['slug' => str($product->category)->slug()]
                    );

                    $product->category_id = $category->id;
                    $product->save();
                }
            });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};

