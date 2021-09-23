<?php

namespace Beckn\Core\Model\ResourceModel\PricePolicy;

use Beckn\Core\Model\PricePolicy;
use Beckn\Core\Model\ResourceModel\PricePolicy as ResourceModelPricePolicy;
use Beckn\Core\Api\Data\PricePolicyInterface;

/**
 * Class Collection
 * @author Indglobal
 * @package Beckn\Core\Model\ResourceModel\PricePolicy
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = PricePolicyInterface::ENTITY_ID;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(PricePolicy::class, ResourceModelPricePolicy::class);
    }
}
