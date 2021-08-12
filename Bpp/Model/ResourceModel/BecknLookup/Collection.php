<?php

namespace Beckn\Bpp\Model\ResourceModel\BecknLookup;

/**
 * Class Collection
 * @author Indoglobal
 * @package Beckn\Bpp\Model\ResourceModel\BecknLookup
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
        $this->_init('Beckn\Bpp\Model\BecknLookup', 'Beckn\Bpp\Model\ResourceModel\BecknLookup');
    }
}
