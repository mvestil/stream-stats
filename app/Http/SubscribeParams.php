<?php

namespace App\Http;

use App\Http\Enums\Plan;

/**
 * Class SubscribeParams
 */
class SubscribeParams
{
    /**
     * @var string|null
     */
    private ?string $paymentMethodNonce;

    /**
     * @var string|null
     */
    private ?string $paymentMethodToken;

    /**
     * @var Plan
     */
    private Plan $plan;


    /**
     * SubscribeParams constructor.
     *
     * @param Plan        $plan
     * @param string|null $paymentMethodNonce
     * @param string|null $paymentMethodToken
     */
    public function __construct(Plan $plan, ?string $paymentMethodNonce, ?string $paymentMethodToken = null)
    {
        $this->paymentMethodNonce = $paymentMethodNonce;
        $this->paymentMethodToken = $paymentMethodToken;
        $this->plan = $plan;
    }

    /**
     * @return string|null
     */
    public function getPaymentMethodNonce(): ?string
    {
        return $this->paymentMethodNonce;
    }

    /**
     * @return string|null
     */
    public function getPaymentMethodToken(): ?string
    {
        return $this->paymentMethodToken;
    }

    /**
     * @return Plan
     */
    public function getPlan(): Plan
    {
        return $this->plan;
    }
}
