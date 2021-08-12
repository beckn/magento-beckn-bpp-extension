<?php

namespace Beckn\Bpp\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface SubscribeRepositoryInterface
 * @author Indglobal
 * @package Beckn\Bpp\Api
 */
interface SubscribeRepositoryInterface
{
    /**
     * @param mixed $challenge
     * @param mixed $subscriber_id
     * @return \Beckn\Bpp\Api\Data\SubscribeInterface
     */
    public function manageSubscribe($challenge, $subscriber_id);
}