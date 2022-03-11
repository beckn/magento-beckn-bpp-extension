<?php

namespace Beckn\Core\Setup;

use Beckn\Core\Setup\InstallData as InstallData;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product;

/**
 * Class UpgradeData
 * @author Indglobal
 * @package Beckn\Core\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    const RANGE_START_DATE = 'time_range_start_date_'.InstallData::SUFFIX;
    const PRICE_POLICY = 'price_policy_'.InstallData::SUFFIX;
    const LOCATION_POLICY = 'location_policy_'.InstallData::SUFFIX;
    const PRODUCT_STORE = 'product_store_'.InstallData::SUFFIX;
    const PRODUCT_LIST_ID = 'product_list_id_'.InstallData::SUFFIX;
    const BLOCK_HASH = 'block_hash_'.InstallData::SUFFIX;

    /**
     * @var EavSetupFactory
     */
    protected $_eavSetupFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig
    )
    {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_eavConfig = $eavConfig;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $attribute = $this->_eavConfig->getAttribute(Product::ENTITY, self::RANGE_START_DATE);
            $attributeCheck = ($attribute && $attribute->getId());
            if(!$attributeCheck){
                $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
                /**
                 * @var \Magento\Eav\Setup\EavSetup $eavSetup
                 */
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    self::RANGE_START_DATE,
                    [
                        'type' => 'datetime',
                        'backend' => 'Magento\Catalog\Model\Attribute\Backend\Startdate',
                        'frontend' => '',
                        'label' => 'Item Time Range Start Date',
                        'group' => 'Beckn Configuration',
                        'input' => 'datetime',
                        'class' => '',
                        'source' => '',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => true,
                        'default' => null,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'used_in_product_listing' => true,
                        'unique' => false,
                        'apply_to' => 'simple,configurable,virtual,downloadable,grouped,bundle',
                        'sort_order' => 40
                    ]
                );

                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'time_range_end_date_'.InstallData::SUFFIX,
                    [
                        'type' => 'datetime',
                        'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\Datetime',
                        'frontend' => '',
                        'label' => 'Item Time Range End Date',
                        'group' => 'Beckn Configuration',
                        'input' => 'datetime',
                        'class' => '',
                        'source' => '',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => true,
                        'default' => null,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'used_in_product_listing' => true,
                        'unique' => false,
                        'apply_to' => 'simple,configurable,virtual,downloadable,grouped,bundle',
                        'sort_order' => 60
                    ]
                );
            }
        }
        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $attribute = $this->_eavConfig->getAttribute(Product::ENTITY, self::PRICE_POLICY);
            $attributeCheck = ($attribute && $attribute->getId());
            if(!$attributeCheck){
                $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
                /**
                 * @var \Magento\Eav\Setup\EavSetup $eavSetup
                 */
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    self::PRICE_POLICY,
                    [
                        'type' => 'varchar',
                        'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                        'frontend' => '',
                        'label' => 'Dynamic Pricing Policy',
                        'group' => 'Beckn Configuration',
                        'input' => 'select',
                        'class' => '',
                        'source' => 'Beckn\Core\Model\Config\Source\PricePolicy',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => true,
                        'default' => null,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'used_in_product_listing' => true,
                        'unique' => false,
                        'apply_to' => 'simple,configurable,virtual,downloadable,grouped,bundle',
                        'sort_order' => 50
                    ]
                );
            }
        }
        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $attribute = $this->_eavConfig->getAttribute(Product::ENTITY, self::PRODUCT_STORE);
            $attributeCheck = ($attribute && $attribute->getId());
            if(!$attributeCheck){
                $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
                /**
                 * @var \Magento\Eav\Setup\EavSetup $eavSetup
                 */
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    self::PRODUCT_STORE,
                    [
                        'type' => 'varchar',
                        'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                        'frontend' => '',
                        'label' => 'Product Store',
                        'group' => 'Beckn Configuration',
                        'input' => 'select',
                        'class' => '',
                        'source' => 'Beckn\Core\Model\Config\Source\Store',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => true,
                        'default' => null,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'used_in_product_listing' => true,
                        'unique' => false,
                        'apply_to' => 'simple,configurable,virtual,downloadable,grouped,bundle',
                        'sort_order' => 60
                    ]
                );
            }
        }
        if (version_compare($context->getVersion(), '1.1.9', '<')) {
            $attribute = $this->_eavConfig->getAttribute(Product::ENTITY, self::PRODUCT_LIST_ID);
            $attributeCheck = ($attribute && $attribute->getId());
            if(!$attributeCheck){
                $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
                /**
                 * @var \Magento\Eav\Setup\EavSetup $eavSetup
                 */
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    self::PRODUCT_LIST_ID,
                    [
                        'group' => 'Beckn Configuration',
                        'type' => 'text',
                        'label' => 'Product List Id',
                        'input' => 'text',
                        'backend' => '',
                        'frontend' => '',
                        'class' => '',
                        'source' => '',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => true,
                        'default' => '',
                        'searchable' => true,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => true,
                        'unique' => false,
                        'apply_to' => 'simple,configurable,virtual,downloadable,grouped,bundle',
                        'used_in_product_listing' => true,
                        'sort_order' => 80
                    ]
                );
            }

            $attribute = $this->_eavConfig->getAttribute(Product::ENTITY, self::BLOCK_HASH);
            $attributeCheck = ($attribute && $attribute->getId());
            if(!$attributeCheck){
                $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
                /**
                 * @var \Magento\Eav\Setup\EavSetup $eavSetup
                 */
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    self::BLOCK_HASH,
                    [
                        'group' => 'Beckn Configuration',
                        'type' => 'text',
                        'label' => 'Block Hash',
                        'input' => 'text',
                        'backend' => '',
                        'frontend' => '',
                        'class' => '',
                        'source' => '',
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => true,
                        'default' => '',
                        'searchable' => true,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => true,
                        'unique' => false,
                        'apply_to' => 'simple,configurable,virtual,downloadable,grouped,bundle',
                        'used_in_product_listing' => true,
                        'sort_order' => 100
                    ]
                );
            }
        }
    }
}