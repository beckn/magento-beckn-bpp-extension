<?php

namespace Beckn\Bpp\Model\ResourceModel\BecknQuoteMask;

/**
 * Class Collection
 * @author Indglobal
 * @package Beckn\Bpp\Model\ResourceModel\BecknQuoteMask
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
        $this->_init('Beckn\Bpp\Model\BecknQuoteMask', 'Beckn\Bpp\Model\ResourceModel\BecknQuoteMask');
    }
}
