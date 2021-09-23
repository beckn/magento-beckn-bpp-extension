<?php

namespace Beckn\Core\Model\ResourceModel;

use Beckn\Core\Setup\UpgradeSchema;

/**
 * Class BecknQuoteMask
 * @author Indglobal
 * @package Beckn\Core\Model\ResourceModels
 */
class BecknQuoteMask extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init(UpgradeSchema::TABLE_BECKN_QUOTE_ID, "entity_id");
    }
}
