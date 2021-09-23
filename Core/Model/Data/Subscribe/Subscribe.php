<?php

namespace Beckn\Core\Model\Data\Subscribe;

/**
 * Class Subscribe
 * @author Indglobal
 * @package Beckn\Core\Model\Data\Search
 */
class Subscribe extends \Magento\Framework\Model\AbstractExtensibleModel implements \Beckn\Core\Api\Data\SubscribeInterface
{

    /**
     * @inheritdoc
     */
    public function getAnswer()
    {
        return $this->getData(self::KEY_ANSWER);
    }

    /**
     * @inheritdoc
     */
    public function setAnswer($answer)
    {
        return $this->setData(self::KEY_ANSWER, $answer);
    }
}