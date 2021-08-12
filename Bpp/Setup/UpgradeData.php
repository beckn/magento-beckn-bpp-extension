<?php

namespace Beckn\Bpp\Setup;

use Beckn\Bpp\Setup\InstallData as InstallData;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class UpgradeData
 * @author Indglobal
 * @package Beckn\Bpp\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $_eavSetupFactory;

    /**
     * UpgradeData constructor.
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
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'time_range_start_date_'.InstallData::SUFFIX,
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
}