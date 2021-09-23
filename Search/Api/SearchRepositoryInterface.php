<?php

namespace Beckn\Search\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface SearchRepositoryInterface
 * @author Indglobal
 * @package Beckn\Search\Api
 */
interface SearchRepositoryInterface
{
    /**
     * @param mixed $context
     * @param mixed $message
     * @return string
     */
    public function getSearch($context, $message);
}