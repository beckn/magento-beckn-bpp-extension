<?php

namespace Beckn\Core\Model\ResourceModel;

use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Api\Data\PolicyRequestInterface;

/**
 * Class PolicyRequest
 * @author Indglobal
 * @package Beckn\Core\Model\ResourceModel
 */
class PolicyRequest extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init(UpgradeSchema::TABLE_POLICY_REQUEST, PolicyRequestInterface::ENTITY_ID);
    }
}