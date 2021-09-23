<?php

namespace Beckn\Core\Model\ResourceModel\LocationPolicy;

use Beckn\Core\Model\LocationPolicy;
use Beckn\Core\Model\ResourceModel\LocationPolicy as ResourceModelLocationPolicy;
use Beckn\Core\Api\Data\LocationPolicyInterface;

/**
 * Class Collection
 * @author Indglobal
 * @package Beckn\Core\Model\ResourceModel\LocationPolicy
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = LocationPolicyInterface::ENTITY_ID;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(LocationPolicy::class, ResourceModelLocationPolicy::class);
    }
}
