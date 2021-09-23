<?php

namespace Beckn\Core\Model\ResourceModel;

use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Api\Data\FulfillmentPolicyInterface;

/**
 * Class FulfillmentPolicy
 * @author Indglobal
 * @package Beckn\Core\Model\ResourceModel
 */
class FulfillmentPolicy extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init(UpgradeSchema::TABLE_BECKN_FULFILLMENT_POLICY, FulfillmentPolicyInterface::ENTITY_ID);
    }
}