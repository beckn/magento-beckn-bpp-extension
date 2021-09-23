<?php

namespace Beckn\Core\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Beckn\Core\Api\Data\BecknLookupInterface;

/**
 * Class BecknLookup
 * @author Indglobal
 * @package Beckn\Core\Model
 */
class BecknLookup extends \Magento\Framework\Model\AbstractModel implements BecknLookupInterface, IdentityInterface
{
    const CACHE_TAG = 'beckn_lookup';

    protected $_cacheTag = 'beckn_lookup';

    protected $_eventPrefix = 'beckn_lookup';

    protected function _construct()
    {
        $this->_init('Beckn\Core\Model\ResourceModel\BecknLookup');
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
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getSubscriberId()
    {
        return parent::getData(self::SUBSCRIBER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setSubscriberId($subscriberId)
    {
        return $this->setData(self::SUBSCRIBER_ID, $subscriberId);
    }

    /**
     * @inheritdoc
     */
    public function getSubscriberUrl()
    {
        return parent::getData(self::SUBSCRIBER_URL);
    }

    /**
     * @inheritdoc
     */
    public function setSubscriberUrl($subscriberUrl)
    {
        return $this->setData(self::SUBSCRIBER_URL, $subscriberUrl);
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return parent::getData(self::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritdoc
     */
    public function getDomain()
    {
        return parent::getData(self::DOMAIN);
    }

    /**
     * @inheritdoc
     */
    public function setDomain($domain)
    {
        return $this->setData(self::DOMAIN, $domain);
    }

    /**
     * @inheritdoc
     */
    public function getCity()
    {
        return parent::getData(self::CITY);
    }

    /**
     * @inheritdoc
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * @inheritdoc
     */
    public function getCountry()
    {
        return parent::getData(self::COUNTRY);
    }

    /**
     * @inheritdoc
     */
    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }

    /**
     * @inheritdoc
     */
    public function getSigningPublicKey()
    {
        return parent::getData(self::SIGNING_PUBLIC_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setSigningPublicKey($signingPublicKey)
    {
        return $this->setData(self::SIGNING_PUBLIC_KEY, $signingPublicKey);
    }

    /**
     * @inheritdoc
     */
    public function getEncrPublicKey()
    {
        return parent::getData(self::ENCR_PUBLIC_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setEncrPublicKey($encrPublicKey)
    {
        return $this->setData(self::ENCR_PUBLIC_KEY, $encrPublicKey);
    }

    /**
     * @inheritdoc
     */
    public function getValidFrom()
    {
        return parent::getData(self::VALID_FROM);
    }

    /**
     * @inheritdoc
     */
    public function setValidFrom($validFrom)
    {
        return $this->setData(self::VALID_FROM, $validFrom);
    }

    /**
     * @inheritdoc
     */
    public function getValidUntil()
    {
        return parent::getData(self::VALID_UNTIL);
    }

    /**
     * @inheritdoc
     */
    public function setValidUntil($validUntil)
    {
        return $this->setData(self::VALID_UNTIL, $validUntil);
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