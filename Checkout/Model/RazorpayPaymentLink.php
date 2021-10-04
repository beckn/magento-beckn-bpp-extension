<?php

namespace Beckn\Checkout\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Beckn\Checkout\Api\Data\RazorpayPaymentLinkInterface;
use Beckn\Checkout\Setup\UpgradeSchema;

/**
 * Class RazorpayPaymentLink
 * @author Indglobal
 * @package Beckn\Checkout\Model
 */
class RazorpayPaymentLink extends \Magento\Framework\Model\AbstractModel implements RazorpayPaymentLinkInterface, IdentityInterface
{
    const NOROUTE_ENTITY_ID = "no-route";
    const CACHE_TAG = UpgradeSchema::TABLE_BECKN_RAZORPAY;

    protected $_cacheTag = UpgradeSchema::TABLE_BECKN_RAZORPAY;
    protected $_eventPrefix = UpgradeSchema::TABLE_BECKN_RAZORPAY;

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
        $this->_init("Beckn\Checkout\Model\ResourceModel\RazorpayPaymentLink");
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
    public function getPaymentLink()
    {
        return parent::getData(self::PAYMENT_LINK);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentLink($paymentLink)
    {
        return $this->setData(self::PAYMENT_LINK, $paymentLink);
    }

    /**
     * @inheritdoc
     */
    public function getPaymentId()
    {
        return parent::getData(self::PAYMENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentId($paymentId)
    {
        return $this->setData(self::PAYMENT_ID, $paymentId);
    }

    /**
     * @inheritdoc
     */
    public function getFullResponse()
    {
        return parent::getData(self::FULL_RESPONSE);
    }

    /**
     * @inheritdoc
     */
    public function setFullResponse($fullResponse)
    {
        return $this->setData(self::FULL_RESPONSE, $fullResponse);
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
    public function getTransactionStatus()
    {
        return parent::getData(self::TRANSACTION_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setTransactionStatus($transactionStatus)
    {
        return $this->setData(self::TRANSACTION_STATUS, $transactionStatus);
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