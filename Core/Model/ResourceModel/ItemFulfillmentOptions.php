<?php

namespace Beckn\Core\Model\ResourceModel;

use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Api\Data\ItemFulfillmentOptionsInterface;

/**
 * Class ItemFulfillmentOptions
 * @package Beckn\Core\Model\ResourceModel
 */
class ItemFulfillmentOptions extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init(UpgradeSchema::TABLE_ITEM_FULFILLMENT_OPTIONS, ItemFulfillmentOptionsInterface::ENTITY_ID);
    }
}