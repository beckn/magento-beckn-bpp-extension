<?php

namespace Beckn\Core\Api\Data;

interface PersonDetailsInterface{
    const ENTITY_ID = "entity_id";
    const NAME = "name";
    const GENDER = "gender";
    const IMAGE = "image";
    const CRED = "cred";
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
     * @return mixed
     */
    public function getGender();

    /**
     * @param $gender
     * @return mixed
     */
    public function setGender($gender);

    /**
     * @return mixed
     */
    public function getImage();

    /**
     * @param $image
     * @return mixed
     */
    public function setImage($image);

    /**
     * @return mixed
     */
    public function getCred();

    /**
     * @param $cred
     * @return mixed
     */
    public function setCred($cred);

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