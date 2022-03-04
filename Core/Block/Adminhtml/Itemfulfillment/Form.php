<?php

namespace Beckn\Core\Block\Adminhtml\Itemfulfillment;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Beckn\Core\Helper\Data as Helper;
use Beckn\Core\Model\ResourceModel\PersonDetails\CollectionFactory;
use Beckn\Core\Model\PersonDetailsFactory;
use Beckn\Core\Model\ItemFulfillmentTimesFactory;

/**
 * Class Form
 * @package Beckn\Core\Block\Adminhtml\Persons
 */
class Form extends \Magento\Backend\Block\Template {

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var
     */
    protected $_helper;
    protected $_personsCollection;
    protected $_personDetailsFactory;
    protected $_itemFulfillmentTimesFactory;


    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        Helper $helper,
        CollectionFactory $collectionFactory,
        PersonDetailsFactory $personDetailsFactory,
        ItemFulfillmentTimesFactory $itemFulfillmentTimesFactory,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_helper = $helper;
        $this->_personsCollection = $collectionFactory;
        $this->_personDetailsFactory = $personDetailsFactory;
        $this->_itemFulfillmentTimesFactory = $itemFulfillmentTimesFactory;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    /**
     * @return \Beckn\Core\Model\ItemFulfillmentOptions
     */
    public function getItemFulfillmentData(){
        return $this->_coreRegistry->registry("row_data");
    }

    /**
     * @return string
     */
    public function getFormAction(){
        return $this->_urlBuilder->getUrl("beckn/Itemfulfillment/save");
    }

    /**
     * @return array|array[]
     */
    public function getValueType(){
        return $this->_valueType->toOptionArray();
    }

    public function getItemFulfillmentTimes($entityId){
        return $this->_itemFulfillmentTimesFactory->Create()->getCollection()->addFieldToFilter('fulfillment_option_id', $entityId);
    }

    /**
     * @return array
     */
    public function getFulfillmentType(){
        $fulfillmentType = $this->_helper->getConfigData('fulfillment_config/itemfulfillment/typeoption');
        return json_decode($fulfillmentType);
    }

    /**
     * @return mixed
     */
    public function getAllPersons(){
        return $this->_personsCollection->create()->getData();
    }




}