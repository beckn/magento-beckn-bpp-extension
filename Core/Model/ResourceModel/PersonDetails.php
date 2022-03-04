<?php

namespace Beckn\Core\Model\ResourceModel;

use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Api\Data\PersonDetailsInterface;

/**
 * Class PersonDetails
 * @package Beckn\Core\Model\ResourceModel
 */
class PersonDetails extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init(UpgradeSchema::TABLE_PERSONS_DETAILS, PersonDetailsInterface::ENTITY_ID);
    }
}