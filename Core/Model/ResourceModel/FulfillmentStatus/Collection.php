<?php

namespace Beckn\Core\Model\ResourceModel\FulfillmentStatus;

use Beckn\Core\Model\FulfillmentStatus;
use Beckn\Core\Model\ResourceModel\FulfillmentStatus as ResourceModelFulfillmentStatus;
use Beckn\Core\Api\Data\FulfillmentStatusInterface;

/**
 * Class Collection
 * @author Indglobal
 * @package Beckn\Core\Model\ResourceModel\FulfillmentStatus
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = FulfillmentStatusInterface::ENTITY_ID;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(FulfillmentStatus::class, ResourceModelFulfillmentStatus::class);
    }
}
