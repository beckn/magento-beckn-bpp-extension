<?php

namespace Beckn\Core\Api\Data;

/**
 * Interface LocationPolicyInterface
 * @author Indglobal
 * @package Beckn\Core\Api\Data
 */
interface LocationPolicyInterface {

    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ENTITY_ID = "entity_id";
    const NAME = "name";
    const API_URL = "api_url";
    const METHOD = "method";
    const API_KEY = "api_key";
    const HEADERS = "headers";
    const RESPONSE_BODY_PATH = "response_body_path";
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
    public function getName();

    /**
     * @param $name
     * @return string
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getApiUrl();

    /**
     * @param $apiUrl
     * @return string
     */
    public function setApiUrl($apiUrl);

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param $method
     * @return string
     */
    public function setMethod($method);

    /**
     * @return string
     */
    public function getApiKey();

    /**
     * @param $apiKey
     * @return string
     */
    public function setApiKey($apiKey);

    /**
     * @return string
     */
    public function getHeaders();

    /**
     * @param $headers
     * @return string
     */
    public function setHeaders($headers);

    /**
     * @return string
     */
    public function getResponseBodyPath();

    /**
     * @param $responseBodyPath
     * @return string
     */
    public function setResponseBodyPath($responseBodyPath);

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