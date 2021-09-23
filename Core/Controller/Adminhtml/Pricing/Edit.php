<?php

namespace Beckn\Core\Controller\Adminhtml\Pricing;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Edit
 * @author Indglobal
 * @package Beckn\Core\Controller\Adminhtml\Pricing
 */
class Edit extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Beckn\Core\Model\PricePolicyFactory
     */
    protected $_pricePolicyFactory;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Beckn\Core\Model\PricePolicyFactory $pricePolicyFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_pricePolicyFactory = $pricePolicyFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute() {
        $rowId = (int) $this->getRequest()->getParam('id');
        /**
         * @var \Beckn\Core\Model\PricePolicy $pricePolicy
         */
        $pricePolicy = $this->_pricePolicyFactory->create();
        $rowTitle = "";
        if ($rowId) {
            $pricePolicy = $pricePolicy->load($rowId);
            $rowTitle = $pricePolicy->getTitle();
            if (!$pricePolicy->getEntityId()) {
                $this->messageManager->addErrorMessage(__('Price policy no longer exist.'));
                return $this->_redirect('beckn/pricing/index');
            }
        }

        $this->_coreRegistry->register('row_data', $pricePolicy);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Dynamic Pricing Policy') . $rowTitle : __('Add Dynamic Pricing Policy');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Beckn_Core::pricing');
    }

}
