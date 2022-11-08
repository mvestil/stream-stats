<?php

namespace App\Http\Repositories;

use App\Http\Enums\Plan;
use App\Http\PaymentMethod;
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
    public function subscribe(User $user, PaymentMethod $paymentMethod, Plan $plan): string
    {
        /**
         * @var Gateway $gateway
         */
        $gateway = app(Gateway::class);

        $payload = [
            'planId'             => $plan->value,
            'paymentMethodToken' => $paymentMethod->getToken() ?: $this->createCustomerPaymentMethod($user, $paymentMethod),
        ];

        $result = $gateway->subscription()->create($payload);

        if (!$result->success) {
            throw new \RuntimeException('Unable to successfully subscribe in Braintree');
        }

        logger('result subscribe', [$result->subscription]);

        if ($result->success) {
            logger("success!: ".$result->subscription->id ?? '');
            return $result->subscription->id;
        }

        throw new \RuntimeException('Failed to subscribe');
    }

    /**
     * Create the customer in braintree if not yet created.
     * We can put this into its own service & repo, but for demo purposes, let's just do it here
     *
     * @param User          $user
     * @param PaymentMethod $paymentMethod
     * @return string - the payment method token
     * @throws \Throwable
     */
    private function createCustomerPaymentMethod(User $user, PaymentMethod $paymentMethod): string
    {
        throw_if(!$paymentMethod->getNonce(), new \RuntimeException('Payment method nonce is required'));

        /**
         * @var Gateway $gateway
         */
        $gateway = app(Gateway::class);
        $user = $this->createCustomerIfNotCreated($user);
        $result = $gateway->paymentMethod()->create([
            'customerId'         => $user->gateway_customer_id,
            'paymentMethodNonce' => $paymentMethod->getNonce(),
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
