<?php

namespace App\Infrastructure\Laravel\Providers;

use App\Application\Services\Order\Order\OrderService as ApplicationOrderService;
use App\Application\Services\Product\Product\ProductService as ApplicationProductService;
use App\Domain\Order\Contracts\OrderRepository;
use App\Domain\Order\Contracts\OrderServiceContract;
use App\Domain\Product\Contracts\ProductRepository;
use App\Domain\Product\Contracts\ProductServiceContract;
use App\Infrastructure\Laravel\Events\OrderStatusChanged;
use App\Infrastructure\Laravel\Listeners\ExportOrderListener;
use App\Infrastructure\Laravel\Order\EloquentOrderRepository;
use App\Infrastructure\Laravel\Product\EloquentProductRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OrderServiceContract::class, ApplicationOrderService::class);
        $this->app->bind(ProductServiceContract::class, ApplicationProductService::class);
        $this->app->bind(OrderRepository::class, EloquentOrderRepository::class);
        $this->app->bind(ProductRepository::class, EloquentProductRepository::class);
    }

    public function boot(): void
    {
        Event::listen(OrderStatusChanged::class, ExportOrderListener::class);

        RateLimiter::for('orders-create', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
