<?php

namespace Beckn\Core\Controller\Adminhtml\Location;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Edit
 * @author Indglobal
 * @package Beckn\Core\Controller\Adminhtml\Location
 */
class Edit extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Beckn\Core\Model\LocationPolicyFactory
     */
    protected $_locationPolicyFactory;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Beckn\Core\Model\LocationPolicyFactory $locationPolicyFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Beckn\Core\Model\LocationPolicyFactory $locationPolicyFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_locationPolicyFactory = $locationPolicyFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute() {
        $rowId = (int) $this->getRequest()->getParam('id');
        /**
         * @var \Beckn\Core\Model\LocationPolicy $locationPolicy
         */
        $locationPolicy = $this->_locationPolicyFactory->create();
        $rowTitle = "";
        if ($rowId) {
            $locationPolicy = $locationPolicy->load($rowId);
            $rowTitle = $locationPolicy->getTitle();
            if (!$locationPolicy->getEntityId()) {
                $this->messageManager->addErrorMessage(__('Location policy no longer exist.'));
                return $this->_redirect('beckn/location/index');
            }
        }

        $this->_coreRegistry->register('row_data', $locationPolicy);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Dynamic Location Policy') . $rowTitle : __('Add Dynamic Location Policy');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Beckn_Core::location');
    }

}
