<?php

namespace Beckn\Core\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\DB\Ddl\Table;
use Beckn\Core\Model\Config\Source\ValueType;

/**
 * Class UpgradeSchema
 * @author Indglobal
 * @package Beckn\Core\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    const TABLE_BECKN_QUOTE_ID = 'beckn_quote_id_mask';
    const TABLE_BECKN_LOOKUP = 'beckn_lookup';
    const TABLE_BECKN_PRICE_POLICY = 'beckn_price_policy';
    const TABLE_BECKN_LOCATION_POLICY = 'beckn_location_policy';
    const TABLE_POLICY_REQUEST = 'beckn_policy_request';
    const TABLE_BECKN_FULFILLMENT_POLICY = 'beckn_fulfillment_policy';
    const TABLE_BECKN_FULFILLMENT_STATUS = 'beckn_fulfillment_status';
    const TABLE_STORE = 'store';

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.2', '<=') && !$setup->getConnection()->isTableExists(self::TABLE_BECKN_QUOTE_ID)) {
            $installer = $setup;
            $installer->startSetup();
            $table = $installer->getConnection()
                ->newTable($installer->getTable(self::TABLE_BECKN_QUOTE_ID))
                ->addColumn("entity_id", Table::TYPE_INTEGER, null, ["identity" => true, "unsigned" => true, "nullable" => false, "primary" => true], "Entity Id")
                ->addColumn("quote_id", Table::TYPE_INTEGER, 10, ["nullable" => true, "unsigned" => true], "Quote Id")
                ->addColumn("masked_id", Table::TYPE_TEXT, 32, ["nullable" => true, "default" => null], "Masked Id")
                ->addColumn("transaction_id", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Transaction Id")
                ->addColumn("status", Table::TYPE_INTEGER, 2, ["nullable" => false, "default" => 1], "Status")
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE], 'Updated At')
                ->setComment("BECKN_QUOTE_ID_MASK")
                ->addIndex(
                    $installer->getIdxName(self::TABLE_BECKN_QUOTE_ID, ['quote_id']),
                    ['quote_id']
                )->addIndex(
                    $installer->getIdxName(self::TABLE_BECKN_QUOTE_ID, ['masked_id']),
                    ['masked_id']
                );
            $installer->getConnection()->createTable($table);
            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '1.0.3', '<=')) {
            $installer = $setup;
            $installer->startSetup();
            $connection = $installer->getConnection();
            if ($connection->tableColumnExists(self::TABLE_BECKN_QUOTE_ID, 'request_body') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_BECKN_QUOTE_ID), 'request_body', [
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
        if (version_compare($context->getVersion(), '1.0.4', '<=') && !$setup->getConnection()->isTableExists(self::TABLE_BECKN_LOOKUP)) {
            $installer = $setup;
            $installer->startSetup();
            $table = $installer->getConnection()
                ->newTable($installer->getTable(self::TABLE_BECKN_LOOKUP))
                ->addColumn("entity_id", Table::TYPE_INTEGER, null, ["identity" => true, "unsigned" => true, "nullable" => false, "primary" => true], "Entity Id")
                ->addColumn("subscriber_id", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Subscriber Id")
                ->addColumn("subscriber_url", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Subscriber URL")
                ->addColumn("type", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Type")
                ->addColumn("domain", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Domain")
                ->addColumn("city", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "City")
                ->addColumn("country", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Country")
                ->addColumn("signing_public_key", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Signing Public Key")
                ->addColumn("encr_public_key", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Encrypt Public Key")
                ->addColumn("valid_from", Table::TYPE_DATETIME, null, ["nullable" => true, "default" => null], "Valid From")
                ->addColumn("valid_until", Table::TYPE_DATETIME, null, ["nullable" => true, "default" => null], "Valid Until")
                ->addColumn("status", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Status")
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE], 'Updated At')
                ->setComment("BECKN LOOKUP");
            $installer->getConnection()->createTable($table);
            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.5', '<=') && !$setup->getConnection()->isTableExists(self::TABLE_BECKN_PRICE_POLICY)) {
            $installer = $setup;
            $installer->startSetup();
            $table = $installer->getConnection()
                ->newTable($installer->getTable(self::TABLE_BECKN_PRICE_POLICY))
                ->addColumn("entity_id", Table::TYPE_INTEGER, null, ["identity" => true, "unsigned" => true, "nullable" => false, "primary" => true], "Entity Id")
                ->addColumn("name", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Name")
                ->addColumn("api_url", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "API URL")
                ->addColumn("method", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Method")
                ->addColumn("api_key", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "API KEY")
                ->addColumn("headers", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Headers")
                ->addColumn("response_body_path", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Response Body Path")
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE], 'Updated At')
                ->setComment("Beckn Price Policy");
            $installer->getConnection()->createTable($table);
            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.5', '<=') && !$setup->getConnection()->isTableExists(self::TABLE_POLICY_REQUEST)) {
            $installer = $setup;
            $installer->startSetup();
            $table = $installer->getConnection()
                ->newTable($installer->getTable(self::TABLE_POLICY_REQUEST))
                ->addColumn("entity_id", Table::TYPE_INTEGER, null, ["identity" => true, "unsigned" => true, "nullable" => false, "primary" => true], "Entity Id")
                ->addColumn("policy_id", Table::TYPE_INTEGER, null, ["unsigned" => true, "nullable" => false], "Policy ID")
                ->addColumn("policy_type", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Policy Type")
                ->addColumn("request_type", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Request Type")
                ->addColumn("key", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Key")
                ->addColumn("value", Table::TYPE_TEXT, null, ["nullable" => true, "default" => null], "Value")
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE], 'Updated At')
                ->setComment("Policy Request");
            $installer->getConnection()->createTable($table);
            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.6', '<=') && !$setup->getConnection()->isTableExists(self::TABLE_BECKN_LOCATION_POLICY)) {
            $installer = $setup;
            $installer->startSetup();
            $table = $installer->getConnection()
                ->newTable($installer->getTable(self::TABLE_BECKN_LOCATION_POLICY))
                ->addColumn("entity_id", Table::TYPE_INTEGER, null, ["identity" => true, "unsigned" => true, "nullable" => false, "primary" => true], "Entity Id")
                ->addColumn("name", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Name")
                ->addColumn("api_url", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "API URL")
                ->addColumn("method", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Method")
                ->addColumn("api_key", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "API KEY")
                ->addColumn("headers", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Headers")
                ->addColumn("response_body_path", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Response Body Path")
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE], 'Updated At')
                ->setComment("Beckn Location Policy");
            $installer->getConnection()->createTable($table);
            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.7', '<=') && !$setup->getConnection()->isTableExists(self::TABLE_BECKN_FULFILLMENT_POLICY)) {
            $installer = $setup;
            $installer->startSetup();
            $table = $installer->getConnection()
                ->newTable($installer->getTable(self::TABLE_BECKN_FULFILLMENT_POLICY))
                ->addColumn("entity_id", Table::TYPE_INTEGER, null, ["identity" => true, "unsigned" => true, "nullable" => false, "primary" => true], "Entity Id")
                ->addColumn("name", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Name")
                ->addColumn("type", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Type")
                ->addColumn("center", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Center")
                ->addColumn("radius", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Radius")
                ->addColumn("agents", Table::TYPE_INTEGER, 2, ["nullable" => true, "default" => null], "Agents")
                ->addColumn("vehicles", Table::TYPE_INTEGER, 2, ["nullable" => true, "default" => null], "Vehicles")
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE], 'Updated At')
                ->setComment("Beckn Fulfillment Policy");
            $installer->getConnection()->createTable($table);

            $installer->startSetup();
            $table = $installer->getConnection()
                ->newTable($installer->getTable(self::TABLE_BECKN_FULFILLMENT_STATUS))
                ->addColumn("entity_id", Table::TYPE_INTEGER, null, ["identity" => true, "unsigned" => true, "nullable" => false, "primary" => true], "Entity Id")
                ->addColumn("location_id", Table::TYPE_INTEGER, null, ["unsigned" => true, "nullable" => false], "Location Id")
                ->addColumn("status", Table::TYPE_TEXT, 255, ["nullable" => true, "default" => null], "Status")
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE], 'Updated At')
                ->addForeignKey(
                    $setup->getFkName(
                        self::TABLE_BECKN_FULFILLMENT_STATUS,
                        'location_id',
                        self::TABLE_BECKN_FULFILLMENT_POLICY,
                        'entity_id'
                    ),
                    'location_id',
                    $setup->getTable(self::TABLE_BECKN_FULFILLMENT_POLICY),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment("Beckn Fulfillment Status");
            $installer->getConnection()->createTable($table);
            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.1.0', '<=')) {
            $installer = $setup;
            $installer->startSetup();
            $connection = $installer->getConnection();
            if ($connection->tableColumnExists(self::TABLE_STORE, 'gps_location') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'gps_location', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'comment' => 'GPS Location',
                    ]
                );
            }
            if ($connection->tableColumnExists(self::TABLE_STORE, 'location_id') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'location_id', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'after' => "gps_location",
                        'comment' => 'Policy Location',
                    ]
                );
            }
            if ($connection->tableColumnExists(self::TABLE_STORE, 'fulfillment_id') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'fulfillment_id', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'after' => "location_id",
                        'comment' => 'Fulfillment Id',
                    ]
                );
            }
            if ($connection->tableColumnExists(self::TABLE_STORE, 'address_door') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'door', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'comment' => 'Address Door',
                    ]
                );
            }
            if ($connection->tableColumnExists(self::TABLE_STORE, 'address_name') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'address_name', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'comment' => 'Address Name',
                    ]
                );
            }
            if ($connection->tableColumnExists(self::TABLE_STORE, 'address_building') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'address_building', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'comment' => 'Address Building',
                    ]
                );
            }
            if ($connection->tableColumnExists(self::TABLE_STORE, 'address_street') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'address_street', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'comment' => 'Address Street',
                    ]
                );
            }
            if ($connection->tableColumnExists(self::TABLE_STORE, 'address_locality') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'address_locality', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'comment' => 'Address Locality',
                    ]
                );
            }
            if ($connection->tableColumnExists(self::TABLE_STORE, 'address_state') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'address_state', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'comment' => 'Address State',
                    ]
                );
            }
            if ($connection->tableColumnExists(self::TABLE_STORE, 'address_country') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'address_country', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'comment' => 'Address Country',
                    ]
                );
            }
            if ($connection->tableColumnExists(self::TABLE_STORE, 'address_area_code') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'address_area_code', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'comment' => 'Address Area Code',
                    ]
                );
            }
            if ($connection->tableColumnExists(self::TABLE_STORE, 'address_station_code') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'address_station_code', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'comment' => 'Address Station Code',
                    ]
                );
            }
            if ($connection->tableColumnExists(self::TABLE_STORE, 'address_city_name') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'address_city_name', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'comment' => 'Address City Name',
                    ]
                );
            }
            if ($connection->tableColumnExists(self::TABLE_STORE, 'address_city_code') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_STORE), 'address_city_code', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => null,
                        'comment' => 'Address City Code',
                    ]
                );
            }
            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.1.2', '<=')) {
            $installer = $setup;
            $installer->startSetup();
            $connection = $installer->getConnection();
            if ($connection->tableColumnExists(self::TABLE_POLICY_REQUEST, 'value_type') === false) {
                $installer->getConnection()->addColumn(
                    $installer->getTable(self::TABLE_POLICY_REQUEST), 'value_type', [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'size' => 255,
                        'default' => ValueType::TYPE_BODY_PATH,
                        'after' => 'value',
                        'comment' => 'Value Type',
                    ]
                );
            }
            $installer->endSetup();
        }
    }
}