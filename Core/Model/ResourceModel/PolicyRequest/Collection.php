<?php

namespace Beckn\Core\Model\ResourceModel\PolicyRequest;

use Beckn\Core\Model\PolicyRequest;
use Beckn\Core\Model\ResourceModel\PolicyRequest as ResourceModelPolicyRequest;
use Beckn\Core\Api\Data\PolicyRequestInterface;

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
    protected $_idFieldName = PolicyRequestInterface::ENTITY_ID;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(PolicyRequest::class, ResourceModelPolicyRequest::class);
    }
}
