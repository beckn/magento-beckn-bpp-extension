<?php

namespace Beckn\Core\Block\Adminhtml\Order\View;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Beckn\Core\Helper\Data as Helper;

class FulfillmentStatus extends \Magento\Backend\Block\Template
{
    /**
     * @var Helper
     */
    protected $_helper;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * FulfillmentStatus constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Helper $helper
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Helper $helper,
        \Magento\Framework\Registry $registry,
        array $data = [], ?JsonHelper $jsonHelper = null, ?DirectoryHelper $directoryHelper = null
    )
    {
        $this->_helper = $helper;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    /**
     * @return array
     */
    public function getAllFulfillmentStatus()
    {
        $fulfillment = $this->_helper->getConfigData(Helper::XML_PATH_FULFILLMENT_STATUS);
        $fulfillmentData = json_decode($fulfillment, true);
        $fulfillmentStatus = [];
//        $fulfillmentStatus[] = [
//            "status_code" => "",
//            "status_message" => __("Select Status"),
//            "parent_status" => "",
//        ];
        if(!empty($fulfillmentData)){
            foreach ($fulfillmentData as $item){
                $fulfillmentStatus[] = [
                    "status_code" => $item['status_code'],
                    "status_message" => $item['status_message'],
                    "parent_status" => $item['parent_status'],
                ];
            }
        }
        return $fulfillmentStatus;
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('beckn/fulfillmentstatus/save', ['_secure' => true]);
    }
}