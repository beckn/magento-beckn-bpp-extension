<?php

namespace Beckn\Core\Controller\Adminhtml\Persons;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Edit
 * @package Beckn\Core\Controller\Adminhtml\Persons
 */
class Edit extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Beckn\Core\Model\PersonDetailsFactory
     */
    protected $_personDetailsFactory;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Beckn\Core\Model\PersonDetailsFactory $personDetailsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Beckn\Core\Model\PersonDetailsFactory $personDetailsFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_personDetailsFactory = $personDetailsFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute() {
        $rowId = (int) $this->getRequest()->getParam('id');
        /**
         * @var \Beckn\Core\Model\PersonDetails $personDetails
         */
        $personDetails = $this->_personDetailsFactory->create();
        $rowTitle = "";
        if ($rowId) {
            $personDetails = $personDetails->load($rowId);
            $rowTitle = $personDetails->getTitle();
            if (!$personDetails->getEntityId()) {
                $this->messageManager->addErrorMessage(__('Person No longer exist.'));
                return $this->_redirect('beckn/persons/index');
            }
        }

        $this->_coreRegistry->register('row_data', $personDetails);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Person Details') . $rowTitle : __('Add Person');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Beckn_Core::person_details');
    }

}
