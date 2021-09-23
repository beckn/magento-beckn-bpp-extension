<?php

namespace Beckn\Core\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Beckn\Core\Api\Data\BecknQuoteMaskInterface;
use Beckn\Core\Setup\UpgradeSchema;

/**
 * Class BecknQuoteMask
 * @author Indglobal
 * @package Beckn\Core\Model
 */
class BecknQuoteMask extends \Magento\Framework\Model\AbstractModel implements BecknQuoteMaskInterface, IdentityInterface
{
    const NOROUTE_ENTITY_ID = "no-route";
    const CACHE_TAG = UpgradeSchema::TABLE_BECKN_QUOTE_ID;

    protected $_cacheTag = UpgradeSchema::TABLE_BECKN_QUOTE_ID;
    protected $_eventPrefix = UpgradeSchema::TABLE_BECKN_QUOTE_ID;

    /**
     * BecknQuoteMask constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init("Beckn\Core\Model\ResourceModel\BecknQuoteMask");
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\AbstractResource|\Magento\Framework\Model\ResourceModel\Db\AbstractDb|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * @param int $id
     * @param null $field
     * @return BecknQuoteMask
     */
    public function load($id, $field = null)
    {
        if ($id === null)
            return $this->noRouteProduct();
        return parent::load($id, $field);
    }

    /**
     * @return BecknQuoteMask
     */
    public function noRouteProduct()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . "_" . $this->getId()];
    }

    /**
     * @param $transactionId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByTransactionId($transactionId)
    {
        $connection = $this->_getResource()->getConnection();
        $tableName = $connection->getTableName(UpgradeSchema::TABLE_BECKN_QUOTE_ID);
        $select = $connection->select()->from($tableName)->where('transaction_id = :transaction_id');
        $bind = [':transaction_id' => $transactionId];
        return $connection->fetchRow($select, $bind);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteId()
    {
        return parent::getData(self::QUOTE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * @inheritdoc
     */
    public function getMaskedId()
    {
        return parent::getData(self::MASKED_ID);
    }

    /**
     * @inheritdoc
     */
    public function setMaskedId($maskedId)
    {
        return $this->setData(self::MASKED_ID, $maskedId);
    }

    /**
     * @inheritdoc
     */
    public function getTransactionId()
    {
        return parent::getData(self::TRANSACTION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTransactionId($transactionId)
    {
        return $this->setData(self::TRANSACTION_ID, $transactionId);
    }

    /**
     * @inheritdoc
     */
    public function getRequestBody()
    {
        return parent::getData(self::REQUEST_BODY);
    }

    /**
     * @inheritdoc
     */
    public function setRequestBody($requestBody)
    {
        return $this->setData(self::REQUEST_BODY, $requestBody);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return parent::getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

}