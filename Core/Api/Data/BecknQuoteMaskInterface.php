<?php

namespace Beckn\Core\Api\Data;

/**
 * Interface BecknQuoteMaskInterface
 * @author Indglobal
 * @package Beckn\Core\Api\Data
 */
interface BecknQuoteMaskInterface {

    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ENTITY_ID = "entity_id";
    const QUOTE_ID = "quote_id";
    const MASKED_ID = "masked_id";
    const TRANSACTION_ID = "transaction_id";
    const REQUEST_BODY = "request_body";
    const STATUS = "status";
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
     * @return int
     */
    public function getQuoteId();

    /**
     * @param $quoteId
     * @return int
     */
    public function setQuoteId($quoteId);

    /**
     * @return string
     */
    public function getMaskedId();

    /**
     * @param $maskedId
     * @return string
     */
    public function setMaskedId($maskedId);

    /**
     * @return string
     */
    public function getTransactionId();

    /**
     * @param $transactionId
     * @return string
     */
    public function setTransactionId($transactionId);

    /**
     * @return mixed
     */
    public function getRequestBody();

    /**
     * @param $requestBody
     * @return mixed
     */
    public function setRequestBody($requestBody);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param $status
     * @return int
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param $updatedAt
     * @return string
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param $createdAt
     * @return string
     */
    public function setCreatedAt($createdAt);
}
