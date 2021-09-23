<?php

namespace Beckn\Core\Api\Data;

/**
 * Interface FulfillmentStatusInterface
 * @author Indglobal
 * @package Beckn\Core\Api\Data
 */
interface FulfillmentStatusInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ENTITY_ID = "entity_id";
    const LOCATION_ID = "location_id";
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
    public function getLocationId();

    /**
     * @param $locationId
     * @return int
     */
    public function setLocationId($locationId);

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