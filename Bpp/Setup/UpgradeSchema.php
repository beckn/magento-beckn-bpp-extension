<?php

namespace Beckn\Bpp\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 * @author Indglobal
 * @package Beckn\Bpp\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    const TABLE_NAME = 'beckn_quote_id_mask';

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.2', '<=')) {
            $installer = $setup;
            $installer->startSetup();
            $table = $installer->getConnection()
                ->newTable($installer->getTable(self::TABLE_NAME))
                ->addColumn("entity_id", Table::TYPE_INTEGER, null, ["identity" => true, "unsigned" => true, "nullable" => false, "primary" => true], "Entity Id")
                ->addColumn("quote_id", Table::TYPE_INTEGER, 10, ["nullable" => true, "unsigned" => true], "Quote Id")
                ->addColumn("masked_id", Table::TYPE_TEXT, 32, ["nullable" => true, "default" => null], "Masked Id")
                ->addColumn("transaction_id", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Transaction Id")
                ->addColumn("status", Table::TYPE_INTEGER, 2, ["nullable" => false, "default" => 1], "Status")
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE], 'Updated At')
                ->setComment("BECKN_QUOTE_ID_MASK")
                ->addIndex(
                    $installer->getIdxName(self::TABLE_NAME, ['quote_id']),
                    ['quote_id']
                )->addIndex(
                    $installer->getIdxName(self::TABLE_NAME, ['masked_id']),
                    ['masked_id']
                );
            $installer->getConnection()->createTable($table);
            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '1.0.3', '<=')) {
            $installer = $setup;
            $installer->startSetup();
            $connection = $installer->getConnection();
            if ($connection->tableColumnExists(self::TABLE_NAME, 'request_body') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable('beckn_quote_id_mask'), 'request_body', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 0,
                        'after' => 'transaction_id',
                        'default' => null,
                        'comment' => 'Request Body',
                    ]
                );
            }
            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '1.0.4', '<=')) {
            $installer = $setup;
            $installer->startSetup();
            $table = $installer->getConnection()
                ->newTable($installer->getTable('beckn_lookup'))
                ->addColumn("entity_id", Table::TYPE_INTEGER, null, ["identity" => true, "unsigned" => true, "nullable" => false, "primary" => true], "Entity Id")
                ->addColumn("subscriber_id", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Subscriber Id")
                ->addColumn("subscriber_url", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Subscriber URL")
                ->addColumn("type", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Type")
                ->addColumn("domain", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Domain")
                ->addColumn("city", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "City")
                ->addColumn("country", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Country")
                ->addColumn("signing_public_key", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Signin Public Key")
                ->addColumn("encr_public_key", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Encrypt Public Key")
                ->addColumn("valid_from", Table::TYPE_DATETIME, null, ["nullable" => true, "default" => null], "Valid From")
                ->addColumn("valid_until", Table::TYPE_DATETIME, null, ["nullable" => true, "default" => null], "Valid Until")
                ->addColumn("status", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Status")
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE], 'Updated At');
            $installer->getConnection()->createTable($table);
            $installer->endSetup();
        }
    }
}