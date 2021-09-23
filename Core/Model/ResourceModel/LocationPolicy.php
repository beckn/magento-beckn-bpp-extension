<?php

namespace Beckn\Core\Model\ResourceModel;

use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Api\Data\LocationPolicyInterface;

/**
 * Class LocationPolicy
 * @author Indglobal
 * @package Beckn\Core\Model\ResourceModel
 */
class LocationPolicy extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init(UpgradeSchema::TABLE_BECKN_LOCATION_POLICY, LocationPolicyInterface::ENTITY_ID);
    }
}