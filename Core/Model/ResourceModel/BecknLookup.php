<?php

namespace Beckn\Core\Model\ResourceModel;

/**
 * Class BecknLookup
 * @author Indogloabal
 * @package Beckn\Core\Model\ResourceModel
 */
class BecknLookup extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('beckn_lookup', 'entity_id');
    }

}