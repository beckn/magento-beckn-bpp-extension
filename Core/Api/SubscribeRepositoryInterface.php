<?php

namespace Beckn\Core\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface SubscribeRepositoryInterface
 * @author Indglobal
 * @package Beckn\Core\Api
 */
interface SubscribeRepositoryInterface
{
    /**
     * @param mixed $challenge
     * @param mixed $subscriber_id
     * @return \Beckn\Core\Api\Data\SubscribeInterface
     */
    public function manageSubscribe($challenge, $subscriber_id);
}