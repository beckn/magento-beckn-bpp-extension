<?php

namespace Beckn\Core\Model\ResourceModel\ProductFlagReference;

use Beckn\Core\Model\ProductFlagReference;
use Beckn\Core\Model\ResourceModel\ProductFlagReference as ResourceModelProductFlagReference;
use Beckn\Core\Api\Data\ProductFlagReferenceInterface;

/**
 * Class Collection
 * @package Beckn\Core\Model\ResourceModel\ProductFlagReference
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ProductFlagReferenceInterface::ENTITY_ID;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(ProductFlagReference::class, ResourceModelProductFlagReference::class);
    }
}
