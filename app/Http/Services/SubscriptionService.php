<?php

namespace App\Http\Services;

use App\Http\PaymentMethod;
use App\Http\Repositories\SubscriptionRepositoryInterface;
use App\Http\SubscribeParams;
use App\Models\Subscription;
use App\Models\User;
use App\Http\Enums\Plan;
use RuntimeException;

/**
 * Class SubscriptionService
 */
class SubscriptionService
{
    /**
     * SubscriptionService constructor.
     *
     * @param SubscriptionRepositoryInterface $repo
     */
    public function __construct(
        private SubscriptionRepositoryInterface $repo
    ) {
    }


    /**
     * Subscribe the user to a subscription
     *
     * @param User                      $user
     * @param SubscribeParams $subscribeParams
     * @return Subscription
     * @throws \Throwable
     */
    public function subscribe(User $user, SubscribeParams $subscribeParams): Subscription
    {
        throw_if($user->isSubscribed(), new RuntimeException('User is already subscribed'));
        throw_if(
            !$subscribeParams->getPaymentMethodNonce() && !$subscribeParams->getPaymentMethodToken(),
            new RuntimeException('Payment method information is required')
        );

        return $this->repo->subscribe($user, $subscribeParams);
    }

    /**
     * Cancel the subscription
     *
     * @param User $user
     * @throws \Throwable
     */
    public function cancel(User $user)
    {
        throw_if(!$user->isSubscribed(), new RuntimeException('User is not subscribed. Nothing to cancel'));

        $this->repo->cancel($user);
    }
}
