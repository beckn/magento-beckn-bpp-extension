<?php

namespace Beckn\Core\Model;

use Beckn\Core\Api\Data\PolicyRequestInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Model\ResourceModel\PolicyRequest as ResourceModelPolicyRequest;

/**
 * Class PolicyRequest
 * @author Indglobal
 * @package Beckn\Core\Model
 */
class PolicyRequest extends \Magento\Framework\Model\AbstractModel implements PolicyRequestInterface, IdentityInterface
{

    const CACHE_TAG = UpgradeSchema::TABLE_POLICY_REQUEST;

    protected $_cacheTag = UpgradeSchema::TABLE_POLICY_REQUEST;

    protected $_eventPrefix = UpgradeSchema::TABLE_POLICY_REQUEST;

    protected function _construct()
    {
        $this->_init(ResourceModelPolicyRequest::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
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
     * @return PolicyRequest|int
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @return string
     */
    public function getPolicyId()
    {
        return parent::getData(self::POLICY_ID);
    }

    /**
     * @param $name
     * @return string
     */
    public function setPolicyId($policyId)
    {
        return $this->setData(self::POLICY_ID, $policyId);
    }

    /**
     * @return string
     */
    public function getPolicyType()
    {
        return parent::getData(self::POLICY_TYPE);
    }

    /**
     * @param $policyType
     * @return PolicyRequest|string
     */
    public function setPolicyType($policyType)
    {
        return $this->setData(self::POLICY_TYPE, $policyType);
    }

    /**
     * @return string
     */
    public function getRequestType()
    {
        return parent::getData(self::REQUEST_TYPE);
    }

    /**
     * @param $requestType
     * @return PolicyRequest|string
     */
    public function setRequestType($requestType)
    {
        return $this->setData(self::REQUEST_TYPE, $requestType);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return parent::getData(self::KEY);
    }

    /**
     * @param $key
     * @return string
     */
    public function setKey($key)
    {
        return $this->setData(self::KEY, $key);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return parent::getData(self::VALUE);
    }

    /**
     * @param $value
     * @return string
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * @return string
     */
    public function getValueType()
    {
        return parent::getData(self::VALUE_TYPE);
    }

    /**
     * @param $valueType
     * @return string
     */
    public function setValueType($valueType)
    {
        return $this->setData(self::VALUE_TYPE, $valueType);
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