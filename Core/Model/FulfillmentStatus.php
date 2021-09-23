<?php

namespace Beckn\Core\Model;

use Beckn\Core\Api\Data\FulfillmentStatusInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Model\ResourceModel\FulfillmentStatus as ResourceModelFulfillmentStatus;

/**
 * Class PricePolicy
 * @author Indglobal
 * @package Beckn\Core\Model
 */
class FulfillmentStatus extends \Magento\Framework\Model\AbstractModel implements FulfillmentStatusInterface, IdentityInterface
{
    const CACHE_TAG = UpgradeSchema::TABLE_BECKN_FULFILLMENT_STATUS;

    protected $_cacheTag = UpgradeSchema::TABLE_BECKN_FULFILLMENT_STATUS;

    protected $_eventPrefix = UpgradeSchema::TABLE_BECKN_FULFILLMENT_STATUS;

    protected function _construct()
    {
        $this->_init(ResourceModelFulfillmentStatus::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

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
     * @inheritdoc
     */
    public function getLocationId()
    {
        return parent::getData(self::LOCATION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return parent::getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}