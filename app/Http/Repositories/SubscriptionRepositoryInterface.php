<?php

namespace App\Http\Repositories;

use App\Http\PaymentMethod;
use App\Models\Subscription;
use App\Models\User;
use App\Http\Enums\Plan;

/**
 * Interface SubscriptionRepositoryInterface
 *
 * @package App\Http\Repositories
 */
interface SubscriptionRepositoryInterface
{
    /**
     * Create a subscription to the payment gateway
     *
     * @param User          $user
     * @param PaymentMethod $paymentMethod
     * @param Plan          $plan
     * @return string - the subscription id
     */
    public function subscribe(User $user, PaymentMethod $paymentMethod, Plan $plan): string;
}
