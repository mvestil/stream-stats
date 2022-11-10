<?php

namespace App\Http\Repositories;

use App\Http\Enums\Plan;
use App\Http\PaymentMethod;
use App\Http\SubscribeParams;
use App\Models\Subscription;
use App\Models\User;
use Braintree\Gateway;
use http\Exception\RuntimeException;

/**
 * Class BrainTreeSubscriptionRepository
 */
class BrainTreeSubscriptionRepository implements SubscriptionRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function subscribe(User $user, SubscribeParams $subscribeParams): Subscription
    {
        /**
         * @var Gateway $gateway
         */
        $gateway = app(Gateway::class);

        $payload = [
            'planId'             => $subscribeParams->getPlan()->value,
            'paymentMethodToken' => $subscribeParams->getPaymentMethodToken() ?: $this->createCustomerPaymentMethod($user, $subscribeParams),
        ];

        $result = $gateway->subscription()->create($payload);

        logger('result subscribe', [$result->subscription]);

        if (!$result->success) {
            throw new \RuntimeException('Unable to successfully subscribe in Braintree');
        }

        if ($result->success) {
            logger("success!: ".$result->subscription->id ?? '');

            return Subscription::create([
                'user_id'         => $user->id,
                'subscription_id' => $result->subscription->id,
                'plan_id'         => $subscribeParams->getPlan()->value,
            ]);
        }

        throw new \RuntimeException('Failed to subscribe');
    }

    /**
     * @param User $user
     * @return mixed|void
     */
    public function cancel(User $user)
    {
        /**
         * @var Gateway $gateway
         */
        $gateway = app(Gateway::class);

        $result = $gateway->subscription()->cancel($user->subscription->subscription_id);

        if (!$result->success) {
            throw new \RuntimeException('Failed to cancel subscription');
        }

        $user->subscription->delete();
    }

    /**
     * Create the customer in braintree if not yet created.
     * We can put this into its own service & repo, but for demo purposes, let's just do it here
     *
     * @param User            $user
     * @param SubscribeParams $subscribeParams
     * @return string - the payment method token
     * @throws \Throwable
     */
    private function createCustomerPaymentMethod(User $user, SubscribeParams $subscribeParams): string
    {
        throw_if(!$subscribeParams->getPaymentMethodNonce(), new \RuntimeException('Payment method nonce is required'));

        /**
         * @var Gateway $gateway
         */
        $gateway = app(Gateway::class);
        $user = $this->createCustomerIfNotCreated($user);
        $result = $gateway->paymentMethod()->create([
            'customerId'         => $user->gateway_customer_id,
            'paymentMethodNonce' => $subscribeParams->getPaymentMethodNonce(),
        ]);

        if (!$result->success) {
            throw new \RuntimeException('Unable to create payment method');
        }

        logger('result payment method', [$result->paymentMethod]);

        return $result->paymentMethod->token;
    }

    /**
     * @param User $user
     * @return User
     */
    private function createCustomerIfNotCreated(User $user): User
    {
        /**
         * @var Gateway $gateway
         */
        $gateway = app(Gateway::class);

        if (!$user->gateway_customer_id) {
            $result = $gateway->customer()->create([
                'firstName' => $user->name,
                'lastName'  => $user->name,
                'email'     => $user->email,
            ]);

            if (!$result->success) {
                throw new \RuntimeException('Unable to create customer');
            }

            logger('result customer', [$result->customer]);

            $user->gateway_customer_id = $result->customer->id;
            $user->save();
        }

        return $user;
    }
}
