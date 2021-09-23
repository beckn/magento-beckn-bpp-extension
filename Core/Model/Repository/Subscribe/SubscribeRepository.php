<?php

namespace Beckn\Core\Model\Repository\Subscribe;

use Beckn\Core\Api\Data\SubscribeInterface;
use Beckn\Core\Helper\Data as Helper;
use Beckn\Core\Model\DigitalSignature as DigitalSignature;
use Psr\Log\LoggerInterface;

/**
 * Class SubscribeRepository
 * @author Indglobal
 * @package Beckn\Core\Model\Repository\Search
 */
class SubscribeRepository implements \Beckn\Core\Api\SubscribeRepositoryInterface
{
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var DigitalSignature
     */
    protected $_digitalSignature;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $_dataObjectHelper;

    /**
     * @var \Beckn\Core\Api\Data\SubscribeInterfaceFactory
     */
    protected $_subscribeFactory;

    public function __construct(
        Helper $helper,
        DigitalSignature $digitalSignature,
        LoggerInterface $logger,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Beckn\Core\Api\Data\SubscribeInterfaceFactory $subscribeFactory
    )
    {
        $this->_helper = $helper;
        $this->_digitalSignature = $digitalSignature;
        $this->_logger = $logger;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->_subscribeFactory = $subscribeFactory;

    }

    /**
     * @param mixed $challenge
     * @param mixed $subscriber_id
     * @return SubscribeInterface
     */
    public function manageSubscribe($challenge, $subscriber_id)
    {
        $response = [];
        $response[SubscribeInterface::KEY_ANSWER] = "";
        try {
            $keyPair = $this->_digitalSignature->getSodiumCryptoBoxKeypairFromSecretkeyAndPublickey();
            $sealOpen = sodium_crypto_box_seal_open(base64_decode($challenge), $keyPair);
            $response[SubscribeInterface::KEY_ANSWER] = $sealOpen;
        } catch (\SodiumException $ex) {
            $response[SubscribeInterface::KEY_ANSWER] = __($ex->getMessage());
        }
        $object = $this->_subscribeFactory->create();
        $interface = SubscribeInterface::class;
        $this->_dataObjectHelper->populateWithArray(
            $object, $response, $interface
        );
        return $object;
    }
}