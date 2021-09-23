<?php

namespace Beckn\Core\Model\ResourceModel\BecknLookup;

/**
 * Class Collection
 * @author Indglobal
 * @package Beckn\Core\Model\ResourceModel\BecknLookup
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'beckn_lookup_collection';
    protected $_eventObject = 'becknlookup_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Beckn\Core\Model\BecknLookup', 'Beckn\Core\Model\ResourceModel\BecknLookup');
    }
}
