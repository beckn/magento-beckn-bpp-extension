<?php

namespace Beckn\Core\Model\ResourceModel\FulfillmentPolicy;

use Beckn\Core\Model\FulfillmentPolicy;
use Beckn\Core\Model\ResourceModel\FulfillmentPolicy as ResourceModelFulfillmentPolicy;
use Beckn\Core\Api\Data\FulfillmentPolicyInterface;

/**
 * Class Collection
 * @author Indglobal
 * @package Beckn\Core\Model\ResourceModel\FulfillmentPolicy
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = FulfillmentPolicyInterface::ENTITY_ID;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(FulfillmentPolicy::class, ResourceModelFulfillmentPolicy::class);
    }
}
