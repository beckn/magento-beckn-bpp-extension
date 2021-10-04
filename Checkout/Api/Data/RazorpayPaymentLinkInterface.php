<?php

namespace Beckn\Checkout\Api\Data;

/**
 * Interface RazorpayPaymentLinkInterface
 * @author Indglobal
 * @package Beckn\Checkout\Api\Data
 */
interface RazorpayPaymentLinkInterface
{

    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ENTITY_ID = "entity_id";
    const QUOTE_ID = "quote_id";
    const PAYMENT_LINK = "payment_link";
    const PAYMENT_ID = "payment_id";
    const FULL_RESPONSE = "full_response";
    const STATUS = "status";
    const TRANSACTION_STATUS = "transaction_status";
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
    public function getPaymentLink();

    /**
     * @param $paymentLink
     * @return string
     */
    public function setPaymentLink($paymentLink);

    /**
     * @return string
     */
    public function getPaymentId();

    /**
     * @param $paymentId
     * @return string
     */
    public function setPaymentId($paymentId);

    /**
     * @return string
     */
    public function getFullResponse();

    /**
     * @param $fullResponse
     * @return string
     */
    public function setFullResponse($fullResponse);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param $status
     * @return string
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getTransactionStatus();

    /**
     * @param $transactionStatus
     * @return string
     */
    public function setTransactionStatus($transactionStatus);

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