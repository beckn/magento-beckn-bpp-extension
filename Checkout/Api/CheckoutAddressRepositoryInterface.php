<?php

namespace Beckn\Checkout\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface CheckoutAddressRepositoryInterface
 * @author Indglobal
 * @package Beckn\Bpp\Api
 */
interface CheckoutAddressRepositoryInterface
{
    /**
     * @param mixed $context
     * @param mixed $message
     * @return string
     */
    public function manageAddress($context, $message);
}