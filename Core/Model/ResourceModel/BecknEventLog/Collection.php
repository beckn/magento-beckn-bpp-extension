<?php

namespace Beckn\Core\Model\ResourceModel\BecknEventLog;

/**
 * Class Collection
 * @author Indglobal
 * @package Beckn\Core\Model\ResourceModel\BecknEventLog
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'beckn_event_log_collection';
    protected $_eventObject = 'beckn_event_log_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Beckn\Core\Model\BecknEventLog', 'Beckn\Core\Model\ResourceModel\BecknEventLog');
    }
}
