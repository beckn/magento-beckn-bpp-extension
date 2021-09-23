<?php

namespace Beckn\Checkout\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface TrackRepositoryInterface
 * @author Indglobal
 * @package Beckn\Core\Api
 */
interface TrackRepositoryInterface
{
    /**
     * @param mixed $context
     * @param mixed $message
     * @return string
     */
    public function trackOrder($context, $message);
}