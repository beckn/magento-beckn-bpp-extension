<?php

namespace Beckn\Checkout\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface OrderRepositoryInterface
 * @author Indglobal
 * @package Beckn\Core\Api
 */
interface OrderRepositoryInterface
{
    /**
     * @param mixed $context
     * @param mixed $message
     * @return string
     */
    public function manageOrder($context, $message);

    /**
     * @param mixed $context
     * @param mixed $message
     * @return string
     */
    public function manageSupport($context, $message);
}