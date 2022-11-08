<?php

namespace App\Http\Services;

use App\Http\PaymentMethod;
use App\Http\Repositories\SubscriptionRepositoryInterface;
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
     * @param User          $user
     * @param PaymentMethod $paymentMethod
     * @param Plan          $plan
     * @return Subscription
     * @throws \Throwable
     */
    public function subscribe(User $user, PaymentMethod $paymentMethod, Plan $plan): Subscription
    {
        throw_if($user->isSubscribed(), new RuntimeException('User is already subscribed'));
        throw_if(
            !$paymentMethod->getNonce() && !$paymentMethod->getToken(),
            new RuntimeException('Payment method information is required')
        );

        $subId = $this->repo->subscribe($user, $paymentMethod, $plan);

        // IMPROVEMENTS: move to it's own repository for testability
        $sub = Subscription::create([
            'user_id'         => $user->id,
            'subscription_id' => $subId,
            'plan_id'         => $plan->value,
        ]);

        return $sub;
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
        $this->repo->cancel($user, $user->subscription->subscription_id);
        $user->subscription->delete();
    }
}
