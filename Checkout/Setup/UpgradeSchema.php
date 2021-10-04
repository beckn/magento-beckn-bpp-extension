<?php

namespace Beckn\Checkout\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 * @author Indglobal
 * @package Beckn\Checkout\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    const TABLE_BECKN_RAZORPAY = 'beckn_razorpay_payment_link';
    const DEFAULT_ORDER_TYPE = 'store';

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<=') && !$setup->getConnection()->isTableExists(self::TABLE_BECKN_RAZORPAY)) {
            $installer = $setup;
            $installer->startSetup();
            $table = $installer->getConnection()
                ->newTable($installer->getTable(self::TABLE_BECKN_RAZORPAY))
                ->addColumn("entity_id", Table::TYPE_INTEGER, null, ["identity" => true, "unsigned" => true, "nullable" => false, "primary" => true], "Entity Id")
                ->addColumn("quote_id", Table::TYPE_INTEGER, 10, ["nullable" => true, "unsigned" => true], "Quote Id")
                ->addColumn("payment_link", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Payment Link")
                ->addColumn("payment_id", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Payment Id")
                ->addColumn("full_response", Table::TYPE_TEXT, 0, ["nullable" => true, "default" => null], "Full Response")
                ->addColumn("status", Table::TYPE_INTEGER, 2, ["nullable" => true, "default" => 0], "Status")
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE], 'Updated At')
                ->setComment("BECKN_RAZORPAY_PAYMENT_LINK")
                ->addIndex(
                    $installer->getIdxName(self::TABLE_BECKN_RAZORPAY, ['quote_id']),
                    ['quote_id']
                )->addIndex(
                    $installer->getIdxName(self::TABLE_BECKN_RAZORPAY, ['payment_id']),
                    ['payment_id']
                );
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'), 'order_type', [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'length' => 255,
                    'default' => self::DEFAULT_ORDER_TYPE,
                    'comment' => 'Order Type',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'), 'payment_status', [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => 255,
                    'default' => null,
                    'comment' => 'Payment Status',
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_grid'), 'order_type', [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => self::DEFAULT_ORDER_TYPE,
                    'length' => 255,
                    'comment' => 'Order Type',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_grid'), 'payment_status', [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => 255,
                    'default' => null,
                    'comment' => 'Payment Status',
                ]
            );
            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.2', '<=')) {
            $installer = $setup;
            $installer->startSetup();
            $connection = $installer->getConnection();
            if ($connection->tableColumnExists("sales_order_address", 'beckn_customer_address') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable('sales_order_address'), 'beckn_customer_address', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 0,
                        'default' => null,
                        'comment' => 'Beckn Customer Address',
                    ]
                );
            }
            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.3', '<=')) {
            $installer = $setup;
            $installer->startSetup();
            $connection = $installer->getConnection();
            if ($connection->tableColumnExists(self::TABLE_BECKN_RAZORPAY, 'transaction_status') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_BECKN_RAZORPAY), 'transaction_status', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'after' => "status",
                        'default' => null,
                        'comment' => 'Transaction Status',
                    ]
                );
            }
            $installer->endSetup();
        }
    }
}