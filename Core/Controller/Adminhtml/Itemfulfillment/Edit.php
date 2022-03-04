<?php

namespace Beckn\Core\Controller\Adminhtml\Itemfulfillment;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Edit
 * @package Beckn\Core\Controller\Adminhtml\Itemfulfillment
 */
class Edit extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Beckn\Core\Model\ItemFulfillmentOptions
     */
    protected $_itemFulfillmentOptions;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Beckn\Core\Model\ItemFulfillmentOptionsFactory $itemFulfillmentOptions
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Beckn\Core\Model\ItemFulfillmentOptionsFactory $itemFulfillmentOptions
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_itemFulfillmentOptions = $itemFulfillmentOptions;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute() {
        $rowId = (int) $this->getRequest()->getParam('id');
        /**
         * @var \Beckn\Core\Model\ItemFulfillmentOptions $itemFulfillmentOptions
         */
        $itemFulfillmentOptions = $this->_itemFulfillmentOptions->create();
        $rowTitle = "";
        if ($rowId) {
            $itemFulfillmentOptions = $itemFulfillmentOptions->load($rowId);
            $rowTitle = $itemFulfillmentOptions->getTitle();
            if (!$itemFulfillmentOptions->getEntityId()) {
                $this->messageManager->addErrorMessage(__('Person No longer exist.'));
                return $this->_redirect('beckn/persons/index');
            }
        }

        $this->_coreRegistry->register('row_data', $itemFulfillmentOptions);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Item Fulfillment Option') . $rowTitle : __('Add Item Fulfillment Option');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Beckn_Core::item_fulfillment_options');
    }

}
