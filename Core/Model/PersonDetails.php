<?php

namespace Beckn\Core\Model;

use Beckn\Core\Api\Data\PersonDetailsInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Model\ResourceModel\PersonDetails as ResourceModelPersonDetails;

/**
 * Class PersonDetails
 * @package Beckn\Core\Model
 */
class PersonDetails extends \Magento\Framework\Model\AbstractModel implements PersonDetailsInterface, IdentityInterface
{

    const CACHE_TAG = UpgradeSchema::TABLE_PERSONS_DETAILS;

    protected $_cacheTag = UpgradeSchema::TABLE_PERSONS_DETAILS;

    protected $_eventPrefix = UpgradeSchema::TABLE_PERSONS_DETAILS;

    /**
     * @var ResourceModel\PolicyRequest\CollectionFactory
     */
    protected $_requestBodyCollectionFactory;

    protected function _construct(){
        $this->_init(ResourceModelPersonDetails::class);
    }

    /**
     * PricePolicy constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param ResourceModel\PersonDetails\CollectionFactory $requestBodyCollectionFactory
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
     * @return LocationPolicy|int
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
     * @return array|mixed|null
     */
    public function getGender()
    {
        return parent::getData(self::GENDER);
    }

    /**
     * @param $gender
     * @return PersonDetails|mixed
     */
    public function setGender($gender)
    {
        return $this->setData(self::GENDER, $gender);
    }

    /**
     * @return array|mixed|null
     */
    public function getImage()
    {
        return parent::getData(self::IMAGE);
    }

    /**
     * @param $image
     * @return PersonDetails|mixed
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @return array|mixed|null
     */
    public function getCred()
    {
        return parent::getData(self::CRED);
    }

    /**
     * @param $cred
     * @return PersonDetails|mixed
     */
    public function setCred($cred)
    {
        return $this->setData(self::CRED, $cred);
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