<?php

namespace Beckn\Core\Block\Adminhtml\Fulfillment;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class Policy
 * @author Indglobal
 * @package Beckn\Core\Block\Adminhtml\Fulfillment
 */
class Policy extends \Magento\Backend\Block\Template {

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;


    /**
     * Policy constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     * @param \Beckn\Core\Model\ResourceModel\FulfillmentPolicy\CollectionFactory $fulfillmentPolicyCollectionFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null,
        \Beckn\Core\Model\ResourceModel\FulfillmentPolicy\CollectionFactory $fulfillmentPolicyCollectionFactory
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_fulfillmentPolicyCollectionFactory = $fulfillmentPolicyCollectionFactory;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    /**
     * @return \Beckn\Core\Model\FulfillmentPolicy
     */
    public function getFulfillmentPolicy(){
        return $this->_coreRegistry->registry("row_data");
    }

    /**
     * @return string
     */
    public function getFormAction(){
        return $this->_urlBuilder->getUrl("beckn/fulfillment/save");
    }


    /**
     * @return array
     */
    public function getLocationType(){
        $type = [];
        $type[0] =  ['value' => 'circle', 'label' => 'Circle'];
        return $type;
    }
}