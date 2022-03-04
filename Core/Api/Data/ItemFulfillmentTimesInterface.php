<?php

namespace Beckn\Core\Api\Data;

interface ItemFulfillmentTimesInterface{
    const ENTITY_ID = "entity_id";
    const FULFILLMENT_OPTION_ID = "fulfillment_option_id";
    const START_TIME = "start_time";
    const END_TIME = "end_time";
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
     * @return integer
     */
    public function getFulfillmentOptionId();

    /**
     * @param $fulfillmentOptionId
     * @return integer
     */
    public function setFulfillmentOptionId($fulfillmentOptionId);

    /**
     * @return string
     */
    public function getStartTime();

    /**
     * @param $startTime
     * @return string
     */
    public function setStartTime($startTime);

    /**
     * @return string
     */
    public function getEndTime();

    /**
     * @param $endTime
     * @return string
     */
    public function setEndTime($endTime);

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