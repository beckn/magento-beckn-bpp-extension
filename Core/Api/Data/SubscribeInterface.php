<?php

namespace Beckn\Core\Api\Data;

/**
 * Interface SubscribeInterface
 * @author Indglobal
 * @package Beckn\Core\Api\Data
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