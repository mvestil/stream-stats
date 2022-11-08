<?php

namespace App\Http;

/**
 * Class PaymentMethod
 *
 * Serves as value object for payment method information
 */
class PaymentMethod
{
    /**
     * @var string|null
     */
    private $nonce;

    /**
     * @var string|null
     */
    private $token;

    /**
     * @return string|null
     */
    public function getNonce(): ?string
    {
        return $this->nonce;
    }

    /**
     * @param string|null $nonce
     * @return PaymentMethod
     */
    public function setNonce(?string $nonce): PaymentMethod
    {
        $this->nonce = $nonce;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return PaymentMethod
     */
    public function setToken(?string $token): PaymentMethod
    {
        $this->token = $token;

        return $this;
    }
}
