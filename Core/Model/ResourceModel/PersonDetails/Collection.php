<?php

namespace Beckn\Core\Model\ResourceModel\PersonDetails;

use Beckn\Core\Model\PersonDetails;
use Beckn\Core\Model\ResourceModel\PersonDetails as ResourceModelPersonDetails;
use Beckn\Core\Api\Data\PersonDetailsInterface;

/**
 * Class Collection
 * @package Beckn\Core\Model\ResourceModel\PersonDetails
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = PersonDetailsInterface::ENTITY_ID;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(PersonDetails::class, ResourceModelPersonDetails::class);
    }
}
