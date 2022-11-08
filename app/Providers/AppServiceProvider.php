<?php

namespace App\Providers;

use App\Http\Repositories\BrainTreeSubscriptionRepository;
use App\Http\Repositories\SubscriptionRepositoryInterface;
use Braintree\Gateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Gateway::class, function ($app) {
            return new Gateway([
                'environment' => config('services.braintree.environment'),
                'merchantId'  => config('services.braintree.merchant_id'),
                'publicKey'   => config('services.braintree.public_key'),
                'privateKey'  => config('services.braintree.private_key'),
            ]);
        });

        $this->bindRepositories();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    private function bindRepositories(): void
    {
        $this->app->bind(SubscriptionRepositoryInterface::class, BrainTreeSubscriptionRepository::class);
    }
}
