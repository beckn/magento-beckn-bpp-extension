<?php

namespace Beckn\Core\Controller\Adminhtml\Fulfillment;

use Magento\Framework\Controller\ResultFactory;
/**
 * Class Edit
 * @author Indglobal
 * @package Beckn\Core\Controller\Adminhtml\Fulfillment
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Beckn\Core\Model\FulfillmentPolicyFactory
     */
    protected $_fulfillmentPolicy;


    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Beckn\Core\Model\FulfillmentPolicyFactory $fulfillmentPolicyFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Beckn\Core\Model\FulfillmentPolicyFactory $fulfillmentPolicyFactory
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_fulfillmentPolicy = $fulfillmentPolicyFactory;
        parent::__construct($context);
    }

    public function execute(){
        $rowId = (int) $this->getRequest()->getParam('id');

        /**
         * @var \Beckn\Core\Model\FulfillmentPolicy $fulfillmentPolicy
         */
        $fulfillmentPolicy = $this->_fulfillmentPolicy->create();
        $rowTitle = "";
        if ($rowId) {
            $fulfillmentPolicy = $fulfillmentPolicy->load($rowId);
            $rowTitle = $fulfillmentPolicy->getTitle();
            if (!$fulfillmentPolicy->getEntityId()) {
                $this->messageManager->addErrorMessage(__('Fulfillment policy no longer exist.'));
                return $this->_redirect('beckn/fulfillment/index');
            }
        }
        $this->_coreRegistry->register('row_data', $fulfillmentPolicy);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Fulfillment Policy') . $rowTitle : __('Add Fulfillment Policy');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}