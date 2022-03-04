<?php

namespace Beckn\Core\Model;

use Beckn\Core\Api\Data\ItemFulfillmentOptionsInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Beckn\Core\Setup\UpgradeSchema;
use Beckn\Core\Model\ResourceModel\ItemFulfillmentOptions as ResourceModelItemFulfillmentOptions;

/**
 * Class ItemFulfillmentOptions
 * @package Beckn\Core\Model
 */
class ItemFulfillmentOptions extends \Magento\Framework\Model\AbstractModel implements ItemFulfillmentOptionsInterface, IdentityInterface
{

    const CACHE_TAG = UpgradeSchema::TABLE_ITEM_FULFILLMENT_OPTIONS;

    protected $_cacheTag = UpgradeSchema::TABLE_ITEM_FULFILLMENT_OPTIONS;

    protected $_eventPrefix = UpgradeSchema::TABLE_ITEM_FULFILLMENT_OPTIONS;

    protected function _construct(){
        $this->_init(ResourceModelItemFulfillmentOptions::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */

    public function getEntityId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * @param int $entityId
     * @return LocationPolicy|int
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return parent::getData(self::NAME);
    }

    /**
     * @param $name
     * @return string
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return array|mixed|null
     */
    public function getFulfillmentType()
    {
        return parent::getData(self::FULFILLMENT_TYPE);
    }

    /**
     * @param $gender
     * @return PersonDetails|mixed
     */
    public function setFulfillmentType($fulfillmentType)
    {
        return $this->setData(self::FULFILLMENT_TYPE, $fulfillmentType);
    }

    /**
     * @return array|mixed|null
     */
    public function getFulfillmentPerson()
    {
        return parent::getData(self::FULFILLMENT_PERSON);
    }

    /**
     * @param $image
     * @return PersonDetails|mixed
     */
    public function setFulfillmentPerson($fulfillmentPerson)
    {
        return $this->setData(self::FULFILLMENT_PERSON, $fulfillmentPerson);
    }

    /**
     * @return array|mixed|null
     */
    public function getFulfillmentLocation()
    {
        return parent::getData(self::FULFILLMENT_LOCATION);
    }

    /**
     * @param $cred
     * @return PersonDetails|mixed
     */
    public function setFulfillmentLocation($fulfillmentLocation)
    {
        return $this->setData(self::FULFILLMENT_LOCATION, $fulfillmentLocation);
    }

    /**
     * @return array|mixed|null
     */
    public function getGps()
    {
        return parent::getData(self::GPS);
    }

    /**
     * @param $gps
     * @return ItemFulfillmentOptions|mixed
     */
    public function setGps($gps)
    {
        return $this->setData(self::GPS, $gps);
    }

    /**
     * @return array|mixed|null
     */
    public function getLocationName(){
        return parent::getData(self::LOCATION_NAME);
    }

    /**
     * @param $locationName
     * @return ItemFulfillmentOptions|mixed
     */
    public function setLocationName($locationName)
    {
        return $this->setData(self::LOCATION_NAME, $locationName);
    }

    /**
     * @return array|mixed|null
     */
    public function getBuilding(){
        return parent::getData(self::BUILDING);
    }

    /**
     * @param $building
     * @return ItemFulfillmentOptions|mixed
     */
    public function setBuilding($building){
        return $this->setData(self::BUILDING, $building);
    }

    /**
     * @return array|mixed|null
     */
    public function getStreet(){
        return parent::getData(self::STREET);
    }

    /**
     * @param $street
     * @return ItemFulfillmentOptions|mixed
     */
    public function setStreet($street){
        return $this->setData(self::STREET, $street);
    }

    /**
     * @return array|mixed|null
     */
    public function getLocality(){
        return parent::getData(self::LOCALITY);
    }

    /**
     * @param $locality
     * @return ItemFulfillmentOptions|mixed
     */
    public function setLocality($locality){
        return $this->setData(self::LOCALITY, $locality);
    }

    /**
     * @return array|mixed|null
     */
    public function getWard(){
        return parent::getData(self::WARD);
    }

    /**
     * @param $ward
     * @return ItemFulfillmentOptions|mixed
     */
    public function setWard($ward){
        return $this->setData(self::WARD, $ward);
    }

    /**
     * @return array|mixed|null
     */
    public function getCity(){
        return parent::getData(self::CITY);
    }

    /**
     * @param $city
     * @return ItemFulfillmentOptions|mixed
     */
    public function setCity($city){
        return $this->setData(self::WARD, $city);
    }

    /**
     * @return array|mixed|null
     */
    public function getState(){
        return parent::getData(self::STATE);
    }

    /**
     * @param $state
     * @return ItemFulfillmentOptions|mixed
     */
    public function setState($state){
        return $this->setData(self::STATE, $state);
    }

    /**
     * @return array|mixed|null
     */
    public function getCountry(){
        return parent::getData(self::COUNTRY);
    }

    /**
     * @param $country
     * @return ItemFulfillmentOptions|mixed
     */
    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }

    /**
     * @return array|mixed|null
     */
    public function getAreaCode(){
        return parent::getData(self::AREA_CODE);
    }

    /**
     * @param $areaCode
     * @return ItemFulfillmentOptions|mixed
     */
    public function setAreaCode($areaCode){
        return $this->setData(self::AREA_CODE, $areaCode);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * @param $createdAt
     * @return string
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * @param $updatedAt
     * @return string
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }


}