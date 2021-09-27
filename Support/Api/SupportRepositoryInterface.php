<?php

namespace Beckn\Support\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface SupportRepositoryInterface
 * @author Indglobal
 * @package Beckn\Support\Api
 */
interface SupportRepositoryInterface
{
    /**
     * @param mixed $context
     * @param mixed $message
     * @return string
     */
    public function manageSupport($context, $message);
}