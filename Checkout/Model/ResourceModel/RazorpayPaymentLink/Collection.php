<?php

namespace Beckn\Checkout\Model\ResourceModel\RazorpayPaymentLink;

/**
 * Class Collection
 * @author Indglobal
 * @package Beckn\Core\Model\ResourceModel\BecknQuoteMask
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model.
     */
    protected function _construct() {
        $this->_init('Beckn\Checkout\Model\RazorpayPaymentLink', 'Beckn\Checkout\Model\ResourceModel\RazorpayPaymentLink');
    }

}
