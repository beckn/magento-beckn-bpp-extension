<?php

namespace Beckn\Core\Model\ResourceModel;

use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Api\Data\ItemFulfillmentTimesInterface;

/**
 * Class ItemFulfillmentTimes
 * @package Beckn\Core\Model\ResourceModel
 */
class ItemFulfillmentTimes extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init(UpgradeSchema::TABLE_ITEM_FULFILLMENT_OPTIONS_TIMES, ItemFulfillmentTimesInterface::ENTITY_ID);
    }
}