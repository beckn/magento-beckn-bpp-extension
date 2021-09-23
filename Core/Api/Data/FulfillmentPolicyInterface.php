<?php

namespace Beckn\Core\Api\Data;

/**
 * Interface FulfillmentPolicyInterface
 * @author Indglobal
 * @package Beckn\Core\Api\Data
 */
interface FulfillmentPolicyInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ENTITY_ID = "entity_id";
    const NAME = "name";
    const TYPE = "type";
    const CENTER = "center";
    const RADIUS = "radius";
    const AGENTS = "agents";
    const VEHICLES = "vehicles";
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
    public function getType();

    /**
     * @param $type
     * @return string
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getCenter();

    /**
     * @param $center
     * @return string
     */
    public function setCenter($center);

    /**
     * @return string
     */
    public function getRadius();

    /**
     * @param $radius
     * @return string
     */
    public function setRadius($radius);

    /**
     * @return string
     */
    public function getAgents();

    /**
     * @param $agents
     * @return string
     */
    public function setAgents($agents);

    /**
     * @return string
     */
    public function getVehicles();

    /**
     * @param $vehicles
     * @return string
     */
    public function setVehicles($vehicles);

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