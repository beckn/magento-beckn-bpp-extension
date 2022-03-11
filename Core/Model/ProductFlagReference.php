<?php

namespace Beckn\Core\Model;

use Beckn\Core\Api\Data\ProductFlagReferenceInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Beckn\Core\Setup\UpgradeSchema;

class ProductFlagReference extends \Magento\Framework\Model\AbstractModel implements ProductFlagReferenceInterface, IdentityInterface
{
    const CACHE_TAG = UpgradeSchema::TABLE_BECKN_FLAG_REFERENCE;

    protected $_cacheTag = UpgradeSchema::TABLE_BECKN_FLAG_REFERENCE;

    protected $_eventPrefix = UpgradeSchema::TABLE_BECKN_FLAG_REFERENCE;

    protected function _construct()
    {
        $this->_init('Beckn\Core\Model\ResourceModel\ProductFlagReference');
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritDoc
     */
    public function getEntityId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return parent::getData(self::PRODUCT_ID);
    }

    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @inheritDoc
     */
    public function getProductSku()
    {
        return parent::getData(self::PRODUCT_SKU);
    }

    /**
     * @inheritDoc
     */
    public function setProductSku($productSku)
    {
        return $this->setData(self::PRODUCT_SKU, $productSku);
    }

    /**
     * @inheritDoc
     */
    public function getFlag()
    {
        return parent::getData(self::FLAG);
    }

    /**
     * @inheritDoc
     */
    public function setFlag($flag)
    {
        return $this->setData(self::FLAG, $flag);
    }

    /**
     * @inheritDoc
     */
    public function getProductListId()
    {
        return parent::getData(self::PRODUCT_LIST_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductListId($productListId)
    {
        return $this->setData(self::PRODUCT_LIST_ID, $productListId);
    }

    /**
     * @inheritDoc
     */
    public function getBlockhash()
    {
        return parent::getData(self::BLOCKHASH);
    }

    /**
     * @inheritDoc
     */
    public function setBlockhash($blockhash)
    {
        return $this->setData(self::BLOCKHASH, $blockhash);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @param $productId
     * @param bool $withData
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function productLoadById($productId, $withData = false)
    {
        $connection = $this->_getResource()->getConnection();
        $tableName = $connection->getTableName(UpgradeSchema::TABLE_BECKN_FLAG_REFERENCE);
        $select = $connection->select()->from($tableName)->where('product_id = :product_id');
        $bind = [':product_id' => $productId];
        $data = $connection->fetchRow($select, $bind);
        if ($withData) {
            return $data;
        } else {
            if (!empty($data)) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * @param $productSku
     * @param bool $withData
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function productCheckBySku($productSku, $withData = false)
    {
        $connection = $this->_getResource()->getConnection();
        $tableName = $connection->getTableName(UpgradeSchema::TABLE_BECKN_FLAG_REFERENCE);
        $select = $connection->select()->from($tableName)->where('product_sku = :sku');
        $bind = [':sku' => $productSku];
        $data = $connection->fetchRow($select, $bind);
        if ($withData) {
            return $data;
        } else {
            if (!empty($data)) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * @param $sku
     * @param bool $withData
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function productLoadBySku($sku, $withData = false)
    {
        $connection = $this->_getResource()->getConnection();
        $tableName = $connection->getTableName(UpgradeSchema::TABLE_BECKN_FLAG_REFERENCE);
        $select = $connection->select()->from($tableName)->where('product_sku = :sku');
        $bind = [':sku' => $sku];
        $data = $connection->fetchRow($select, $bind);
        if ($withData) {
            return $data;
        } else {
            if (!empty($data)) {
                return true;
            } else {
                return false;
            }
        }
    }
}