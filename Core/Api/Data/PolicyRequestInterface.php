<?php

namespace Beckn\Core\Api\Data;

/**
 * Interface PolicyRequestInterface
 * @author Indglobal
 * @package Beckn\Core\Api\Data
 */
interface PolicyRequestInterface
{
    const ENTITY_ID = "entity_id";
    const POLICY_ID = "policy_id";
    const POLICY_TYPE = "policy_type";
    const REQUEST_TYPE = "request_type";
    const KEY = "key";
    const VALUE = "value";
    const VALUE_TYPE = "value_type";
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
    public function getPolicyId();

    /**
     * @param $policyId
     * @return int
     */
    public function setPolicyId($policyId);

    /**
     * @return string
     */
    public function getPolicyType();

    /**
     * @param $policyType
     * @return string
     */
    public function setPolicyType($policyType);

    /**
     * @return string
     */
    public function getRequestType();

    /**
     * @param $requestType
     * @return string
     */
    public function setRequestType($requestType);

    /**
     * @return string
     */
    public function getKey();

    /**
     * @param $key
     * @return string
     */
    public function setKey($key);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param $value
     * @return string
     */
    public function setValue($value);

    /**
     * @return string
     */
    public function getValueType();

    /**
     * @param $valueType
     * @return string
     */
    public function setValueType($valueType);

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