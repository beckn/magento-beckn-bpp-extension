<?php

namespace Beckn\Core\Model\ResourceModel\ItemFulfillmentTimes;

use Beckn\Core\Api\Data\ItemFulfillmentTimesInterface;
use Beckn\Core\Model\ItemFulfillmentTimes;
use Beckn\Core\Model\ResourceModel\ItemFulfillmentTimes as ResourceModelItemFulfillmentTimes;

/**
 * Class Collection
 * @package Beckn\Core\Model\ResourceModel\ItemFulfillmentTimes
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ItemFulfillmentTimesInterface::ENTITY_ID;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(ItemFulfillmentTimes::class, ResourceModelItemFulfillmentTimes::class);
    }
}
