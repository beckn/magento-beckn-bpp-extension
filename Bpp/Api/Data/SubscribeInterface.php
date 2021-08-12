<?php

namespace Beckn\Bpp\Api\Data;

/**
 * Interface SubscribeInterface
 * @author Indglobal
 * @package Beckn\Bpp\Api\Data
 */
interface SubscribeInterface
{
    const KEY_ANSWER = "answer";

    /**
     * @return string
     */
    public function getAnswer();

    /**
     * @param string $message
     * @return $this
     */
    public function setAnswer($message);
}