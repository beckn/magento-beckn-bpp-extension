<?php

namespace Beckn\Bpp\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class InstallData
 * @author Indglobal
 * @package Beckn\Bpp\Setup
 */
class InstallData implements InstallDataInterface
{
    const SUFFIX = "bpp";
    /**
     * @var EavSetupFactory
     */
    protected $_eavSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->_eavSetupFactory->create(["setup" => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'item_code_'.self::SUFFIX,
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