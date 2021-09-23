<?php

namespace Beckn\Core\Api\Data;

/**
 * Interface BecknLookupInterface
 * @author Indglobal
 * @package Beckn\Core\Api\Data
 */
interface BecknLookupInterface {
    const ENTITY_ID = "entity_id";
    const SUBSCRIBER_ID = "subscriber_id";
    const SUBSCRIBER_URL = "subscriber_url";
    const TYPE = "type";
    const DOMAIN = "domain";
    const CITY = "city";
    const COUNTRY = "country";
    const SIGNING_PUBLIC_KEY = "signing_public_key";
    const ENCR_PUBLIC_KEY = "encr_public_key";
    const VALID_FROM = "valid_from";
    const VALID_UNTIL = "valid_until";
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
    public function getSubscriberUrl();

    /**
     * @param $subscriberUrl
     * @return string
     */
    public function setSubscriberUrl($subscriberUrl);

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
    public function getDomain();

    /**
     * @param $domain
     * @return string
     */
    public function setDomain($domain);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param $city
     * @return string
     */
    public function setCity($city);

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @param $country
     * @return string
     */
    public function setCountry($country);

    /**
     * @return string
     */
    public function getSigningPublicKey();

    /**
     * @param $signingPublicKey
     * @return string
     */
    public function setSigningPublicKey($signingPublicKey);

    /**
     * @return string
     */
    public function getEncrPublicKey();

    /**
     * @param $encrPublicKey
     * @return string
     */
    public function setEncrPublicKey($encrPublicKey);

    /**
     * @return string
     */
    public function getValidFrom();

    /**
     * @param $validFrom
     * @return string
     */
    public function setValidFrom($validFrom);

    /**
     * @return string
     */
    public function getValidUntil();

    /**
     * @param $validUntil
     * @return string
     */
    public function setValidUntil($validUntil);


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