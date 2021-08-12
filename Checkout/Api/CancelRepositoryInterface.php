<?php

namespace Beckn\Checkout\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface CheckoutRepositoryInterface
 * @author Indglobal
 * @package Beckn\Bpp\Api
 */
interface CancelRepositoryInterface
{
    /**
     * @param mixed $context
     * @return string
     */
    public function cancelReason($context);

    /**
     * @param mixed $context
     * @param mixed $message
     * @return string
     */
    public function cancelOrder($context, $message);
}