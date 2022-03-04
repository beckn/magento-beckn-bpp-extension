<?php

namespace Beckn\Core\Model\ResourceModel;

use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Api\Data\BecknEventLogInterface;

/**
 * Class BecknEventLog
 * @author Indogloabal
 * @package Beckn\Core\Model\ResourceModel
 */
class BecknEventLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init(UpgradeSchema::TABLE_BECKN_EVENT_LOG, BecknEventLogInterface::ENTITY_ID);
    }

}