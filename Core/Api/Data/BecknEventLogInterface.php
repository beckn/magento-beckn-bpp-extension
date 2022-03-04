<?php

namespace Beckn\Core\Api\Data;

/**
 * Interface BecknEventLogInterface
 * @author Indglobal
 * @package Beckn\Core\Api\Data
 */
interface BecknEventLogInterface {
    const ENTITY_ID = "entity_id";
    const EVENT_TYPE = "event_type";
    const EVENT_NAME = "event_name";
    const TRANSACTION_ID = "transaction_id";
    const SUBSCRIBER_ID = "subscriber_id";
    const MESSAGE_ID = "message_id";
    const HEADER_AUTHORIZATION = "header_authorization";
    const PROXY_HEADER_AUTHORIZATION = "proxy_header_authorization";
    const EVENT_DATA = "event_data";
    const RESPONSE_DATA = "response_data";
    const ERROR_CODE = "error_code";
    const ACKNOWLEDGEMENT_STATUS = "acknowledgement_status";
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
     * @return string
     */
    public function getEventType();

    /**
     * @param $eventType
     * @return string
     */
    public function setEventType($eventType);

    /**
     * @return string
     */
    public function getEventName();

    /**
     * @param $eventName
     * @return string
     */
    public function setEventName($eventName);

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
     * @return string
     */
    public function getSubscriberId();

    /**
     * @param $subscriberId
     * @return string
     */
    public function setSubscriberId($subscriberId);

    /**
     * @return string
     */
    public function getMessageId();

    /**
     * @param $messageId
     * @return string
     */
    public function setMessageId($messageId);

    /**
     * @return string
     */
    public function getHeaderAuthorization();

    /**
     * @param $headerAuthorization
     * @return string
     */
    public function setHeaderAuthorization($headerAuthorization);

    /**
     * @return string
     */
    public function getProxyHeaderAuthorization();

    /**
     * @param $proxyHeaderAuthorization
     * @return string
     */
    public function setProxyHeaderAuthorization($proxyHeaderAuthorization);

    /**
     * @return string
     */
    public function getEventData();

    /**
     * @param $eventData
     * @return string
     */
    public function setEventData($eventData);

    /**
     * @return string
     */
    public function getResponseData();

    /**
     * @param $responseData
     * @return string
     */
    public function setResponseData($responseData);

    /**
     * @return string
     */
    public function getErrorCode();

    /**
     * @param $errorCode
     * @return string
     */
    public function setErrorCode($errorCode);

    /**
     * @return string
     */
    public function getAcknowledgementStatus();

    /**
     * @param $acknowledgementStatus
     * @return string
     */
    public function setAcknowledgementStatus($acknowledgementStatus);

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