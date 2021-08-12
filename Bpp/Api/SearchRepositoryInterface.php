<?php

namespace Beckn\Bpp\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface SearchRepositoryInterface
 * @author Indglobal
 * @package Beckn\Bpp\Api
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