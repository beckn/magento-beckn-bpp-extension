<?php

namespace Beckn\Checkout\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface CheckoutRepositoryInterface
 * @author Indglobal
 * @package Beckn\Core\Api
 */
interface CheckoutRepositoryInterface
{
    /**
     * @param mixed $context
     * @param mixed $message
     * @return string
     */
    public function manageCheckout($context, $message);
}