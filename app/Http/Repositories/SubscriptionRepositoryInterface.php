<?php

namespace App\Http\Repositories;

use App\Http\PaymentMethod;
use App\Http\SubscribeParams;
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
     * @param User                      $user
     * @param SubscribeParams $subscribeParams
     * @return Subscription - the subscription id
     */
    public function subscribe(User $user, SubscribeParams $subscribeParams): Subscription;

    /**
     * @param User $user
     * @return mixed
     */
    public function cancel(User $user);
}
