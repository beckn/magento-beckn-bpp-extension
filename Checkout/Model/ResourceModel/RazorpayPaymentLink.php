<?php

namespace Beckn\Checkout\Model\ResourceModel;

use Beckn\Checkout\Setup\UpgradeSchema;

/**
 * Class RazorpayPaymentLink
 * @author Indglobal
 * @package Beckn\Checkout\Model\ResourceModels
 */
class RazorpayPaymentLink extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected $_store = null;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context, $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init(UpgradeSchema::TABLE_BECKN_RAZORPAY, "entity_id");
    }
}
