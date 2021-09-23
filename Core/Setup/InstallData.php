<?php

namespace Beckn\Core\Setup;

use Magento\Catalog\Model\Product;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class InstallData
 * @author Indglobal
 * @package Beckn\Core\Setup
 */
class InstallData implements InstallDataInterface
{
    const SUFFIX = "bpp";
    const ITEM_CODE = 'item_code_' . self::SUFFIX;
    /**
     * @var EavSetupFactory
     */
    protected $_eavSetupFactory;
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * InstallData constructor.
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
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $attribute = $this->_eavConfig->getAttribute(Product::ENTITY, self::ITEM_CODE);
        $attributeCheck = ($attribute && $attribute->getId());
        if (!$attributeCheck) {
            $eavSetup = $this->_eavSetupFactory->create(["setup" => $setup]);
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                self::ITEM_CODE,
                [
                    'group' => 'Beckn Configuration',
                    'type' => 'text',
                    'label' => 'Item Code',
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
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'unique' => false,
                    'apply_to' => 'simple,configurable,virtual,downloadable,grouped,bundle',
                    'used_in_product_listing' => true,
                    'sort_order' => 10
                ]
            );
        }
    }
}