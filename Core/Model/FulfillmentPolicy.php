<?php

namespace Beckn\Core\Model;

use Beckn\Core\Api\Data\FulfillmentPolicyInterface;
use Beckn\Core\Api\Data\FulfillmentStatusInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Model\ResourceModel\FulfillmentPolicy as ResourceModelFulfillmentPolicy;

/**
 * Class FulfillmentPolicy
 * @author Indglobal
 * @package Beckn\Core\Model
 */
class FulfillmentPolicy extends \Magento\Framework\Model\AbstractModel implements FulfillmentPolicyInterface, IdentityInterface
{
    const CACHE_TAG = UpgradeSchema::TABLE_BECKN_FULFILLMENT_POLICY;

    protected $_cacheTag = UpgradeSchema::TABLE_BECKN_FULFILLMENT_POLICY;

    protected $_eventPrefix = UpgradeSchema::TABLE_BECKN_FULFILLMENT_POLICY;

    protected $_fulfillmentStatusCollectionFactory;

    protected function _construct(){
        $this->_init(ResourceModelFulfillmentPolicy::class);
    }

    /**
     * FulfillmentPolicy constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param ResourceModel\FulfillmentStatus\CollectionFactory $fulfillmentStatusCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Beckn\Core\Model\ResourceModel\FulfillmentStatus\CollectionFactory $fulfillmentStatusCollectionFactory,
        array $data = []
    )
    {
        $this->_fulfillmentStatusCollectionFactory = $fulfillmentStatusCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
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
     * @return \Beckn\Core\Model\ResourceModel\FulfillmentStatus\Collection|null
     */
    public function getRequestBody(){
        /**
         * @var \Beckn\Core\Model\ResourceModel\FulfillmentStatus\Collection $collection
         */
        $collection = $this->_fulfillmentStatusCollectionFactory->create();
        $collection->addFieldToFilter(FulfillmentStatusInterface::LOCATION_ID, $this->getEntityId());
        if($collection->getSize()){
            return $collection;
        }
        return null;
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

    public function getName()
    {
        return parent::getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritdoc
     */

    public function getType()
    {
        return parent::getData(self::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritdoc
     */

    public function getCenter()
    {
        return parent::getData(self::CENTER);
    }

    /**
     * @inheritdoc
     */
    public function setCenter($center)
    {
        return $this->setData(self::CENTER, $center);
    }

    /**
     * @inheritdoc
     */

    public function getRadius()
    {
        return parent::getData(self::RADIUS);
    }

    /**
     * @inheritdoc
     */
    public function setRadius($radius)
    {
        return $this->setData(self::RADIUS, $radius);
    }

    /**
     * @inheritdoc
     */

    public function getAgents()
    {
        return parent::getData(self::AGENTS);
    }

    /**
     * @inheritdoc
     */
    public function setAgents($agents)
    {
        return $this->setData(self::AGENTS, $agents);
    }

    /**
     * @inheritdoc
     */

    public function getVehicles()
    {
        return parent::getData(self::VEHICLES);
    }

    /**
     * @inheritdoc
     */
    public function setVehicles($vehicles)
    {
        return $this->setData(self::VEHICLES, $vehicles);
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