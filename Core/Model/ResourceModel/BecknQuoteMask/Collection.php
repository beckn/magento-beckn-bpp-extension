<?php

namespace Beckn\Core\Model\ResourceModel\BecknQuoteMask;

/**
 * Class Collection
 * @author Indglobal
 * @package Beckn\Core\Model\ResourceModel\BecknQuoteMask
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init('Beckn\Core\Model\BecknQuoteMask', 'Beckn\Core\Model\ResourceModel\BecknQuoteMask');
    }
}
