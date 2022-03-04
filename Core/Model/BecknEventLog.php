<?php

namespace Beckn\Core\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Beckn\Core\Api\Data\BecknEventLogInterface;
use Beckn\Core\Setup\UpgradeSchema;

/**
 * Class BecknEventLog
 * @author Indglobal
 * @package Beckn\Core\Model
 */
class BecknEventLog extends \Magento\Framework\Model\AbstractModel implements BecknEventLogInterface, IdentityInterface
{
    const CACHE_TAG = UpgradeSchema::TABLE_BECKN_EVENT_LOG;

    protected $_cacheTag = UpgradeSchema::TABLE_BECKN_EVENT_LOG;

    protected $_eventPrefix = UpgradeSchema::TABLE_BECKN_EVENT_LOG;

    protected function _construct()
    {
        $this->_init('Beckn\Core\Model\ResourceModel\BecknEventLog');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritDoc
     */
    public function getEventType()
    {
        return parent::getData(self::EVENT_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setEventType($eventType)
    {
        return $this->setData(self::EVENT_TYPE, $eventType);
    }

    /**
     * @inheritDoc
     */
    public function getEventName()
    {
        return parent::getData(self::EVENT_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setEventName($eventName)
    {
        return $this->setData(self::EVENT_NAME, $eventName);
    }

    /**
     * @inheritDoc
     */
    public function getTransactionId()
    {
        return parent::getData(self::TRANSACTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTransactionId($transactionId)
    {
        return $this->setData(self::TRANSACTION_ID, $transactionId);
    }

    /**
     * @inheritDoc
     */
    public function getSubscriberId()
    {
        return parent::getData(self::SUBSCRIBER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSubscriberId($subscriberId)
    {
        return $this->setData(self::SUBSCRIBER_ID, $subscriberId);
    }

    /**
     * @inheritDoc
     */
    public function getMessageId()
    {
        return parent::getData(self::MESSAGE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMessageId($messageId)
    {
        return $this->setData(self::MESSAGE_ID, $messageId);
    }

    /**
     * @inheritDoc
     */
    public function getHeaderAuthorization()
    {
        return parent::getData(self::HEADER_AUTHORIZATION);
    }

    /**
     * @inheritDoc
     */
    public function setHeaderAuthorization($headerAuthorization)
    {
        return $this->setData(self::HEADER_AUTHORIZATION, $headerAuthorization);
    }

    /**
     * @inheritDoc
     */
    public function getProxyHeaderAuthorization()
    {
        return parent::getData(self::PROXY_HEADER_AUTHORIZATION);
    }

    /**
     * @inheritDoc
     */
    public function setProxyHeaderAuthorization($proxyHeaderAuthorization)
    {
        return $this->setData(self::PROXY_HEADER_AUTHORIZATION, $proxyHeaderAuthorization);
    }

    /**
     * @inheritDoc
     */
    public function getEventData()
    {
        return parent::getData(self::EVENT_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setEventData($eventData)
    {
        return $this->setData(self::EVENT_DATA, $eventData);
    }

    /**
     * @inheritDoc
     */
    public function getResponseData()
    {
        return parent::getData(self::RESPONSE_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setResponseData($responseData)
    {
        return $this->setData(self::RESPONSE_DATA, $responseData);
    }

    /**
     * @inheritDoc
     */
    public function getErrorCode()
    {
        return parent::getData(self::ERROR_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setErrorCode($errorCode)
    {
        return $this->setData(self::ERROR_CODE, $errorCode);
    }

    /**
     * @inheritDoc
     */
    public function getAcknowledgementStatus()
    {
        return parent::getData(self::ACKNOWLEDGEMENT_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setAcknowledgementStatus($acknowledgementStatus)
    {
        return $this->setData(self::ACKNOWLEDGEMENT_STATUS, $acknowledgementStatus);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}