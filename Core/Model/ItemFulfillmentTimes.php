<?php

namespace Beckn\Core\Model;

use Beckn\Core\Api\Data\ItemFulfillmentTimesInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Model\ResourceModel\ItemFulfillmentTimes as ResourceModelItemFulfillmentTimes;

/**
 * Class ItemFulfillmentOptions
 * @package Beckn\Core\Model
 */
class ItemFulfillmentTimes extends \Magento\Framework\Model\AbstractModel implements ItemFulfillmentTimesInterface, IdentityInterface
{

    const CACHE_TAG = UpgradeSchema::TABLE_ITEM_FULFILLMENT_OPTIONS_TIMES;

    protected $_cacheTag = UpgradeSchema::TABLE_ITEM_FULFILLMENT_OPTIONS_TIMES;

    protected $_eventPrefix = UpgradeSchema::TABLE_ITEM_FULFILLMENT_OPTIONS_TIMES;

    protected function _construct(){
        $this->_init(ResourceModelItemFulfillmentTimes::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */

    public function getEntityId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * @param int $entityId
     * @return LocationPolicy|int
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @return array|mixed|null
     */
    public function getFulfillmentOptionId(){
        return parent::getData(self::FULFILLMENT_OPTION_ID);
    }

    /**
     * @param $fulfillmentOptionId
     * @return ItemFulfillmentTimes|int
     */
    public function setFulfillmentOptionId($fulfillmentOptionId){
        return $this->setData(self::FULFILLMENT_OPTION_ID, $fulfillmentOptionId);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getStartTime(){
        return parent::getData(self::START_TIME);
    }

    /**
     * @param $startTime
     * @return ItemFulfillmentTimes|string
     */
    public function setStartTime($startTime){
        return $this->setData(self::START_TIME, $startTime);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getEndTime(){
        return parent::getData(self::END_TIME);
    }

    /**
     * @param $endTime
     * @return ItemFulfillmentTimes|string
     */
    public function setEndTime($endTime){
        return $this->setData(self::START_TIME, $endTime);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * @param $createdAt
     * @return string
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * @param $updatedAt
     * @return string
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }


}