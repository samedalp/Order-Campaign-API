<?php

namespace App\Providers;

use App\Services\Campaign\CampaignService;
use App\Services\Campaign\CampaignServiceInterface;
use App\Services\Order\CreateOrderService;
use App\Services\Order\CreateOrderServiceInterface;
use App\Services\Order\OrderNumberGenerator;
use App\Services\Order\OrderNumberGeneratorInterface;
use App\Services\Shipping\ShippingCalculator;
use App\Services\Shipping\ShippingCalculatorInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CampaignServiceInterface::class, CampaignService::class);
        $this->app->bind(ShippingCalculatorInterface::class, ShippingCalculator::class);
        $this->app->bind(OrderNumberGeneratorInterface::class, OrderNumberGenerator::class);
        $this->app->bind(CreateOrderServiceInterface::class, CreateOrderService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
