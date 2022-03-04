<?php

namespace Beckn\Core\Api\Data;

/**
 * Interface PricePolicyInterface
 * @author Indglobal
 * @package Beckn\Core\Api\Data
 */
interface ProductFlagReferenceInterface {

    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ENTITY_ID = "entity_id";
    const PRODUCT_ID = "product_id";
    const PRODUCT_SKU = "product_sku";
    const FLAG = "flag";
    const PRODUCT_LIST_ID = "product_list_id";
    const BLOCKHASH = "blockhash";
    const CREATED_AT = "created_at";
    const UPDATED_AT = "updated_at";

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param $entityId
     * @return int
     */
    public function setEntityId($entityId);

    /**
     * @return mixed
     */
    public function getProductId();

    /**
     * @param $productId
     * @return mixed
     */
    public function setProductId($productId);

    /**
     * @return string
     */
    public function getProductSku();

    /**
     * @param $productSku
     * @return mixed
     */
    public function setProductSku($productSku);

    /**
     * @return mixed
     */
    public function getFlag();

    /**
     * @param $flag
     * @return mixed
     */
    public function setFlag($flag);

    /**
     * @return string
     */
    public function getProductListId();

    /**
     * @param $productListId
     * @return string
     */
    public function setProductListId($productListId);

    /**
     * @return string
     */
    public function getBlockhash();

    /**
     * @param $blockhash
     * @return string
     */
    public function setBlockhash($blockhash);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param $createdAt
     * @return string
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param $updatedAt
     * @return string
     */
    public function setUpdatedAt($updatedAt);
}