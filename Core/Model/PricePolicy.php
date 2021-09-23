<?php

namespace Beckn\Core\Model;

use Beckn\Core\Api\Data\PolicyRequestInterface;
use Beckn\Core\Api\Data\PricePolicyInterface;
use Beckn\Core\Model\Config\Source\PolicyType;
use Beckn\Core\Model\Config\Source\RequestType;
use Beckn\Core\Model\ResourceModel\PolicyRequest\Collection;
use Magento\Framework\DataObject\IdentityInterface;
use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Model\ResourceModel\PricePolicy as ResourceModelPricePolicy;

/**
 * Class PricePolicy
 * @author Indglobal
 * @package Beckn\Core\Model
 */
class PricePolicy extends \Magento\Framework\Model\AbstractModel implements PricePolicyInterface, IdentityInterface
{

    const CACHE_TAG = UpgradeSchema::TABLE_BECKN_PRICE_POLICY;

    protected $_cacheTag = UpgradeSchema::TABLE_BECKN_PRICE_POLICY;

    protected $_eventPrefix = UpgradeSchema::TABLE_BECKN_PRICE_POLICY;

    /**
     * @var ResourceModel\PolicyRequest\CollectionFactory
     */
    protected $_requestBodyCollectionFactory;

    protected function _construct(){
        $this->_init(ResourceModelPricePolicy::class);
    }

    /**
     * PricePolicy constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param ResourceModel\PolicyRequest\CollectionFactory $requestBodyCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        ResourceModel\PolicyRequest\CollectionFactory $requestBodyCollectionFactory,
        array $data = []
    )
    {
        $this->_requestBodyCollectionFactory = $requestBodyCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    /**
     * @return ResourceModel\PolicyRequest\Collection
     */
    public function getRequestBody(){
        /**
         * @var Collection $collection
         */
        $collection = $this->_requestBodyCollectionFactory->create();
        $collection->addFieldToFilter(PolicyRequestInterface::POLICY_TYPE, PolicyType::POLICY_TYPE['price_policy']);
        $collection->addFieldToFilter(PolicyRequestInterface::REQUEST_TYPE, RequestType::BODY);
        $collection->addFieldToFilter(PolicyRequestInterface::POLICY_ID, $this->getEntityId());
        return $collection;
    }

    /**
     * @return ResourceModel\PolicyRequest\Collection
     */
    public function getRequestHeader(){
        /**
         * @var Collection $collection
         */
        $collection = $this->_requestBodyCollectionFactory->create();
        $collection->addFieldToFilter(PolicyRequestInterface::POLICY_TYPE, PolicyType::POLICY_TYPE['price_policy']);
        $collection->addFieldToFilter(PolicyRequestInterface::REQUEST_TYPE, RequestType::HEADER);
        $collection->addFieldToFilter(PolicyRequestInterface::POLICY_ID, $this->getEntityId());
        return $collection;
    }

    /**
     * @inheritdoc
     */

    public function getEntityId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * @param int $entityId
     * @return PricePolicy|int
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return parent::getData(self::NAME);
    }

    /**
     * @param $name
     * @return string
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return parent::getData(self::API_URL);
    }

    /**
     * @param $apiUrl
     * @return string
     */
    public function setApiUrl($apiUrl)
    {
        return $this->setData(self::API_URL, $apiUrl);
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return parent::getData(self::METHOD);
    }

    /**
     * @param $method
     * @return string
     */
    public function setMethod($method)
    {
        return $this->setData(self::METHOD, $method);
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return parent::getData(self::API_KEY);
    }

    /**
     * @param $apiKey
     * @return string
     */
    public function setApiKey($apiKey)
    {
        return $this->setData(self::API_KEY, $apiKey);
    }

    /**
     * @return string
     */
    public function getHeaders()
    {
        return parent::getData(self::HEADERS);
    }

    /**
     * @param $headers
     * @return string
     */
    public function setHeaders($headers)
    {
        return $this->setData(self::HEADERS, $headers);
    }

    /**
     * @return string
     */
    public function getResponseBodyPath()
    {
        return parent::getData(self::RESPONSE_BODY_PATH);
    }

    /**
     * @param $responseBodyPath
     * @return string
     */
    public function setResponseBodyPath($responseBodyPath)
    {
        return $this->setData(self::RESPONSE_BODY_PATH, $responseBodyPath);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * @param $createdAt
     * @return string
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * @param $updatedAt
     * @return string
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}