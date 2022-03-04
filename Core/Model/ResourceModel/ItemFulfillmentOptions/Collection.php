<?php

namespace Beckn\Core\Model\ResourceModel\ItemFulfillmentOptions;

use Beckn\Core\Model\ItemFulfillmentOptions;
use Beckn\Core\Model\ResourceModel\ItemFulfillmentOptions as ResourceModelItemFulfillmentOptions;
use Beckn\Core\Api\Data\ItemFulfillmentOptionsInterface;

/**
 * Class Collection
 * @package Beckn\Core\Model\ResourceModel\ItemFulfillmentOptions
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ItemFulfillmentOptionsInterface::ENTITY_ID;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(ItemFulfillmentOptions::class, ResourceModelItemFulfillmentOptions::class);
    }
}
