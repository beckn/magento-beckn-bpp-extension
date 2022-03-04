<?php

namespace Beckn\Core\Api\Data;

interface ItemFulfillmentOptionsInterface{
    const ENTITY_ID = "entity_id";
    const NAME = "name";
    const FULFILLMENT_TYPE = "fulfillment_type";
    const FULFILLMENT_PERSON = "fulfillment_person";
    const FULFILLMENT_LOCATION = "fulfillment_location";
    const GPS = "gps";
    const LOCATION_NAME = "location_name";
    const BUILDING = "building";
    const STREET = "street";
    const LOCALITY = "locality";
    const WARD = "ward";
    const CITY = "city";
    const STATE = "state";
    const COUNTRY = "country";
    const AREA_CODE = "area_code";
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
    public function getFulfillmentType();

    /**
     * @param $fulfillmentType
     * @return mixed
     */
    public function setFulfillmentType($fulfillmentType);

    /**
     * @return mixed
     */
    public function getFulfillmentPerson();

    /**
     * @param $fulfillmentPerson
     * @return mixed
     */
    public function setFulfillmentPerson($fulfillmentPerson);

    /**
     * @return mixed
     */
    public function getFulfillmentLocation();

    /**
     * @param $fulfillmentLocation
     * @return mixed
     */
    public function setFulfillmentLocation($fulfillmentLocation);

    /**
     * @return mixed
     */
    public function getGps();

    /**
     * @param $cred
     * @return mixed
     */
    public function setGps($fulfillmentLocation);

    /**
     * @return mixed
     */
    public function getLocationName();

    /**
     * @param $locationName
     * @return mixed
     */
    public function setLocationName($locationName);

    /**
     * @return mixed
     */
    public function getBuilding();

    /**
     * @param $building
     * @return mixed
     */
    public function setBuilding($building);

    /**
     * @return mixed
     */
    public function getStreet();

    /**
     * @param $street
     * @return mixed
     */
    public function setStreet($street);

    /**
     * @return mixed
     */
    public function getLocality();

    /**
     * @param $locality
     * @return mixed
     */
    public function setLocality($locality);

    /**
     * @return mixed
     */
    public function getWard();

    /**
     * @param $ward
     * @return mixed
     */
    public function setWard($ward);

    /**
     * @return mixed
     */
    public function getCity();

    /**
     * @param $city
     * @return mixed
     */
    public function setCity($city);

    /**
     * @return mixed
     */
    public function getState();

    /**
     * @param $state
     * @return mixed
     */
    public function setState($state);

    /**
     * @return mixed
     */
    public function getCountry();

    /**
     * @param $country
     * @return mixed
     */
    public function setCountry($country);

    /**
     * @return mixed
     */
    public function getAreaCode();

    /**
     * @param $areaCode
     * @return mixed
     */
    public function setAreaCode($areaCode);

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