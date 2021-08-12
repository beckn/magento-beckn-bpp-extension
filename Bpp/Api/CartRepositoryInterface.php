<?php

namespace Beckn\Bpp\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface CartRepositoryInterface
 * @author Indglobal
 * @package Beckn\Bpp\Api
 */
interface CartRepositoryInterface
{
    /**
     * @param mixed $context
     * @param mixed $message
     * @return string
     */
    public function manageCart($context, $message);
}