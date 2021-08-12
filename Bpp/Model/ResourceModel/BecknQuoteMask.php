<?php

namespace Beckn\Bpp\Model\ResourceModel;

use Beckn\Bpp\Setup\UpgradeSchema;

/**
 * Class BecknQuoteMask
 * @author Indglobal
 * @package Beckn\Bpp\Model\ResourceModels
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
        $this->_init(UpgradeSchema::TABLE_NAME, "entity_id");
    }
}
