<?php

namespace App\Providers;

use App\Models\Orders;
use App\Observers\OrderObserver;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\DashboardRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\Contracts\CloudinaryServiceInterface;
use App\Services\CloudinaryService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class,   OrderRepository::class);
        $this->app->bind(CloudinaryServiceInterface::class, CloudinaryService::class);
        $this->app->bind(DashboardRepositoryInterface::class,  DashboardRepository::class);
    }

    public function boot(): void
    {
        Orders::observe(OrderObserver::class);
    }
}